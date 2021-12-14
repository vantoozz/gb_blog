<?php

namespace GeekBrains\Blog\UnitTests\Commands;

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\ArgumentsException;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\UnitTests\DummyLogger;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItRequiresLastName(): void
    {
        // Передаем объект, возвращаемый нашей функцией
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            // Добавили паполь
            'password' => 'some_password',
            'first_name' => 'Ivan',
        ]));
    }

    public function testItRequiresPassword(): void
    {
        // Передаем объект, возвращаемый нашей функцией
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');

        $command->handle(new Arguments([
            'username' => 'Ivan',
        ]));
    }

    public function testItRequiresFirstName(): void
    {
        // Вызываем ту же функцию
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan', 'password' => 'some_password']));
    }

    public function testItSavesUserToRepository(): void
    {
        // Создаем объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {

            // В этом свойстве мы храним информацию о том,
            // был би вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощьэ этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        // Передаем наш мок в команду
        $command = new CreateUserCommand(
            $usersRepository,
            new DummyLogger()
        );

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => 'some_password',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        // Прверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
}
