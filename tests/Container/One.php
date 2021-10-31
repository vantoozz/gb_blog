<?php

namespace GeekBrains\Blog\UnitTests\Container;

class One
{
    public function __construct(
        private string $stringParameter
    ) {
    }

    /**
     * @return string
     */
    public function getStringParameter(): string
    {
        return $this->stringParameter;
    }
}
