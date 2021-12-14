<?php

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    // Логируем информацию об исключении.
    // Объект исключения передатся логгеру
    // с ключом "exception".
    // Уровень логирования -- ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}
