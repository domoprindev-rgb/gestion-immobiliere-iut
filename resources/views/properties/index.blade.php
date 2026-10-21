@extends('layouts.app')
@section('title', 'Logements')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Patrimoine</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Logements</h1><p class="mt-2 text-slate-500">{{ $properties->total() }} logement(s) dans votre portefeuille.</p></div>
    <a href="#ajouter" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">+ Ajouter un logement</a>
</div>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3 font-semibold">Logement</th><th class="px-5 py-3 font-semibold">Adresse</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Loyer mensuel</th><th class="px-5 py-3 font-semibold">Disponibilité</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($properties as $property)
                    <tr class="hover:bg-slate-50"><td class="px-5 py-4 font-semibold text-slate-900">{{ $property->title }}</td><td class="px-5 py-4 text-slate-600">{{ $property->address }}</td><td class="px-5 py-4 text-slate-600">{{ ucfirst($property->type) }}</td><td class="px-5 py-4 font-medium text-slate-900">{{ number_format((float) $property->price, 0, ',', ' ') }} FCFA</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $property->status === 'disponible' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $property->status === 'disponible' ? 'Disponible' : 'Loué' }}</span></td><td class="px-5 py-4 text-right"><a class="font-semibold text-blue-700 hover:underline" href="{{ route('properties.show', $property) }}">Détails →</a></td></tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center"><p class="font-medium text-slate-700">Aucun logement enregistré</p><p class="mt-1 text-sm text-slate-500">Ajoutez votre premier logement avec le formulaire ci-dessous.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($properties->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $properties->links() }}</div>@endif
</section>

<section id="ajouter" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5"><h2 class="text-lg font-semibold text-slate-900">Ajouter un logement</h2><p class="mt-1 text-sm text-slate-500">Les champs marqués sont requis pour créer la fiche.</p></div>
    <form method="POST" action="{{ route('properties.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        <label class="text-sm font-medium text-slate-700">Nom du logement<input name="title" value="{{ old('title') }}" placeholder="Ex. Appartement Bonapriso" maxlength="150" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></label>
        <label class="text-sm font-medium text-slate-700">Adresse<input name="address" value="{{ old('address') }}" placeholder="Quartier, ville" maxlength="255" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></label>
        <label class="text-sm font-medium text-slate-700">Loyer mensuel (FCFA)<input name="price" type="number" min="0" step="100" value="{{ old('price') }}" placeholder="150000" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></label>
        <label class="text-sm font-medium text-slate-700">Type<input name="type" value="{{ old('type') }}" placeholder="Appartement, studio, maison…" maxlength="80" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></label>
        <div class="sm:col-span-2"><button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Enregistrer le logement</button></div>
    </form>
</section>
@endsection
