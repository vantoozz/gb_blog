<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;

use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Login
 * @package GeekBrains\Blog\Http
 */
final class Login implements ActionInterface
{
    /**
     * Login constructor.
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
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

        if (!$user->credentials()->check($password)) {
            return new JsonResponse(['success' => false]);
        }

        $request->getSession()->set("csdcsd", 324234);

        return new JsonResponse(['success' => true]);
    }
}