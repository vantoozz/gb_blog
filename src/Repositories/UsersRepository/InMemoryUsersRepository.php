<?php

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    /**
     * @var User[]
     */
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }


    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    // Добавили метод получения пользователя по username
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }
}
