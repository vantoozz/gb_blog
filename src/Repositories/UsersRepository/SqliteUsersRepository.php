<?php

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\Name;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UsersRepositoryInterface
{

    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(User $user): void
    {
        // Добавили поле username в запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
             VALUES (:uuid, :username, :first_name, :last_name)'
        );

        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
        ]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    // Добавили метод получения пользователя по username

    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }

        // Создаем объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );
    }

    // Вынесли общую логику в отдельный приватный метод

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }
}