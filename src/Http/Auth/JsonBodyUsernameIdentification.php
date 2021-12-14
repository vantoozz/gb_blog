<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;

class JsonBodyUsernameIdentification implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            // Получаем имя пользователя из JSON-тела запроса;
            // ожидаем, что имя пользователя находится в поле username
            $username = $request->jsonBodyField('username');
        } catch (HttpException|InvalidArgumentException $e) {
            // Если невозможно получить имя пользователя из запроса –
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }

        try {
            // Ищем пользователя в репозитории
            // и возвращаем его
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден –
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
