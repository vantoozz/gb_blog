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
     * @param int $id
     * @param Name $name
     */
    public function __construct(
        private int $id,
        private Name $name
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
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
