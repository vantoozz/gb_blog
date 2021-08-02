<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Comments;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\UUID;
use PDO;
use PDOException;

/**
 * Class SqliteCommentsRepository
 * @package GeekBrains\Blog\Repositories\Comments
 */
final class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    /**
     * SqliteCommentsRepositoryInterface constructor.
     * @param PDO $pdo
     */
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * @param Comment $comment
     * @throws CommentsRepositoryException
     */
    public function save(Comment $comment): void
    {
        $query = <<<'SQL'
            INSERT INTO comments (
                uuid, 
                author_uuid,
                parent_uuid, 
                text,
                created_at, 
                updated_at
            ) VALUES (
                :uuid, 
                :author_uuid,
                :parent_uuid, 
                :text,
                datetime('now'), 
                datetime('now')
            )
            ON CONFLICT (uuid) DO UPDATE SET 
                author_uuid = :author_uuid, 
                parent_uuid = :parent_uuid,
                text = :text,
                updated_at = datetime('now')
SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                ':uuid' => (string)$comment->uuid(),
                ':author_uuid' => (string)$comment->authorUuid(),
                ':parent_uuid' => (string)$comment->parentUuid(),
                ':text' => $comment->text(),
            ]);
        } catch (PDOException $e) {
            throw new CommentsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function get(UUID $uuid): Comment
    {
        try {
            $statement = $this->pdo->prepare('SELECT * FROM comments WHERE uuid = ?');
            $statement->execute([(string)$uuid]);
            /** @var array|false $result */
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new CommentsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        if (false === $result) {
            throw new CommentNotFoundException("Cannot find comment by uuid: $uuid");
        }

        $data = $result[0];

        return $this->makeComment($data);
    }

    /**
     * @param array $data
     * @return Comment
     * @throws CommentsRepositoryException
     */
    private function makeComment(array $data): Comment
    {
        try {
            return new Comment(
                new UUID($data['uuid']),
                new UUID($data['parent_uuid']),
                new UUID($data['author_uuid']),
                $data['text']
            );
        } catch (InvalidArgumentException $e) {
            throw new CommentsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UUID $uuid
     * @return array
     * @throws CommentsRepositoryException
     */
    public function getChildren(UUID $uuid): array
    {
        $query = <<< 'SQL'
            WITH RECURSIVE comments_tree(uuid, parent_uuid, author_uuid, text)
                AS (
                    SELECT uuid, parent_uuid, author_uuid, text
                    FROM comments
                    WHERE parent_uuid = ?
                    UNION
                    SELECT c.uuid, c.parent_uuid, c.author_uuid, c.text
                    FROM comments c 
                        JOIN comments_tree t ON c.parent_uuid = t.uuid
                )
            SELECT * FROM comments_tree
SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([(string)$uuid]);
            /** @var array|false $result */
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new CommentsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        return array_map(
            fn(array $row) => $this->makeComment($row),
            $result
        );
    }
}
