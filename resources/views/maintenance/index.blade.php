@extends('layouts.app')
@section('title', 'Interventions')
@section('content')
<div class="mb-7"><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Entretien du patrimoine</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Interventions & maintenance</h1><p class="mt-2 text-sm text-slate-500">Déclarez les incidents et suivez leur priorité, leur état et leur coût.</p></div>
@if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
<section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <h2 class="font-bold text-slate-900">Déclarer une intervention</h2>
    <form method="POST" action="{{ route('maintenance.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @csrf
        <label class="text-xs font-bold text-slate-600">Logement<select name="property_id" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Choisir…</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->title }}</option>@endforeach</select></label>
        <label class="text-xs font-bold text-slate-600">Titre<input name="title" value="{{ old('title') }}" maxlength="150" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Catégorie<input name="category" value="{{ old('category') }}" maxlength="80" placeholder="Plomberie, électricité…" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Priorité<select name="priority" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="normale">Normale</option><option value="basse">Basse</option><option value="haute">Haute</option><option value="urgente">Urgente</option></select></label>
        <label class="text-xs font-bold text-slate-600">Coût estimé (FCFA)<input name="estimated_cost" type="number" min="0" step="0.01" value="{{ old('estimated_cost') }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Signalé le<input name="reported_at" type="date" max="{{ today()->toDateString() }}" value="{{ old('reported_at', today()->toDateString()) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600 sm:col-span-2 lg:col-span-3">Description<textarea name="description" rows="3" maxlength="3000" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal">{{ old('description') }}</textarea></label>
        <div class="sm:col-span-2 lg:col-span-3"><button class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Créer la demande</button></div>
    </form>
</section>
@endif
<form method="GET" action="{{ route('maintenance.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="text-xs font-bold text-slate-600">État<select name="status" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Tous</option><option value="nouvelle" @selected(($filters['status'] ?? '') === 'nouvelle')>Nouvelle</option><option value="en_cours" @selected(($filters['status'] ?? '') === 'en_cours')>En cours</option><option value="terminee" @selected(($filters['status'] ?? '') === 'terminee')>Terminée</option></select></label>
    <label class="text-xs font-bold text-slate-600">Logement<select name="property_id" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Tous</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(($filters['property_id'] ?? '') == $property->id)>{{ $property->title }}</option>@endforeach</select></label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Filtrer</button><a href="{{ route('maintenance.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Effacer</a></div>
</form>
<section class="grid gap-4">
    @forelse ($requests as $maintenanceRequest)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $maintenanceRequest->property->title }} · {{ $maintenanceRequest->category }}</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $maintenanceRequest->title }}</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $maintenanceRequest->description }}</p><p class="mt-3 text-xs text-slate-400">Signalé le {{ $maintenanceRequest->reported_at->format('d/m/Y') }}@if ($maintenanceRequest->resolved_at) · Résolu le {{ $maintenanceRequest->resolved_at->format('d/m/Y') }}@endif</p></div>
                <div class="flex gap-2"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $maintenanceRequest->priority === 'urgente' ? 'bg-rose-50 text-rose-700' : ($maintenanceRequest->priority === 'haute' ? 'bg-amber-50 text-amber-800' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($maintenanceRequest->priority) }}</span><span class="rounded-full px-3 py-1 text-xs font-bold {{ $maintenanceRequest->status === 'terminee' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ str_replace('_', ' ', ucfirst($maintenanceRequest->status)) }}</span></div>
            </div>
            <div class="mt-4 flex flex-wrap items-end justify-between gap-3 border-t border-slate-100 pt-4">
                <p class="text-sm text-slate-500">Estimé : <strong class="text-slate-800">{{ $maintenanceRequest->estimated_cost !== null ? number_format((float) $maintenanceRequest->estimated_cost, 0, ',', ' ').' FCFA' : '—' }}</strong> · Réel : <strong class="text-slate-800">{{ $maintenanceRequest->actual_cost !== null ? number_format((float) $maintenanceRequest->actual_cost, 0, ',', ' ').' FCFA' : '—' }}</strong></p>
                @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
                    <form method="POST" action="{{ route('maintenance.update', $maintenanceRequest) }}" class="flex flex-wrap items-end gap-2">@csrf @method('PUT')
                        <label class="text-[10px] font-bold text-slate-500">État<select name="status" class="mt-1 block rounded-lg border border-slate-200 px-2 py-2 text-xs"><option value="nouvelle" @selected($maintenanceRequest->status === 'nouvelle')>Nouvelle</option><option value="en_cours" @selected($maintenanceRequest->status === 'en_cours')>En cours</option><option value="terminee" @selected($maintenanceRequest->status === 'terminee')>Terminée</option></select></label>
                        <label class="text-[10px] font-bold text-slate-500">Priorité<select name="priority" class="mt-1 block rounded-lg border border-slate-200 px-2 py-2 text-xs">@foreach (['basse', 'normale', 'haute', 'urgente'] as $priority)<option value="{{ $priority }}" @selected($maintenanceRequest->priority === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></label>
                        <label class="text-[10px] font-bold text-slate-500">Coût réel<input name="actual_cost" type="number" min="0" step="0.01" value="{{ $maintenanceRequest->actual_cost }}" class="mt-1 block w-28 rounded-lg border border-slate-200 px-2 py-2 text-xs"></label>
                        <label class="text-[10px] font-bold text-slate-500">Estimation<input name="estimated_cost" type="number" min="0" step="0.01" value="{{ $maintenanceRequest->estimated_cost }}" class="mt-1 block w-28 rounded-lg border border-slate-200 px-2 py-2 text-xs"></label>
                        <button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white">Mettre à jour</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Aucune intervention ne correspond aux filtres.</div>
    @endforelse
</section>
@if ($requests->hasPages())<div class="mt-5 rounded-2xl border border-slate-200 bg-white px-5 py-4">{{ $requests->links() }}</div>@endif
@endsection
