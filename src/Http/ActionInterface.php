<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ActionInterface
 * @package GeekBrains\Blog\Http
 */
interface ActionInterface
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse;
}
