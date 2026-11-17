@extends('layouts.app')
@section('title', 'Modifier le bail')
@section('content')
<a href="{{ route('properties.show', $lease->property) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-700">← Retour au logement</a>
<section class="mx-auto mt-5 max-w-3xl rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Bail en cours</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Modifier le bail</h1>
    <p class="mt-1 text-sm text-slate-500">{{ $lease->property->title }} · {{ $lease->tenant?->name ?? $lease->tenant_name }}</p>
    <form method="POST" action="{{ route('leases.update', $lease) }}" class="mt-7 grid gap-4 sm:grid-cols-2">
        @csrf @method('PUT')
        <label class="text-sm font-semibold text-slate-700">Locataire<select name="tenant_id" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal">@foreach ($tenants as $tenant)<option value="{{ $tenant->id }}" @selected(old('tenant_id', $lease->tenant_id) == $tenant->id)>{{ $tenant->name }}{{ $tenant->email ? ' · '.$tenant->email : '' }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold text-slate-700">Loyer mensuel (FCFA)<input name="rent_amount" type="number" min="1" step="100" value="{{ old('rent_amount', $lease->rent_amount) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Début du bail<input name="start_date" type="date" max="{{ now()->toDateString() }}" value="{{ old('start_date', $lease->start_date->toDateString()) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Fin du bail <span class="font-normal text-slate-400">(facultatif)</span><input name="end_date" type="date" value="{{ old('end_date', $lease->end_date?->toDateString()) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Jour d’échéance<select name="due_day" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal">@foreach (range(1, 28) as $day)<option value="{{ $day }}" @selected(old('due_day', $lease->due_day) == $day)>{{ $day }} du mois</option>@endforeach</select></label>
        <div class="flex flex-wrap gap-3 pt-2 sm:col-span-2"><button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Enregistrer le bail</button><a href="{{ route('properties.show', $lease->property) }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Annuler</a></div>
    </form>
</section>
@endsection
