<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($this->attemptLogin(Admin::class, $request)) {
            return redirect()->route('admin.dashboard');
        }

        // If not found, check in the Customers table
        if ($this->attemptLogin(Customer::class, $request)) {
            return redirect()->route('customer.reservation');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    private function attemptLogin($model, $request)
    {
        $user = $model::where('email', $request->email)->first();
        if (!$user) return false;
        // If checking a Customer, ensure they are active
        if ($model === Customer::class && $user && $user->is_active == 0) {
            return false;
        }

        if (Hash::check($request->password, $user->password)) {
            // Use guard based on model
            if ($model === Admin::class) {
                Auth::guard('admin')->login($user);
            } else {
               Auth::login($user);
            }
            return true;
        }

        return false;
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
