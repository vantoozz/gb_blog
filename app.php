<?php declare(strict_types=1);

use GeekBrains\Blog\Commands\AddUser;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$application->setName('GeekBrains\' Blog');

$application->addCommands(array_map(
    static fn(string $className) => $container->get($className),
    [
        AddUser::class,
    ]
));

$application->run();

