<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class CommentId
 * @package GeekBrains\Blog
 */
final class CommentId
{
    /**
     * CommentId constructor.
     * @param UUID $parentUUID
     * @param UUID $uuid
     */
    public function __construct(
        private UUID $parentUUID,
        private UUID $uuid
    ) {
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function parentUuid(): UUID
    {
        return $this->parentUUID;
    }

}
