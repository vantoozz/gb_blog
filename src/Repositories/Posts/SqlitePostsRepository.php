<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Posts;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\UUID;

/**
 * Class SqlitePostsRepository
 * @package GeekBrains\Blog\Repositories\Posts
 */
final class SqlitePostsRepository implements PostsRepositoryInterface
{
    /**
     * SqlitePostsRepository constructor.
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @param Post $post
     * @throws PostsRepositoryException
     */
    public function save(Post $post): void
    {
        $query = <<<'SQL'
            INSERT INTO posts (
                uuid, 
                author_uuid,
                title, 
                text,
                created_at, 
                updated_at
            ) VALUES (
                :uuid, 
                :author_uuid,
                :title, 
                :text,
                datetime('now'), 
                datetime('now')
            )
            ON CONFLICT (uuid) DO UPDATE SET 
                author_uuid = :author_uuid, 
                title = :title,
                text = :text,
                updated_at = datetime('now')
SQL;
        try {
            $this->connection->executeQuery(
                $query,
                [
                    'uuid' => (string)$post->uuid(),
                    'author_uuid' => $post->authorUuid(),
                    'title' => $post->title(),
                    'text' => $post->text(),
                ]
            );
        } catch (DbalException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UUID $authorUuid
     * @return Post[]
     * @throws PostsRepositoryException
     */
    public function getByAuthor(UUID $authorUuid): array
    {
        try {
            /** @var array $result */
            $result = $this->connection->executeQuery(
                'SELECT * FROM posts WHERE author_uuid = :author_uuid',
                ['author_uuid' => (string)$authorUuid]
            )->fetchAllAssociative();
        } catch (DbalException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return array_map(
            fn(array $row) => $this->makePost($row),
            $result
        );
    }

    /**
     * @param array $data
     * @return Post
     */
    private function makePost(array $data): Post
    {
        return new Post(
            new UUID($data['uuid']),
            new UUID($data['author_uuid']),
            $data['title'],
            $data['text'],
        );
    }

    /**
     * @param UUID $uuid
     */
    public function delete(UUID $uuid): void
    {
        try {
            $this->connection->executeStatement(
                'DELETE FROM posts WHERE uuid = :uuid',
                ['uuid' => (string)$uuid]
            );
        } catch (DbalException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UUID $uuid
     * @return Post
     * @throws PostNotFoundException
     * @throws PostsRepositoryException
     */
    public function get(UUID $uuid): Post
    {
        try {
            /** @var array $result */
            $result = $this->connection->executeQuery(
                'SELECT * FROM posts WHERE uuid = :uuid',
                ['uuid' => $uuid]
            )->fetchAllAssociative();
        } catch (DbalException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        if (count($result) !== 1) {
            throw new PostNotFoundException("Cannot find post by uuid: $uuid");
        }

        return $this->makePost($result[0]);
    }
}
