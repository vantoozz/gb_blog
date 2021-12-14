<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            return;
        }

        // Создаем объект пользователя
        // Функия createFrom сама создат UUID
        // и захэширует пароль
        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );

        $this->usersRepository->save($user);

        // Поллучаем UUID созданного пользователя
        $this->logger->info('User created: ' . $user->uuid());
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
