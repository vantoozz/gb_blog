<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Authentication;

use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\User;

/**
 * Interface AuthenticationInterface
 * @package GeekBrains\Blog\Http\Authentication
 */
interface AuthenticationInterface
{
    /**
     * @param Request $request
     * @return User
     * @throws NotAuthenticatedException
     */
    public function user(Request $request): User;

    /**
     * @return User
     */
    public function token(User $user): string;
}
