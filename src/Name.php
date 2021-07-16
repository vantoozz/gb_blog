<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Name
 * @package GeekBrains\Blog
 */
final class Name
{
    /**
     * Name constructor.
     * @param string $first
     * @param string $second
     */
    public function __construct(
        private string $first,
        private string $second
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->first . ' ' . $this->second;
    }
}
