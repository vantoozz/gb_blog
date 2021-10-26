<?php

namespace GeekBrains\Blog\Http;

// Абстракный класс ответа,
// содержащий общую функциональность
// успешного и неуспешного ответа
abstract class Response
{
    protected const SUCCESS = true;

    // Метод для отправки ответа
    public function send(): void
    {
        // Данные ответа:
        // маркировка успешности и полезные данные
        $data = ['success' => static::SUCCESS] + $this->payload();

        // Отправляем заголок, говорщий, что в теле ответа будет JSON
        header('Content-Type: application/json');

        // Кодируем в JSON и отправляем в теле ответа данные
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    // Декларация абстрактного метода,
    // возвращающего полезные данные ответа.
    abstract protected function payload(): array;
}
