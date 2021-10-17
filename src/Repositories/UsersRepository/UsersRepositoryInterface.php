<?php

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;

    public function get(UUID $uuid): User;

    // Добавили метод
    public function getByUsername(string $username): User;
}
