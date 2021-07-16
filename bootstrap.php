<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$container = $builder->build();

$container->set(UsersRepositoryInterface::class,
    DI\factory(fn() => new SqliteUsersRepository(
        DriverManager::getConnection([
            'url' => 'sqlite:///blog.sqlite',
        ])
    ))
);

return $container;
