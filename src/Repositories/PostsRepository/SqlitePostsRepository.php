<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\PostsRepository;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\UUID;
use PDO;
use PDOException;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

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
            $statement = $this->connection->prepare($query);
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

    public function get(UUID $uuid): Post
    {
        try {
            $statement = $this->connection->prepare('SELECT * FROM posts WHERE uuid = ?');
            $statement->execute([(string)$uuid]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PostsRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        if (false === $result) {
            throw new PostNotFoundException("Cannot find post by uuid: $uuid");
        }

        try {
            return new Post(
                new UUID($result['uuid']),
                new UUID($result['author_uuid']),
                $result['title'],
                $result['text'],
            );
        } catch (InvalidArgumentException $e) {
            throw new PostsRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
