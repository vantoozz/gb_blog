<?php

namespace GeekBrains\Blog\UnitTests\Repositories\UsersRepository;

use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
    // когда запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        // Сначала нам нужно подготовыить все стабы

        // 2. Создаем стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 4. Стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // 5. Стаб запроса будет возвращать false
        //   при вызове метода fetch
        $statementStub->method('fetch')->willReturn(false);

        // 3. Стаб подключения будет возвращать друой стаб –
        //    стаб запроса – при вызове метода prepare
        $connectionStub->method('prepare')->willReturn($statementStub);

        // 1. Передаем в репозиторий стаб подключения
        $repository = new SqliteUsersRepository($connectionStub);

        // Ожидаем, что будеи брошено исключение
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        // Вызываем метод получения пользователя
        $repository->getByUsername('Ivan');
    }
    
    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                // добавили пароль
                ':password' => 'some_password',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);

        $repository->save(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'ivan123',
                // добавили пароль
                'some_password',
                new Name('Ivan', 'Nikitin')
            )
        );
    }
}
