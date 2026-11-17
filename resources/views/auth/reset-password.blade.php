@extends('auth.layout')
@section('title', 'Choisir un nouveau mot de passe')
@section('content')
<section class="rounded-3xl border border-white/10 bg-white p-6 shadow-2xl sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Sécurité du compte</p>
    <h1 class="mt-2 text-2xl font-bold text-slate-950">Choisir un nouveau mot de passe</h1>
    <form method="POST" action="{{ route('password.update') }}" class="mt-6 grid gap-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="text-sm font-semibold text-slate-700">Adresse e-mail<input name="email" type="email" value="{{ old('email', $email) }}" autocomplete="username" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Nouveau mot de passe<input name="password" type="password" autocomplete="new-password" required minlength="12" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Confirmer le mot de passe<input name="password_confirmation" type="password" autocomplete="new-password" required minlength="12" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Réinitialiser le mot de passe</button>
    </form>
</section>
@endsection
