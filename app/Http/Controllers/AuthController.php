<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showSetup(): View|RedirectResponse
    {
        if (User::query()->exists()) {
            return to_route('login');
        }

        return view('auth.setup');
    }

    public function setup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            abort_unless(User::query()->doesntExist(), 404);

            return User::query()->create([
                'name' => $validated['name'],
                'email' => mb_strtolower($validated['email']),
                'password' => $validated['password'],
                'role' => 'admin',
                'is_active' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('dashboard')->with('success', 'Votre compte administrateur est prêt.');
    }

    public function showLogin(): View|RedirectResponse
    {
        if (User::query()->doesntExist()) {
            return to_route('setup');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = mb_strtolower($credentials['email']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis ne sont pas valides.',
            ]);
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Ce compte est désactivé. Contactez un administrateur.',
            ]);
        }

        $request->session()->regenerate();

        return to_route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);
        $validated['email'] = mb_strtolower($validated['email']);
        $status = Password::sendResetLink($validated);

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'status' : 'error',
            __($status),
        );
    }

    public function showResetPassword(string $token, Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        $credentials['email'] = mb_strtolower($credentials['email']);

        $status = Password::reset($credentials, function (User $user, string $password): void {
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? to_route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
