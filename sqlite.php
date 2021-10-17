<?php declare(strict_types=1);

//Создаем объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Вставляем строку в таблицу пользователей
$connection->exec(
    "INSERT INTO users (first_name, last_name) VALUES ('Ivan', 'Nikitin')"
);


interface CalculatorInterface
{
    public function sum(int $a, int $b): int;
}

class MyCalculator implements CalculatorInterface
{
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

    public function double(int $a): int
    {
        return $a * 2;
    }
}
