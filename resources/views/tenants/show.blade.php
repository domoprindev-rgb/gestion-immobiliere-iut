@extends('layouts.app')
@section('title', $tenant->name)
@section('content')
<a href="{{ route('tenants.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-700">← Tous les locataires</a>
<section class="mt-4 flex flex-wrap items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Dossier locataire</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $tenant->name }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $tenant->email ?? 'Aucun e-mail' }} · {{ $tenant->phone ?? 'Aucun téléphone' }}</p>
        @if ($tenant->address)<p class="mt-1 text-sm text-slate-500">{{ $tenant->address }}</p>@endif
    </div>
    @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
        <a href="{{ route('tenants.edit', $tenant) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Modifier le dossier</a>
    @endif
</section>
@if ($tenant->notes)<section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold text-slate-900">Notes</h2><p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $tenant->notes }}</p></section>@endif
<section class="mt-7">
    <h2 class="mb-4 text-xl font-bold text-slate-950">Historique des baux</h2>
    <div class="grid gap-4">
        @forelse ($tenant->leases as $lease)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap justify-between gap-3">
                    <div><a href="{{ route('properties.show', $lease->property) }}" class="font-bold text-brand-700 hover:underline">{{ $lease->property->title }}</a><p class="mt-1 text-sm text-slate-500">{{ $lease->start_date->format('d/m/Y') }} — {{ $lease->end_date?->format('d/m/Y') ?? 'Durée indéterminée' }}</p></div>
                    <span class="h-fit rounded-full px-3 py-1 text-xs font-bold {{ $lease->status === 'actif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $lease->status === 'actif' ? 'Actif' : 'Terminé' }}</span>
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-800">{{ number_format((float) $lease->rent_amount, 0, ',', ' ') }} FCFA / mois · {{ $lease->payments->count() }} paiement(s)</p>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Aucun bail associé à ce dossier.</p>
        @endforelse
    </div>
</section>
@if (auth()->user()->isAdministrator() && $tenant->leases->isEmpty())
    <form method="POST" action="{{ route('tenants.destroy', $tenant) }}" class="mt-6" onsubmit="return confirm('Supprimer ce dossier ?');">@csrf @method('DELETE')<button class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700">Supprimer le dossier</button></form>
@endif
@endsection
