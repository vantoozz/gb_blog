<?php

namespace GeekBrains\Blog\UnitTests\Container;

class Three
{
    public function __construct(
        private $something
    ) {
    }

    public function something()
    {
        return $this->something;
    }
}
