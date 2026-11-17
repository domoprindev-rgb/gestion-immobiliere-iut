<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'gestionnaire', 'comptable'])],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::query()->create([
            ...$validated,
            'email' => mb_strtolower($validated['email']),
            'is_active' => true,
        ]);

        return to_route('users.index')->with('success', 'Le compte a été créé.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'gestionnaire', 'comptable'])],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($user->isAdministrator()
            && ($validated['role'] !== 'admin' || ! $validated['is_active'])
            && User::query()->where('role', 'admin')->where('is_active', true)->count() === 1) {
            throw ValidationException::withMessages([
                'role' => 'Le dernier administrateur actif ne peut pas être désactivé ou rétrogradé.',
            ]);
        }

        $user->update([
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        if ($request->user()->is($user) && ! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return to_route('login')->with('status', 'Votre compte a été désactivé.');
        }

        return to_route('users.index')->with('success', 'Les droits du compte ont été mis à jour.');
    }
}
