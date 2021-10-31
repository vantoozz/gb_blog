<?php

namespace GeekBrains\Blog\UnitTests\Container;


final class SomeClass implements SomeInterface
{

    public function __construct(
        private Two $two
    ) {
    }

    public function calculateSomething(): int
    {
        return $this->two->number();
    }
}
