<?php declare(strict_types=1);

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Actions\Login;
use GeekBrains\Blog\Http\Actions\Posts\DeletePost;
use GeekBrains\Blog\Http\Actions\Posts\MyPosts;
use GeekBrains\Blog\Http\Actions\Posts\PostComments;
use GeekBrains\Blog\Http\Actions\Posts\PostsByAuthor;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/bootstrap.php';

$request = new Request($_GET, $_SERVER);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $container->get(LoggerInterface::class)->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}

if (is_file(__DIR__ . '/public' . $path)) {
    return false;
}

$routes = [
    '/login' => Login::class,
    '/posts/author' => PostsByAuthor::class,
    '/posts/my' => MyPosts::class,
    '/posts/delete' => DeletePost::class,
    '/comments' => PostComments::class,
];

if (!array_key_exists($path, $routes)) {
    $container->get(LoggerInterface::class)->warning("Not found: $path");
    (new ErrorResponse('Not found'))->send();
    return;
}

/** @var ActionInterface $action */
$action = $container->get($routes[$path]);

try {
    $action->handle($request)->send();
} catch (AppException $e) {
    $container->get(LoggerInterface::class)->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
}
