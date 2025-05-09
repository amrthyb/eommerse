<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// use Symfony\Component\HttpFoundation\Response;

class Permission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!in_array($permission, $user->roles->permissions ?? [])) {
            Log::warning('User tidak memiliki izin: ' . $permission);
            return redirect()->to('/dashboard');
            // return redirect()->back();
    }

        return $next($request);
    }
}
