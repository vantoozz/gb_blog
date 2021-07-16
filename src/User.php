<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class User
 * @package GeekBrains\Blog
 */
final class User
{
    /**
     * User constructor.
     * @param Name $name
     */
    public function __construct(
        private Name $name
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
