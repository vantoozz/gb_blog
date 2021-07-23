<?php declare(strict_types=1);

use GeekBrains\Blog\Commands\Posts\CreatePost;
use GeekBrains\Blog\Commands\Posts\GetPostsByAuthor;
use GeekBrains\Blog\Commands\Users\CreateUser;
use GeekBrains\Blog\Commands\Users\UpdateUser;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$application->setName('GeekBrains\' Blog');

$application->addCommands(array_map(
    static fn(string $className) => $container->get($className),
    [
        CreateUser::class,
        UpdateUser::class,
        CreatePost::class,
        GetPostsByAuthor::class,
    ]
));

$application->run();
