<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;


/**
 * Class ErrorResponse
 * @package GeekBrains\Blog\Http
 */
final class ErrorResponse extends Response
{
    /**
     *
     */
    protected const SUCCESS = false;

    /**
     * @param string $reason
     */
    public function __construct(
        private string $reason = 'Something goes wrong'
    ) {
    }

    /**
     * @return array
     */
    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
