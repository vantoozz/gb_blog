<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @return JsonResponse
     * @throws UsersRepositoryException
     */
    public function handle(Request $request): JsonResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        if (empty($username) || empty($password)) {
            return new JsonResponse(['success' => false]);
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return new JsonResponse(['success' => false]);
        }

        if (!$user->checkPassword($password)) {
            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse([
            'success' => true,
            'token' => $this->authentication->token($user),
        ]);
    }
}
