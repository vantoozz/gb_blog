<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Comments;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\UUID;

/**
 * Interface CommentsRepositoryInterface
 * @package GeekBrains\Blog\Repositories\Comments
 */
interface CommentsRepositoryInterface
{
    /**
     * @param Comment $comment
     * @throws CommentsRepositoryException
     */
    public function save(Comment $comment): void;


    /**
     * @param UUID $uuid
     * @return Comment
     * @throws CommentNotFoundException
     * @throws CommentsRepositoryException
     */
    public function get(UUID $uuid): Comment;
}
