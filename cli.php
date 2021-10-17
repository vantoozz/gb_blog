<?php

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$usersRepository = new SqliteUsersRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$command = new CreateUserCommand($usersRepository);

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

