<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Users;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

/**
 * Class SqliteUsersRepository
 * @package GeekBrains\Blog\Repositories\Users
 */
final class SqliteUsersRepository implements UsersRepositoryInterface
{

    /**
     * SqliteUsersRepository constructor.
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @param string $username
     * @return User
     */
    public function getByUsername(string $username): User
    {
        try {
            /** @var array $result */
            $result = $this->connection->executeQuery(
                'SELECT * FROM users WHERE username = :username',
                ['username' => $username]
            )->fetchAllAssociative();
        } catch (DbalException $e) {
            throw new UsersRepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        if (count($result) !== 1) {
            throw new UserNotFoundException("Cannot find user by username: $username");
        }

        $data = $result[0];

        return new User(
            new UUID($data['uuid']),
            new Name($data['first_name'], $data['last_name']),
            new Credentials($data['username'], $data['password_hash'], $data['password_salt'])
        );
    }

    /**
     * @param User $user
     * @throws UsersRepositoryException
     */
    public function save(User $user): void
    {
        $query = <<<'SQL'
            INSERT INTO users (
                uuid, 
                first_name, 
                last_name, 
                username, 
                password_hash, 
                password_salt, 
                created_at, 
                updated_at
            ) VALUES (
                :uuid, 
                :first_name, 
                :last_name, 
                :username, 
                :password_hash, 
                :password_salt, 
                datetime('now'), 
                datetime('now')
            )
            ON CONFLICT (uuid) DO UPDATE SET 
                first_name = :first_name, 
                last_name = :last_name,
                username = :username,
                password_hash = :password_hash,
                password_salt = :password_salt,
                updated_at = datetime('now')
SQL;

        try {
            $this->connection->executeQuery(
                $query,
                [
                    'uuid' => (string)$user->uuid(),
                    'first_name' => $user->name()->first(),
                    'last_name' => $user->name()->last(),
                    'username' => $user->username(),
                    'password_hash' => $user->hashedPassword(),
                    'password_salt' => $user->passwordSalt(),
                ]
            );
        } catch (DbalException $e) {
            throw new UsersRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
