<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Authentication;

use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NaiveAuthentication
 * @package GeekBrains\Blog\Http\Authentication
 */
final class NaiveAuthentication implements AuthenticationInterface
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
     * @return User
     * @throws NotAuthenticatedException
     * @throws UsersRepositoryException
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

    /**
     * @param User $user
     * @return string
     */
    public function token(User $user): string
    {
        return $user->username();
    }
}
