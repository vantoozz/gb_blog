<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface AuthInterface
 * @package GeekBrains\Blog\Auth
 */
interface AuthInterface
{
    /**
     * @param Request $request
     * @return bool
     */
    public function hasUser(Request $request): bool;

    /**
     * @param Request $request
     * @return User
     * @throws NotAuthenticatedException
     */
    public function user(Request $request): User;
}
