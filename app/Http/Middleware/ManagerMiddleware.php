<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Use admin guard for manager authentication
        $user = Auth::guard('admin')->user();

        if (!$user) {
            Log::warning('Manager access attempted without admin guard authentication.');
            return redirect()->route('login');
        }

        Log::info('Authenticated admin user found via admin guard:', ['name' => $user->name]);

        if ($user->role === 'Manager') {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
