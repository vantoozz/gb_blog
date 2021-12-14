<?php

// Функция с двумя параметрами возвращает строку
function someFunction(bool $one, int $two = 123,): string
{
    return $one . $two;
}

// Создаем объект рефлексии
// Передаем ему имя интересующей нас функции
$reflection = new ReflectionFunction('someFunction');

// Получаем тип возвращаемого фунцкией знпачения
echo $reflection->getReturnType()->getName() . "\n";

// Получаем параметры функции
foreach ($reflection->getParameters() as $parameter) {
    // Для каждлго праметра функции
    // получаем имя и тип
    echo $parameter->getName() . '[' . $parameter->getType()->getName() . "]\n";
}
