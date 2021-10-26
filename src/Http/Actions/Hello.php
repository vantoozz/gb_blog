<?php

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;

class Hello implements ActionInterface
{
    public function handle(Request $request): Response
    {
        return new SuccessfulResponse(['message' => 'hello']);
    }
}
