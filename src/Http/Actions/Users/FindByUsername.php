<?php

namespace GeekBrains\Blog\Http\Actions\Users;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

// Класс реализует контракт действия
class FindByUsername implements ActionInterface
{
    // Нам понабодится репозиторий пользователей,
    // внедряем контракт его в качесве зависимости
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получть искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username –
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берем из описания исключения
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден –
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->username(),
            'name' => $user->name()->first() . ' ' . $user->name()->last(),
        ]);
    }
}
