<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;

/**
 * Class Request
 * @package GeekBrains\Blog\Http
 */
final class Request
{
    /**
     * @param string[] $query
     * @param string[] $server
     */
    public function __construct(
        private array $query,
        private array $server
    ) {
    }

    /**
     * @return string
     * @throws HttpException
     */
    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException('Cannot get path from the request');
        }

        $components = parse_url($this->server['REQUEST_URI']);

        if (!is_array($components) || !array_key_exists('path', $components)) {
            throw new HttpException('Cannot get path from the request');
        }

        return $components['path'];
    }

    /**
     * @param string $param
     * @return string
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->query)) {
            throw new HttpException("No such query param in the request: $param");
        }

        $value = trim($this->query[$param]);

        if (empty($value)) {
            throw new HttpException("Empty query param in the request: $param");
        }

        return $value;
    }

    /**
     * @param string $header
     * @return string
     * @throws HttpException
     */
    public function header(string $header): string
    {
        $headerName = mb_strtoupper("http_$header");

        if (!array_key_exists($headerName, $this->server)) {
            throw new HttpException("No such header in the request: $header");
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            throw new HttpException("Empty header in the request: $header");
        }

        return $value;
    }
}
