@extends('layouts.app')
@section('title', 'Modifier le locataire')
@section('content')
<a href="{{ route('tenants.show', $tenant) }}" class="text-sm font-semibold text-slate-500 hover:text-brand-700">← Retour au dossier</a>
<section class="mx-auto mt-5 max-w-3xl rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Dossier locatif</p>
    <h1 class="mt-2 text-2xl font-bold text-slate-950">Modifier le locataire</h1>
    <form method="POST" action="{{ route('tenants.update', $tenant) }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf @method('PUT')
        <label class="text-sm font-semibold text-slate-700">Nom complet<input name="name" value="{{ old('name', $tenant->name) }}" required maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal"></label>
        <label class="text-sm font-semibold text-slate-700">E-mail<input name="email" type="email" value="{{ old('email', $tenant->email) }}" maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal"></label>
        <label class="text-sm font-semibold text-slate-700">Téléphone<input name="phone" value="{{ old('phone', $tenant->phone) }}" maxlength="40" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal"></label>
        <label class="text-sm font-semibold text-slate-700">Adresse<input name="address" value="{{ old('address', $tenant->address) }}" maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal"></label>
        <label class="text-sm font-semibold text-slate-700 sm:col-span-2">Notes<textarea name="notes" rows="4" maxlength="2000" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal">{{ old('notes', $tenant->notes) }}</textarea></label>
        <div class="flex gap-3 sm:col-span-2"><button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white">Enregistrer</button><a href="{{ route('tenants.show', $tenant) }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600">Annuler</a></div>
    </form>
</section>
@endsection
