<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\Users;

use GeekBrains\Blog\User;

/**
 * Interface UsersRepositoryInterface
 * @package GeekBrains\Blog\Repositories\Users
 */
interface UsersRepositoryInterface
{
    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     * @throws UsersRepositoryException
     */
    public function getByUsername(string $username): User;

    /**
     * @param User $user
     * @throws UsersRepositoryException
     */
    public function save(User $user): void;
}
