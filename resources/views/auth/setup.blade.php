@extends('auth.layout')
@section('title', 'Créer le compte administrateur')
@section('content')
<section class="rounded-3xl border border-white/10 bg-white p-6 shadow-2xl sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Première installation</p>
    <h1 class="mt-2 text-2xl font-bold text-slate-950">Créer le compte administrateur</h1>
    <p class="mt-1 text-sm leading-6 text-slate-500">Ce formulaire n’est disponible que tant qu’aucun utilisateur n’existe. Il sera fermé après la création du premier compte.</p>
    <form method="POST" action="{{ route('setup.store') }}" class="mt-6 grid gap-4">
        @csrf
        <label class="text-sm font-semibold text-slate-700">Nom complet<input name="name" value="{{ old('name') }}" autocomplete="name" required autofocus maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Adresse e-mail<input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Mot de passe (12 caractères minimum)<input name="password" type="password" autocomplete="new-password" required minlength="12" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Confirmer le mot de passe<input name="password_confirmation" type="password" autocomplete="new-password" required minlength="12" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Créer mon espace sécurisé</button>
    </form>
</section>
@endsection
