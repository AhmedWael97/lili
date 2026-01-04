<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /**
     * Show login form
     */
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Log successful login
            // $this->auditRepo->log(
            //     'user_logged_in',
            //     "User logged in",
            //     $user->id,
            //     'User',
            //     $user->id
            // );

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard.index'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $this->auditRepo->log(
                'user_logged_out',
                "User logged out",
                $user->id,
                'User',
                $user->id
            );
        }

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
