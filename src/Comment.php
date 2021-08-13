<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Comment
 * @package GeekBrains\Blog
 */
final class Comment
{

    /**
     * @param int $id
     * @param int $parentId
     * @param int $authorId
     * @param string $text
     */
    public function __construct(
        private int $id,
        private int $parentId,
        private int $authorId,
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
    public function parentId(): int
    {
        return $this->parentId;
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
    public function text(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->text;
    }
}
