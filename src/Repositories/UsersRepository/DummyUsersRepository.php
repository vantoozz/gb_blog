<?php

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\Name;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

// Dummy – чучуло, манекен
class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // Ничего не делаем
    }

    public function get(UUID $uuid): User
    {
        // И тут ничего не делаем
        throw new UserNotFoundException('Not found');
    }

    public function getByUsername(string $username): User
    {
        // Нас интересует реализация только этого метода
        // Для нашего теста не важно, что это будет за пользователь,
        // поэтому возвращаем совершенно произвольного
        return new User(UUID::random(), 'some_username', 'some_password', new Name('first_name', 'last_name'));
    }
}
