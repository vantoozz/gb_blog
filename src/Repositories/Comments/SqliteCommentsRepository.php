<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Comments;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use GeekBrains\Blog\Comment;

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
}
