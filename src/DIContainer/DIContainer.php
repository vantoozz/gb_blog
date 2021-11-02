<?php

namespace GeekBrains\Blog\DIContainer;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

class DIContainer implements ContainerInterface
{
    private array $resolvers = [];

    public function bind(string $id, $resolver): void
    {
        $this->resolvers[$id] = $resolver;
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

    public function get(string $type): object
    {
        if (!array_key_exists($type, $this->resolvers)) {
            return $this->resolve($type);
        }

        $resolver = $this->resolvers[$type];

        if (is_object($resolver)) {
            return $resolver;
        }

        return $this->resolve((string)$resolver);
    }

    private function resolve(string $className): object
    {
        if (!class_exists($className)) {
            throw new NotFoundException("No such class: $className");
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
                throw new NotFoundException(
                    "Cannot find type of $parameter->name @ $className"
                );
            }

            if ($parameterType->isBuiltin()) {
                throw new NotFoundException(
                    "Cannot resolve built-in class [$parameterType] as $parameter->name @ $className"
                );
            }

            $parameters[] = $this->get($parameterType->getName());
        }

        return $reflectionClass->newInstance(...$parameters);
    }
}
