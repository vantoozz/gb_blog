<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;

use Exception;
use GeekBrains\Blog\Exceptions\RuntimeException;

/**
 * Class Response
 * @package GeekBrains\Blog\Http
 */
abstract class Response
{
    protected const SUCCESS = true;

    /**
     *
     */
    public function send(): void
    {
        $data = ['success' => static::SUCCESS] + $this->payload();

        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        header('Content-Type: application/json');
        
        echo $json;
    }

    /**
     * @return array
     */
    abstract protected function payload(): array;
}
