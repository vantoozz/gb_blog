<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Users;

use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PDO;
use PDOException;

/**
 * Class SqliteUsersRepository
 * @package GeekBrains\Blog\Repositories\Users
 */
final class SqliteUsersRepository implements UsersRepositoryInterface
{

    /**
     * SqliteUsersRepository constructor.
     * @param PDO $pdo
     */
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     * @throws UsersRepositoryException
     */
    public function getByUsername(string $username): User
    {
        try {
            $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
            $statement->execute([$username]);
            /** @var array|false $result */
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new UsersRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        if (false === $result) {
            throw new UserNotFoundException("Cannot find user by username: $username");
        }

        try {
            return new User(
                new UUID($result['uuid']),
                new Name($result['first_name'], $result['last_name']),
                new Credentials($result['username'], $result['password_hash'], $result['password_salt'])
            );
        } catch (InvalidArgumentException $e) {
            throw new UsersRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }
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
                updated_at = DATETIME('now')
SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                ':uuid' => (string)$user->uuid(),
                ':first_name' => $user->name()->first(),
                ':last_name' => $user->name()->last(),
                ':username' => $user->username(),
                ':password_hash' => $user->hashedPassword(),
                ':password_salt' => $user->passwordSalt(),
            ]);
        } catch (PDOException $e) {
            throw new UsersRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
