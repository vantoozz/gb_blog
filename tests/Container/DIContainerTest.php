<?php

namespace GeekBrains\Blog\UnitTests\Container;

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Container\NotFoundException;
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
}
