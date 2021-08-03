<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;


/**
 * Class SuccessfulResponse
 * @package GeekBrains\Blog\Http
 */
final class SuccessfulResponse extends Response
{
    /**
     *
     */
    protected const SUCCESS = true;

    /**
     * @param array $data
     */
    public function __construct(
        private array $data = []
    ) {
    }

    /**
     * @return array
     */
    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}
