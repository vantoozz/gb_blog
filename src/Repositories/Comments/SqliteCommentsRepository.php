<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Comments;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\CommentId;
use GeekBrains\Blog\UUID;

/**
 * Class SqliteCommentsRepository
 * @package GeekBrains\Blog\Repositories\Comments
 */
final class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    /**
     * SqliteCommentsRepositoryInterface constructor.
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @param Comment $comment
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
            $this->connection->executeQuery(
                $query,
                [
                    'uuid' => (string)$comment->uuid(),
                    'author_uuid' => (string)$comment->authorUuid(),
                    'parent_uuid' => (string)$comment->parentUuid(),
                    'text' => $comment->text(),
                ]
            );
        } catch (DbalException $e) {
            throw new CommentsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function get(UUID $uuid): Comment
    {
        $query = <<<'SQL'
            SELECT 
                   c.uuid, 
                   c.author_uuid,
                   c.parent_uuid, 
                   c.text, 
                   p.uuid parent_post_uuid, 
                   c2.uuid parent_comment_uuid
            FROM comments c 
                LEFT JOIN posts p ON c.parent_uuid = p.uuid
                LEFT JOIN comments c2 ON c.parent_uuid = c2.uuid
                WHERE c.uuid = :uuid
SQL;

        try {
            /** @var array $result */
            $result = $this->connection
                ->executeQuery($query, ['uuid' => $uuid])
                ->fetchAllAssociative();
        } catch (DbalException $e) {
            throw new CommentsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        if (count($result) !== 1) {
            throw new CommentNotFoundException("Cannot find comment by uuid: $uuid");
        }

        $data = $result[0];

        return new Comment(
            $this->makeCommentId($data),
            new UUID($data['author_uuid']),
            $data['text']
        );
    }

    /**
     * @param array $data
     * @return CommentId
     * @throws CommentsRepositoryException
     */
    private function makeCommentId(array $data): CommentId
    {
        if (!empty($data['parent_post_uuid'])) {
            return CommentId::forPost(new UUID($data['parent_post_uuid']), new UUID($data['uuid']));
        }
        if (!empty($data['parent_comment_uuid'])) {
            return CommentId::forComment(new UUID($data['parent_comment_uuid']), new UUID($data['uuid']));
        }

        throw new CommentsRepositoryException("Cannot find a parent for comment ${data['uuid']}");
    }
}
