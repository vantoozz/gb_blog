<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Comment
 * @package GeekBrains\Blog
 */
final class Comment
{

    /**
     * @param UUID $uuid
     * @param UUID $parentUUID
     * @param UUID $authorUuid
     * @param string $text
     */
    public function __construct(
        private UUID $uuid,
        private UUID $parentUUID,
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

    /**
     * @return UUID
     */
    public function parentUuid(): UUID
    {
        return $this->parentUUID;
    }

    /**
     * @return UUID
     */
    public function authorUuid(): UUID
    {
        return $this->authorUuid;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
