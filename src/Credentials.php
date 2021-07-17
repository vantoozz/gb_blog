<?php declare(strict_types=1);


namespace GeekBrains\Blog;

use Exception;
use JetBrains\PhpStorm\Pure;

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
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->salt);
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


    /**
     * @param string $username
     * @param string $password
     * @return static
     * @throws Exception
     */
    public static function createFrom(string $username, string $password): self
    {
        $salt = random_bytes(128);
        return new self(
            $username,
            self::hash($password, $salt),
            $salt
        );
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    private static function hash(string $password, string $salt): string
    {
        return hash('whirlpool', $password . $salt);
    }
}
