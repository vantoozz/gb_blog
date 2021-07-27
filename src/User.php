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
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return $this->credentials->check($password);
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->credentials->username();
    }

    /**
     * @return string
     */
    public function hashedPassword(): string
    {
        return $this->credentials->hashedPassword();
    }

    /**
     * @return string
     */
    public function passwordSalt(): string
    {
        return $this->credentials->salt();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
