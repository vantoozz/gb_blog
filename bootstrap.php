<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$container = $builder->build();

$container->set(UuidFactoryInterface::class,
    DI\factory(fn() => new UuidFactory())
);

$container->set(UsersRepositoryInterface::class,
    DI\factory(fn(ContainerInterface $container) => new SqliteUsersRepository(
        DriverManager::getConnection([
            'url' => 'sqlite:///blog.sqlite',
        ]),
        $container->get(UuidFactoryInterface::class)
    ))
);

return $container;
