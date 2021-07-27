<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Comments;

use GeekBrains\Blog\Comment;

/**
 * Interface CommentsRepositoryInterface
 * @package GeekBrains\Blog\Repositories\Comments
 */
interface CommentsRepositoryInterface
{
    /**
     * @param Comment $comment
     */
    public function save(Comment $comment): void;
}
