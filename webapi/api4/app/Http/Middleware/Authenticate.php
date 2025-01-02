<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Nếu là request từ API và chưa xác thực, trả về null để không chuyển hướng
        if (!$request->expectsJson()) {
            return route('login');
        }

        return null; 
    }
}
