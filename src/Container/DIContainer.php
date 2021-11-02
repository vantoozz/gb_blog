<?php

namespace GeekBrains\Blog\Container;

class DIContainer
{
    public function get(string $type): object
    {
        // Бросаем исключение, только если класс не существует
        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        // Создаем объект класса $type
        return new $type();
    }
}
