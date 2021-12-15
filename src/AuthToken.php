<?php

namespace GeekBrains\Blog;

use DateTimeImmutable;

class AuthToken
{
    public function __construct(
        private string $token,
        private UUID $userUuid,
        private DateTimeImmutable $expiresOn
    ) {
    }

    // Строка токена
    public function __toString(): string
    {
        return $this->token;
    }

    // UUID пользователя
    public function userUuid(): UUID
    {
        return $this->userUuid;
    }

    // Срок годности
    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}
