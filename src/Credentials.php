<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use Exception;
use GeekBrains\Blog\Exceptions\RuntimeException;

/**
 * Class Credentials
 * @package GeekBrains\Blog
 */
final class Credentials
{
    /**
     * Credentials constructor.
     * @param string $username
     * @param string $hashedPassword
     * @param string $salt
     */
    public function __construct(
        private string $username,
        private string $hashedPassword,
        private string $salt
    ) {
    }

    /**
     * @param string $username
     * @param string $password
     * @return static
     */
    public static function createFrom(string $username, string $password): self
    {
        try {
            $salt = bin2hex(random_bytes(40));
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return new self(
            $username,
            self::hash($username, $password, $salt),
            $salt
        );
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $salt
     * @return string
     */
    private static function hash(string $username, string $password, string $salt): string
    {
        return hash('whirlpool', $username . $password . $salt);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        return $this->hashedPassword === self::hash($this->username, $password, $this->salt);
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @return string
     */
    public function salt(): string
    {
        return $this->salt;
    }
}
