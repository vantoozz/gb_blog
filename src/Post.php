<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Post
 * @package GeekBrains\Blog
 */
final class Post
{
    /**
     * Post constructor.
     * @param UUID $uuid
     * @param UUID $authorUuid
     * @param string $title
     * @param string $text
     */
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private string $title,
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
    public function authorUuid(): UUID
    {
        return $this->authorUuid;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
