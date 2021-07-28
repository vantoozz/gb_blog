<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\Authentication\SignatureAuthentication;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Comments\SqliteCommentsRepository;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\Users\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$container = $builder->build();

$container->set(
    Connection::class,
    DI\factory(fn() => DriverManager::getConnection([
        'url' => 'sqlite:///blog.sqlite',
    ]))
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
    CommentsRepositoryInterface::class,
    DI\get(SqliteCommentsRepository::class)
);

$container->set(
    AuthenticationInterface::class,
    DI\get(SignatureAuthentication::class)
);


$container->set(
    \Faker\Generator::class,
    DI\factory(function () {
        $generator = new \Faker\Generator();

        $generator->addProvider(new Person($generator));
        $generator->addProvider(new Text($generator));
        $generator->addProvider(new Internet($generator));
        $generator->addProvider(new Lorem($generator));
        $generator->addProvider(new \Faker\Provider\DateTime($generator));

        return $generator;
    })
);

return $container;










