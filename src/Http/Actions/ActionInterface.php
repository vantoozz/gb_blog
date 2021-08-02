<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Exceptions\AppException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ActionInterface
 * @package GeekBrains\Blog\Http\Actions
 */
interface ActionInterface
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AppException
     */
    public function handle(Request $request): JsonResponse;
}
