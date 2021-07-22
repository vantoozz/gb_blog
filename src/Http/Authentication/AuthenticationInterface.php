<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Authentication;

use GeekBrains\Blog\User;
use Symfony\Component\HttpFoundation\Request;

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
}
