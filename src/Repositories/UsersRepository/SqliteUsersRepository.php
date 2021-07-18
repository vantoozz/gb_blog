<?php declare(strict_types=1);


namespace GeekBrains\Blog\Repositories\UsersRepository;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\User;
use Ramsey\Uuid\UuidFactoryInterface;

/**
 * Class SqliteUsersRepository
 * @package GeekBrains\Blog\Repositories\UsersRepository
 */
final class SqliteUsersRepository implements UsersRepositoryInterface
{

    /**
     * SqliteUsersRepository constructor.
     * @param Connection $connection
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        private Connection $connection,
        private UuidFactoryInterface $uuidFactory,
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

        $user = new User(
            $this->uuidFactory->fromString($data['uuid']),
            new Name($data['first_name'], $data['last_name']),
            new Credentials($data['username'], $data['password_hash'], $data['password_salt'])
        );
        return $user;
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
                    'username' => $user->credentials()->username(),
                    'password_hash' => $user->credentials()->hashedPassword(),
                    'password_salt' => $user->credentials()->salt(),
                ]
            );
        } catch (DbalException $e) {
            throw new UsersRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
