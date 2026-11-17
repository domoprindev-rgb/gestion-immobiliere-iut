@extends('layouts.app')
@section('title', 'Logements')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Votre patrimoine</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Logements</h1><p class="mt-2 text-sm text-slate-500">Centralisez les informations et la disponibilité de vos biens.</p></div>
    @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
        <a href="#ajouter" class="rounded-xl bg-brand-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-900/15 hover:bg-brand-900">+ Ajouter un logement</a>
    @endif
</div>

<section class="mb-5 grid gap-3 sm:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Tous les logements</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $propertyCount }}</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Disponibles</p><p class="mt-2 text-2xl font-bold text-emerald-700">{{ $availableCount }}</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Occupés</p><p class="mt-2 text-2xl font-bold text-blue-700">{{ $occupiedCount }}</p></article>
</section>

<form method="GET" action="{{ route('properties.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="min-w-[220px] flex-1 text-xs font-bold text-slate-600">Rechercher un logement
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, adresse ou type…" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal text-slate-800 outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
    </label>
    <label class="min-w-[160px] text-xs font-bold text-slate-600">Disponibilité
        <select name="status" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal text-slate-700 outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
            <option value="">Tous les logements</option>
            <option value="disponible" @selected(request('status') === 'disponible')>Disponibles</option>
            <option value="loue" @selected(request('status') === 'loue')>Occupés</option>
        </select>
    </label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700">Filtrer</button><a href="{{ route('properties.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Effacer</a></div>
</form>

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Logement</th><th class="px-5 py-3 font-bold">Adresse</th><th class="px-5 py-3 font-bold">Type</th><th class="px-5 py-3 font-bold">Loyer mensuel</th><th class="px-5 py-3 font-bold">Disponibilité</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($properties as $property)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4"><a href="{{ route('properties.show', $property) }}" class="font-bold text-slate-900 hover:text-brand-700">{{ $property->title }}</a></td>
                        <td class="px-5 py-4 text-slate-600">{{ $property->address }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ ucfirst($property->type) }}</td>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ number_format((float) $property->price, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $property->status === 'disponible' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $property->status === 'disponible' ? 'Disponible' : 'Occupé' }}</span></td>
                        <td class="px-5 py-4 text-right"><a class="font-bold text-brand-700 hover:text-brand-900" href="{{ route('properties.show', $property) }}">Ouvrir →</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-xl text-slate-500">⌂</span><p class="mt-4 font-bold text-slate-800">{{ request()->filled('search') || request()->filled('status') ? 'Aucun résultat pour ces filtres' : 'Votre portefeuille est encore vide' }}</p><p class="mt-1 text-sm text-slate-500">Ajoutez ou recherchez un bien pour commencer.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($properties->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $properties->links() }}</div>@endif
</section>

@if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
<section id="ajouter" class="mt-6 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5"><p class="text-xs font-bold uppercase tracking-wider text-brand-700">Nouvelle fiche</p><h2 class="mt-1 text-xl font-bold text-slate-900">Ajouter un logement</h2><p class="mt-1 text-sm text-slate-500">Renseignez les informations principales du bien.</p></div>
    <form method="POST" action="{{ route('properties.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        <label class="text-sm font-semibold text-slate-700">Nom du logement<input name="title" value="{{ old('title') }}" placeholder="Ex. Appartement Bonapriso" maxlength="150" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Adresse<input name="address" value="{{ old('address') }}" placeholder="Quartier, ville" maxlength="255" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Loyer mensuel (FCFA)<input name="price" type="number" min="0" step="100" value="{{ old('price') }}" placeholder="150000" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Type de bien<input name="type" value="{{ old('type') }}" placeholder="Appartement, studio, maison…" maxlength="80" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <div class="sm:col-span-2"><button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Créer la fiche logement</button></div>
    </form>
</section>
@endif
@endsection
