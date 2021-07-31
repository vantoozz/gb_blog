<?php declare(strict_types=1);

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Http\Login;
use GeekBrains\Blog\Http\Posts\DeletePost;
use GeekBrains\Blog\Http\Posts\MyPosts;
use GeekBrains\Blog\Http\Posts\PostComments;
use GeekBrains\Blog\Http\Posts\PostsByAuthor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/bootstrap.php';

$request = Request::createFromGlobals();

$uri = strtok($request->getRequestUri(), '?');

if (is_file(__DIR__ . '/public/' . $request->getRequestUri())) {
    return false;
}

$routes = [
    '/login' => Login::class,
    '/posts/author' => PostsByAuthor::class,
    '/posts/my' => MyPosts::class,
    '/posts/delete' => DeletePost::class,
    '/comments' => PostComments::class,
];

if (!array_key_exists($uri, $routes)) {
    $container->get(LoggerInterface::class)->warning("Not found: $uri");
    (new Response(status: Response::HTTP_NOT_FOUND))->send();
    return;
}

/** @var ActionInterface $action */
$action = $container->get($routes[$uri]);

try {
    $action->handle($request)->send();
} catch (AppException $e) {
    $container->get(LoggerInterface::class)->error($e->getMessage(), ['exception' => $e]);
    (new JsonResponse(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR))->send();
}
