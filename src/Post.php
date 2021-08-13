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
     * @param int $id
     * @param int $authorId
     * @param string $title
     * @param string $text
     */
    public function __construct(
        private int $id,
        private int $authorId,
        private string $title,
        private string $text,
    ) {
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function authorId(): int
    {
        return $this->authorId;
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title . ' >>> ' . $this->text;
    }
}
