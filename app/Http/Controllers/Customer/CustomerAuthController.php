<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /**
     * Show customer login / registration page.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->hasAdminAccess()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('customer.dashboard');
        }

        return view('customer.auth.login');
    }

    /**
     * Process customer login request (supports both standard form and AJAX modal).
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'অনুগ্রহ করে ইমেইল অথবা মোবাইল নম্বর দিন।',
            'password.required' => 'অনুগ্রহ করে পাসওয়ার্ড দিন।',
        ]);

        $login = trim($request->input('login'));
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $fieldType => $login,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            $redirectUrl = $user->hasAdminAccess()
                ? route('admin.dashboard')
                : ($request->input('redirect_to') ?: route('customer.dashboard'));

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'স্বাগতম! আপনি সফলভাবে লগইন করেছেন।',
                    'redirect' => $redirectUrl,
                    'user'     => [
                        'name'  => $user->name,
                        'email' => $user->email,
                    ],
                ]);
            }

            return redirect()->intended($redirectUrl)
                ->with('success', 'স্বাগতম ' . $user->name . '! আপনি সফলভাবে লগইন করেছেন।');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'ইমেইল/মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
            ], 422);
        }

        throw ValidationException::withMessages([
            'login' => ['ইমেইল/মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।'],
        ]);
    }

    /**
     * Process customer registration request (supports both standard form and AJAX modal).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required'     => 'অনুগ্রহ করে আপনার নাম দিন।',
            'email.required'    => 'একটি সঠিক ইমেইল ঠিকানা দিন।',
            'email.unique'      => 'এই ইমেইল দিয়ে ইতিমধ্যে একটি অ্যাকাউন্ট রয়েছে।',
            'password.required' => 'পাসওয়ার্ড দিন।',
            'password.min'      => 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => User::ROLE_CUSTOMER,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = $request->input('redirect_to') ?: route('customer.dashboard');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'অভিনন্দন! আপনার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে।',
                'redirect' => $redirectUrl,
                'user'     => [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->to($redirectUrl)
            ->with('success', 'অভিনন্দন! আপনার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে।');
    }

    /**
     * Customer Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'আপনি সফলভাবে লগআউট করেছেন।');
    }
}
