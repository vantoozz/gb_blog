<?php

namespace GeekBrains\Blog;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private string $hashedPassword,
        private Name $name
    ) {
    }

    public static function createFrom(
        string $username,
        string $password,
        Name $name
    ): self {
        // Генерируем UUID
        $uuid = UUID::random();
        return new self(
            $uuid,
            $username,
            // Передаем сгенерированный UUID
            // в функцию хэшированиия пароля
            self::hash($password, $uuid),
            $name
        );
    }

    private static function hash(string $password, UUID $uuid): string
    {
        // Используем UUID в качестве соли
        return hash('sha256', $uuid . $password);
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function checkPassword(string $password): bool
    {
        // Передаем UUID пользователя
        // в функцию хэшированиия пароля
        return $this->hashedPassword
            === self::hash($password, $this->uuid);
    }

    public function username(): string
    {
        return $this->username;
    }

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
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
