<?php

namespace GeekBrains\Blog\Http\Auth;


use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;

class PasswordAuthentication implements AuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль с помошью метода пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}
