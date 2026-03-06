<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            Log::warning('Admin user not found via admin guard.');
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        Log::info('Authenticated admin user found:', ['name' => $user->name]);
        if ($user && ($user->role === 'Vendor' || $user->role === 'Manager')) {
            return $next($request);  // Allow the request to continue if the user is an admin or manager
        }
        return redirect()->route('login')->with('error', 'Unauthorized role.');
    }
}
