<?php

namespace GeekBrains\Blog\UnitTests\Container;

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Container\NotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\InMemoryUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
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

    public function testItReturnsPredefinedObject(): void
    {
        // Создаем объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // вернуть объект типа SomeClass, он
        // возвращал бы предопределенный объект
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа SomeClass
        $object = $container->get(SomeClassWithParameter::class);

        // Проверяем, что контейнер вернул
        // объект того же типа
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        // Проверяем, что контейнер вернул
        // тот же самый объект
        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        // Создаем объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило получения
        // объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);

        // Проверяем, что контейнер вернул
        // объект нужного нам типа
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
}
