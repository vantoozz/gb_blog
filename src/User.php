<?php

namespace GeekBrains\Blog;

class User
{
    // Добавим свойство username
    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $name
    ) {
    }


    public function username(): string
    {
        return $this->username;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }


    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}
