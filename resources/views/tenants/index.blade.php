@extends('layouts.app')
@section('title', 'Locataires')
@section('content')
<div class="mb-7">
    <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Dossiers locatifs</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Locataires</h1>
    <p class="mt-2 text-sm text-slate-500">Centralisez les coordonnées et retrouvez l’historique de chaque locataire.</p>
</div>

@if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="font-bold text-slate-900">Ajouter un locataire</h2>
        <form method="POST" action="{{ route('tenants.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @csrf
            <label class="text-xs font-bold text-slate-600">Nom complet<input name="name" value="{{ old('name') }}" required maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
            <label class="text-xs font-bold text-slate-600">E-mail<input name="email" type="email" value="{{ old('email') }}" maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
            <label class="text-xs font-bold text-slate-600">Téléphone<input name="phone" value="{{ old('phone') }}" maxlength="40" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
            <label class="text-xs font-bold text-slate-600">Adresse<input name="address" value="{{ old('address') }}" maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
            <div class="sm:col-span-2 lg:col-span-4"><label class="block text-xs font-bold text-slate-600">Notes<textarea name="notes" rows="2" maxlength="2000" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal">{{ old('notes') }}</textarea></label></div>
            <div class="sm:col-span-2 lg:col-span-4"><button class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Créer le dossier</button></div>
        </form>
    </section>
@endif

<form method="GET" action="{{ route('tenants.index') }}" class="mb-5 flex gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="min-w-0 flex-1 text-xs font-bold text-slate-600">Rechercher un locataire
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, e-mail ou téléphone…" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal">
    </label>
    <button class="self-end rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Rechercher</button>
    <a href="{{ route('tenants.index') }}" class="self-end rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Effacer</a>
</form>

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Locataire</th><th class="px-5 py-3 font-bold">Coordonnées</th><th class="px-5 py-3 font-bold">Baux</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tenants as $tenant)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $tenant->name }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $tenant->email ?? '—' }}<span class="mt-0.5 block text-xs text-slate-400">{{ $tenant->phone ?? 'Aucun téléphone' }}</span></td>
                        <td class="px-5 py-4 text-slate-600">{{ $tenant->leases_count }}</td>
                        <td class="px-5 py-4 text-right"><a href="{{ route('tenants.show', $tenant) }}" class="font-bold text-brand-700 hover:underline">Dossier →</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-slate-500">Aucun locataire ne correspond à la recherche.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($tenants->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $tenants->links() }}</div>@endif
</section>
@endsection
