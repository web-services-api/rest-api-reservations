<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !$user->tokenCan('ROLE_ADMIN')) {
            return response()->json(['message' => 'Access denied'], 403);
        }
    }
}