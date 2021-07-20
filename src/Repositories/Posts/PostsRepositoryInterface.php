<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Posts;

use GeekBrains\Blog\Post;

/**
 * Interface PostsRepositoryInterface
 * @package GeekBrains\Blog\Repositories\Posts
 */
interface PostsRepositoryInterface
{
    /**
     * @param Post $post
     */
    public function save(Post $post): void;
}
