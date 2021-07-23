<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;

/**
 * Class UUID
 * @package GeekBrains\Blog
 */
final class UUID
{
    private const UUID_REGEXP = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

    /**
     * UUID constructor.
     * @param string $uuidString
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $uuidString
    ) {
        if (!preg_match(self::UUID_REGEXP, $this->uuidString)) {
            throw new InvalidArgumentException("Malformed UUID: $this->uuidString");
        }
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
