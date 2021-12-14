<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
