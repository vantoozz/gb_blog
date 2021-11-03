<?php

namespace GeekBrains\Blog\Container;

class DIContainer
{
    // Массив правил создания объектов
    private array $resolvers = [];


    // Метод для добавления правил
    public function bind(string $type, string $class)
    {
        $this->resolvers[$type] = $class;
    }

    public function get(string $type): object
    {
        // Если есть правило для создания объекта типа $type
        // например, $type может иметь значение
        // 'GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface'
        if (array_key_exists($type, $this->resolvers)) {
            // .. тогда мы будем создавать объект того класса,
            // которй указан в правиле согласно правилу, например
            // 'GeekBrains\Blog\Repositories\UsersRepository\InMemoryUsersRepository'
            $typeToCreate = $this->resolvers[$type];

            // Вызываем тот же самый метод контейнера
            // и передаем в нее имя класса, указананого в правиле
            return $this->get($typeToCreate);
        }
        
        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        return new $type();
    }
}
