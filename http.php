<?php

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\Actions\Posts\CreatePost;
use GeekBrains\Blog\Http\Actions\Posts\FindByUuid;
use GeekBrains\Blog\Http\Actions\Users\FindByUsername;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили еще один уровеь вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/show' => new FindByUuid(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        // Дробавили новый маршрут
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
];

// Если у нас нет маршрутов для метода запроса –
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для данного метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по метолу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
