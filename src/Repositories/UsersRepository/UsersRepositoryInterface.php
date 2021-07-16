<?php declare(strict_types=1);

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\User;

/**
 * Interface UsersRepositoryInterface
 * @package GeekBrains\Blog\Repositories\UsersRepository
 */
interface UsersRepositoryInterface
{
    /**
     * @param int $userId
     * @return User
     * @throws UserNotFoundException
     */
    public function get(int $userId): User;

    /**
     * @param User $user
     */
    public function save(User $user): void;
}
