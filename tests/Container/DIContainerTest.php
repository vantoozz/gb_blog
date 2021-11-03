<?php

namespace GeekBrains\Blog\UnitTests\Container;

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Container\NotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\InMemoryUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        // Создаем объект контейнера
        $container = new DIContainer();

        // Описываем ожидаемое исключение
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: GeekBrains\Blog\UnitTests\Container\SomeClass'
        );

        // Пытаемся получить объект несуществующего класса
        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        // Создаем объект контейнера
        $container = new DIContainer();

        // Пытаемся получить объект класса без зависимостей
        $object = $container->get(SomeClassWithoutDependencies::class);

        // Проверяем, что объект, который вернул контейнер,
        // имеет желаемый тип
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }


    public function testItResolvesClassByContract(): void
    {
        // Создаем объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // создать объект, реализующий контракт
        // UsersRepositoryInterface, он возвращал бы
        // объект класса InMemoryUsersRepository
        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        // Пытаемся получить объект класса,
        // реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);

        // Проверяем, что объект, который вернул контейнер,
        // имеет желаемый тип
        $this->assertInstanceOf(
            InMemoryUsersRepository::class,
            $object
        );
    }
}
