<?php declare(strict_types=1);


namespace GeekBrains\Blog\Repositories\UsersRepository;


use Doctrine\DBAL\Connection;
use GeekBrains\Blog\User;

/**
 * Class SqliteUsersRepository
 * @package GeekBrains\Blog\Repositories\UsersRepository
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
     * @param int $userId
     * @return User
     */
    public function get(int $userId): User
    {
        // TODO: Implement get() method.
    }

    /**
     * @param User $user
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(User $user): void
    {

//        $this->connection->executeQuery()
    }
}
