<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;

/**
 * Class Login
 * @package GeekBrains\Blog\Http\Actions
 */
final class Login implements ActionInterface
{
    /**
     * Login constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param AuthenticationInterface $authentication
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private AuthenticationInterface $authentication,
    ) {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UsersRepositoryException
     */
    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
            $password = $request->query('password');
        } catch (HttpException) {
            return new ErrorResponse('No credentials');
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return new ErrorResponse('User not found or password is incorrect');
        }

        if (!$user->checkPassword($password)) {
            return new ErrorResponse('User not found or password is incorrect');
        }

        return new SuccessfulResponse([
            'token' => $this->authentication->token($user),
        ]);
    }
}
