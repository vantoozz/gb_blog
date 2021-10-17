<?php

namespace GeekBrains\Blog;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;

final class UUID
{
    // Внутри объекта мы храним UUID как строку
    public function __construct(
        private string $uuidString
    ) {
        // Если входная строка не подходит по формату –
        // бросаем исключение InvalidArgumentException
        // (его мы тоже добавили)
        //
        // Таким образом мы гарантируем, что если объект
        // был создан, то он точно содержит правмльный UUID
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    // А вот так мы можем сгененивовать новый случайный UUID
    // и получить его в качестве объекта нашего класса
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }
}
