<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class User
 * @package GeekBrains\Blog
 */
final class User
{
    /**
     * User constructor.
     * @param Uuid $uuid
     * @param Name $name
     * @param Credentials $credentials
     */
    public function __construct(
        private UuidInterface $uuid,
        private Name $name,
        private Credentials $credentials
    ) {
    }

    /**
     * @return UuidInterface
     */
    public function uuid(): UuidInterface
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
