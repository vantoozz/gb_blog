<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NaiveRequestAuth
 * @package GeekBrains\Blog\Auth
 */
final class NaiveAuth implements AuthInterface
{

    /**
     * NaiveRequestAuth constructor.
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function hasUser(Request $request): bool
    {
        return $request->query->has('username');
    }

    /**
     * @param Request $request
     * @return User
     * @throws NotAuthenticatedException
     */
    public function user(Request $request): User
    {
        $username = $request->query->get('username');
        if (empty($username)) {
            throw new NotAuthenticatedException('No username provided');
        }

        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            throw new NotAuthenticatedException("No such user: $username");
        }
    }
}
