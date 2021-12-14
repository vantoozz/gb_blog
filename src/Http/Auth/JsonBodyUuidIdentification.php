<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

class JsonBodyUuidIdentification implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            // Получаем пользователя из JSON-тела запроса
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса –
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }

        try {
            // Ищем пользователя в репозитории
            // и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден –
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
