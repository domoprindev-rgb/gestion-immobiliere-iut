@extends('auth.layout')
@section('title', 'Connexion')
@section('content')
<section class="rounded-3xl border border-white/10 bg-white p-6 shadow-2xl sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Espace sécurisé</p>
    <h1 class="mt-2 text-2xl font-bold text-slate-950">Connexion</h1>
    <p class="mt-1 text-sm text-slate-500">Connectez-vous pour gérer votre portefeuille immobilier.</p>
    <form method="POST" action="{{ route('login.store') }}" class="mt-6 grid gap-4">
        @csrf
        <label class="text-sm font-semibold text-slate-700">Adresse e-mail<input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Mot de passe<input name="password" type="password" autocomplete="current-password" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="flex items-center gap-2 text-sm text-slate-600"><input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-brand-700 focus:ring-brand-500"> Rester connecté</label>
        <button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Se connecter</button>
    </form>
    <a href="{{ route('password.request') }}" class="mt-5 block text-center text-sm font-semibold text-brand-700 hover:underline">Mot de passe oublié ?</a>
</section>
@endsection
