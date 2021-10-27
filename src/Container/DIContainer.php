<?php

namespace GeekBrains\Blog\Container;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class DIContainer
{
    private array $resolvers = [];

    public function bind(string $id, $object): void
    {
        $this->resolvers[$id] = $object;
    }

    public function has(string $id): bool
    {
        try {
            $this->get($id);
            return true;
        } catch (ContainerException $e) {
            return false;
        }
    }

    public function get(string $id): object
    {
        if (!array_key_exists($id, $this->resolvers)) {
            return $this->resolve($id);
        }

        $object = $this->resolvers[$id];

        if (is_object($object)) {
            return $object;
        }

        if (!is_string($object)) {
            throw new ContainerException("Cannot resolve $id");
        }

        try {
            return $this->resolve($object);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function resolve(string $className): object
    {
        if (!class_exists($className)) {
            throw new ContainerException("No such class: $className");
        }

        $reflectionClass = new ReflectionClass($className);

        $constructor = $reflectionClass->getConstructor();

        if (null === $constructor || 0 === $constructor->getNumberOfParameters()) {
            return $reflectionClass->newInstance();
        }

        $parameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            $parameterType = $parameter->getType();
            if (!$parameterType instanceof ReflectionNamedType) {
                throw new ContainerException(
                    "Cannot find parameter's type $parameter->name @ $className"
                );
            }

            $parameters[] = $this->get($parameterType->getName());
        }

        return $reflectionClass->newInstance(...$parameters);
    }
}
