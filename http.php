<?php declare(strict_types=1);

use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Http\Login;
use GeekBrains\Blog\Http\PostsByAuthor;
use Psr\Container\ContainerInterface;
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
];

if (!array_key_exists($uri, $routes)) {
    (new Response(status: Response::HTTP_NOT_FOUND))->send();
    return;
}

/** @var ActionInterface $action */
$action = $container->get($routes[$uri]);

$action->handle($request)->send();
