<?php

namespace GeekBrains\Blog\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

// Согласно PSR-11, исключение описывающее ситуацию,
// когда объект не найден в контейнере,
// должно реализовать котракт NotFoundExceptionInterface
class NotFoundException extends Exception
    implements NotFoundExceptionInterface
{
}
