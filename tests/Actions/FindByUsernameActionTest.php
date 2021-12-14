<?php

namespace GeekBrains\Blog\UnitTests\Actions;

use GeekBrains\Blog\Http\Actions\Users\FindByUsername;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{


    // Запускаем тест в отдельном процессе
    /**
     * @runInSeparateProcess
     */
    // Тест, проверяющий, что будет возвращен неудачный ответ,
    // если в запросе нет параметра username
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        // Создаем объект запроса
        // Вместо суперглобальных переменных
        // передаем простые массивы
        $request = new Request([], [], '');

        // Создаем стаб репозитория пользователей
        $usersRepository = $this->usersRepository([]);

        //Создаем объект действия
        $action = new FindByUsername($usersRepository);

        // Запускаем действие
        $response = $action->handle($request);

        // Проверяем, что ответ – неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);

        // Опиываем ожидание того, счто будет отправлено в поток вывода
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        // Отправляем ответ в поток вывода
        $response->send();
    }

    /**
     * @runInSeparateProcess
     */
    // Тест, проверяющий, что будет возвращен неудачный ответ,
    // если пользователь не найден
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        // Теперь запрос будет иметь параметр username
        $request = new Request(['username' => 'ivan'], [], '');

        // Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     */
    // Тест, проверяющий, что будет возвращен удачный ответ,
    // если пользователь найден
    public function testItReturnsSuccessfulResponse(): void
    {
        // Добавили пустое тело запроса
        $request = new Request(['username' => 'ivan'], [], '');

        // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                'ivan',
                'some_password',
                new Name('Ivan', 'Nikitin')
            ),
        ]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        // Проверяем, что ответ – удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');

        $response->send();
    }

    // Функция, создающая стабы репозитория пользователей,
    // принимает массив "существующих" пользователей
    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {

            public function __construct(
                private array $users
            ) {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->username()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("Not found");
            }
        };
    }
}
