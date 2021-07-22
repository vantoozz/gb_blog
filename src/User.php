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
     * @param UUID $uuid
     * @param Name $name
     * @param Credentials $credentials
     */
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private Credentials $credentials
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
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return Credentials
     */
    public function credentials(): Credentials
    {
        return $this->credentials;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
