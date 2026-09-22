<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Sanitize email
        $request->merge(['email' => strtolower($request->email)]);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // DATA INTEGRITY FIX: Wrap multi-table insert in transaction
        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'applicant',
            ]);

            // Auto-create Applicant Profile for Admin visibility
            \App\Models\Applicant::create([
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'General Applicant',
                'status' => 'Pending',
                'phone' => 'Not provided',
                'location' => 'Not provided',
            ]);

            return $user;
        });

        Auth::login($user);

        // Dispatch Welcome Email
        $user->notify(new \App\Notifications\WelcomeApplicantNotification($user->name));

        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('applicant.profile')]);
        }

        return redirect()->route('applicant.profile')->with('success', 'Account created successfully!');
    }

    /**
     * Handle partner registration.
     */
    public function registerPartner(Request $request)
    {
        // Sanitize email
        $request->merge(['email' => strtolower($request->email)]);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = User::create([
            'name' => $validated['company_name'], // Use company name as user name for partners
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'partner',
        ]);

        Auth::login($user);

        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('partner.dashboard')]);
        }

        return redirect()->route('partner.dashboard')->with('success', 'Partner account created successfully!');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'login_type' => ['nullable', 'string'], // Added login_type validation
        ]);

        $key = 'login|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => ['email' => ["Too many login attempts. Please try again in {$seconds} seconds."]],
                ], 429);
            }

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        // Check if user exists first
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            RateLimiter::hit($key, 300);
            $error = 'We check our records, but couldn\'t find an account with that email.';
            
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['email' => [$error]]], 422);
            }
            return back()->withErrors(['email' => $error])->onlyInput('email');
        }

        // SECURITY FIX: Block deactivated users from logging in
        if (!$user->is_active) {
            RateLimiter::hit($key, 300);
            $error = 'Your account has been deactivated by the administrator. Please contact support.';
            
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['email' => [$error]]], 403);
            }
            return back()->withErrors(['email' => $error])->onlyInput('email');
        }

        // Attempt authentication
        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            // Strict Role Check based on Login Form Type
            if ($request->filled('login_type')) {
                $role = Auth::user()->role;
                $expectedRole = $request->login_type;

                // Admin bypass? No, Admin should use Admin login.
                // Allow Staff to login via Admin portal
                if ($role !== $expectedRole && !($expectedRole === 'admin' && $role === 'staff')) {
                    Auth::logout();
                    
                    $errorMessage = "Access Restricted: You cannot log in here.";
                    if ($expectedRole === 'partner') {
                        $errorMessage = "Access Denied: This portal is for Partners only. Please use the Applicant login.";
                    } elseif ($expectedRole === 'applicant') {
                        $errorMessage = "Access Denied: This portal is for Applicants only. Please use the Partner login.";
                    } elseif ($expectedRole === 'admin' || $expectedRole === 'staff') {
                         $errorMessage = "Access Denied: Administrative access only.";
                    }

                    if ($request->wantsJson()) {
                        return response()->json([
                            'errors' => ['email' => [$errorMessage]],
                        ], 422); // Unprocessable Entity
                    }
                    
                    return back()->withErrors(['email' => $errorMessage])->onlyInput('email');
                }
            }

            if (Auth::user()->role === 'partner') {
                if ($request->wantsJson()) {
                    return response()->json(['redirect' => route('partner.dashboard')]);
                }
                return redirect()->route('partner.dashboard');
            }
            if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') {
                if ($request->wantsJson()) {
                    return response()->json(['redirect' => route('admin.dashboard')]);
                }
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->role === 'applicant') {
                if ($request->wantsJson()) {
                    return response()->json(['redirect' => route('applicant.profile')]);
                }
                return redirect()->route('applicant.profile');
            }

            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('portal')]);
            }
            return redirect()->route('portal');
        }

        RateLimiter::hit($key, 300); // 5 minutes decay

        $error = 'Incorrect password. Please try again.';

        if ($request->wantsJson()) {
            return response()->json([
                'errors' => [
                    'password' => [$error], 
                ],
            ], 422);
        }

        return back()->withErrors([
            'password' => $error,
        ])->onlyInput('email');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
