<?php

// $someObject – это объект анонимного класса
$someObject = new class {

    // Метод в анонимном классе
    public function sayHello(string $name): void
    {
        echo "Hello, $name!";
    }
};

// На объекте $someObject можно вызывать методы
$someObject->sayHello('Ivan');

// Hello, Ivan!
