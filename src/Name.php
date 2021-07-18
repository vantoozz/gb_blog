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
     * @param string $last
     */
    public function __construct(
        private string $first,
        private string $last
    ) {
    }

    /**
     * @return string
     */
    public function first(): string
    {
        return $this->first;
    }

    /**
     * @return string
     */
    public function last(): string
    {
        return $this->last;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->first . ' ' . $this->last;
    }
}
