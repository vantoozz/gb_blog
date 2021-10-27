<?php

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Exceptions\AppException;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {
    // "Заворачиваем" $argv в объект типа Arguments
    $command->handle(Arguments::fromArgv($argv));
}
// Так как мы добавили исключение ArgumentsException
// имеет смысл обрабатывать все исключения приложения,
// а не только исключение CommandException
catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

