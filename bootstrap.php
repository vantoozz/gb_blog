<?php

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;


// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Создаем объект контейнера ..
$container = new DIContainer();

// .. и настраиваем его:

// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

// Возвращаем объект контейнера
return $container;

