<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Comment
 * @package GeekBrains\Blog
 */
final class Comment
{
    /**
     * Comment constructor.
     * @param UUID $uuid
     * @param UUID $commentableUUID
     * @param UUID $authorUuid
     * @param string $text
     */
    public function __construct(
        private UUID $uuid,
        private UUID $commentableUUID,
        private UUID $authorUuid,
        private string $text,
    ) {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }
}
