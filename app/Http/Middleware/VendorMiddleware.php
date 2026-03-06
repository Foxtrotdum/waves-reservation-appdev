<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Always pull the authenticated user from the admin guard
        $user = Auth::guard('admin')->user();

        if (!$user) {
            Log::warning('Vendor access attempted without admin guard authentication.');
            return redirect()->route('login');
        }

        // Log for debugging
        Log::info('Authenticated admin user found via admin guard:', ['name' => $user->name]);

        if ($user->role === 'Vendor') {
            return $next($request);
        }

        // not vendor
        return redirect()->route('login');
    }
}
