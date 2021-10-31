<?php

namespace GeekBrains\Blog\UnitTests\Container;

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Container\NotFoundException;
use PHPUnit\Framework\TestCase;

final class DIContainerTest extends TestCase
{
    public function testItReturnsBoundObject(): void
    {
        $container = new DIContainer();
        $container->bind(One::class, new One("some string"));

        $object = $container->get(One::class);

        $this->assertInstanceOf(One::class, $object);
        $this->assertEquals("some string", $object->getStringParameter());
    }

    public function testItIsNotResolvingBuiltinTypes(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve built-in class [string] as stringParameter @ GeekBrains\Blog\UnitTests\Container\One'
        );

        $container->get(One::class);
    }

    public function testItThrowsAnExceptionWhenResolverIsNotAString(): void
    {
        $container = new DIContainer();

        $container->bind(One::class, 1.23);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'No such class: 1.23'
        );

        $container->get(One::class);
    }

    public function testItResolvesClassWithoutConstructor(): void
    {
        $container = new DIContainer();

        $object = $container->get(Two::class);

        $this->assertInstanceOf(Two::class, $object);
    }

    public function testItThrowsAnExceptionIfParameterHasNoType(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot find type of something @ GeekBrains\Blog\UnitTests\Container\Three'
        );

        $container->get(Three::class);
    }

    public function testItThrowsAnExceptionIfClassNotExists(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'No such class: GeekBrains\Blog\UnitTests\Container\Four'
        );

        $container->get(Four::class);
    }

    public function testItResolvesDependenciesRecursively(): void
    {
        $container = new DIContainer();

        $container->bind(SomeInterface::class, SomeClass::class);

        $object = $container->get(SomeInterface::class);

        $this->assertInstanceOf(SomeClass::class, $object);
        $this->assertEquals(2, $object->calculateSomething());
    }

    public function testItDoesHaveAnObject(): void
    {
        $container = new DIContainer();

        $container->bind(One::class, new One("some string"));

        $this->assertTrue($container->has(One::class));
    }

    public function testItDoesNotHaveAnObject(): void
    {
        $container = new DIContainer();

        $this->assertFalse($container->has(Three::class));
    }
}
