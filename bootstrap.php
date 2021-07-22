<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\Authentication\NaiveAuthentication;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\Users\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$container = $builder->build();

$container->set(Connection::class,
    DI\factory(fn() => DriverManager::getConnection([
        'url' => 'sqlite:///blog.sqlite',
    ])));

$container->set(
    UuidFactoryInterface::class,
    DI\get(UuidFactory::class)
);

$container->set(
    UsersRepositoryInterface::class,
    DI\get(SqliteUsersRepository::class)
);

$container->set(
    PostsRepositoryInterface::class,
    DI\get(SqlitePostsRepository::class)
);

$container->set(
    AuthenticationInterface::class,
    DI\get(NaiveAuthentication::class)
);

return $container;
