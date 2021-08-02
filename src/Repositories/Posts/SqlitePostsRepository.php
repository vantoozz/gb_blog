<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\UUID;
use PDO;
use PDOException;

/**
 * Class SqlitePostsRepository
 * @package GeekBrains\Blog\Repositories\Posts
 */
final class SqlitePostsRepository implements PostsRepositoryInterface
{
    /**
     * SqlitePostsRepository constructor.
     * @param PDO $pdo
     */
    public function __construct(
        private PDO $pdo
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
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                ':uuid' => (string)$post->uuid(),
                ':author_uuid' => $post->authorUuid(),
                ':title' => $post->title(),
                ':text' => $post->text(),
            ]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
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
            $statement = $this->pdo->prepare('SELECT * FROM posts WHERE author_uuid = ?');
            $statement->execute([(string)$authorUuid]);
            /** @var array|false $result */
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PostsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        try {
            return array_map(
                fn(array $row) => $this->makePost($row),
                $result
            );
        } catch (InvalidArgumentException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $data
     * @return Post
     * @throws InvalidArgumentException
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
     * @throws PostsRepositoryException
     */
    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->pdo->prepare('DELETE FROM posts WHERE uuid = ?');
            $statement->execute([(string)$uuid]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
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
            $statement = $this->pdo->prepare('SELECT * FROM posts WHERE uuid = ?');
            $statement->execute([(string)$uuid]);
            /** @var array|false $result */
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PostsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        if (false === $result) {
            throw new PostNotFoundException("Cannot find post by uuid: $uuid");
        }

        try {
            return $this->makePost($result);
        } catch (InvalidArgumentException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
