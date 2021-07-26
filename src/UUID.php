<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;

/**
 * Class UUID
 * @package GeekBrains\Blog
 */
final class UUID
{
    /**
     * UUID constructor.
     * @param string $uuidString
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $uuidString
    ) {
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException("Malformed UUID: $this->uuidString");
        }
    }

    /**
     * @return $this
     */
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    /**
     * @param UUID $other
     * @return bool
     */
    public function equals(UUID $other): bool
    {
        return (string)$this === (string)$other;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->uuidString;
    }
}
