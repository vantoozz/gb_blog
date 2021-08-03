<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;


/**
 * Interface ActionInterface
 * @package GeekBrains\Blog\Http\Actions
 */
interface ActionInterface
{

    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function handle(Request $request): Response;
}
