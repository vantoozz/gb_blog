<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Posts;

use GeekBrains\Blog\Post;
use GeekBrains\Blog\UUID;

/**
 * Interface PostsRepositoryInterface
 * @package GeekBrains\Blog\Repositories\Posts
 */
interface PostsRepositoryInterface
{
    /**
     * @param Post $post
     * @throws PostsRepositoryException
     */
    public function save(Post $post): void;

    /**
     * @param UUID $uuid
     * @return Post
     * @throws PostNotFoundException
     * @throws PostsRepositoryException
     */
    public function get(UUID $uuid): Post;

    /**
     * @param UUID $uuid
     * @throws PostsRepositoryException
     */
    public function delete(UUID $uuid): void;

    /**
     * @param UUID $authorUuid
     * @return Post[]
     * @throws PostsRepositoryException
     */
    public function getByAuthor(UUID $authorUuid): array;
}
