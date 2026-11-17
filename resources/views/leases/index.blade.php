@extends('layouts.app')
@section('title', 'Baux & locataires')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Suivi des locations</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Baux & locataires</h1><p class="mt-2 text-sm text-slate-500">Consultez les contrats actifs, les échéances et l’historique locatif.</p></div>
    @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
        <a href="{{ route('properties.index') }}?status=disponible" class="rounded-xl bg-brand-700 px-4 py-3 text-sm font-bold text-white hover:bg-brand-900">+ Créer un bail</a>
    @endif
</div>

<section class="mb-5 grid gap-3 sm:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Baux actifs</p><p class="mt-2 text-2xl font-bold text-brand-700">{{ $activeLeaseCount }}</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Revenus mensuels attendus</p><p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((float) $activeRentAmount, 0, ',', ' ') }} <span class="text-xs font-semibold">FCFA</span></p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Baux clôturés</p><p class="mt-2 text-2xl font-bold text-slate-600">{{ $endedLeaseCount }}</p></article>
</section>

<form method="GET" action="{{ route('leases.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="min-w-[220px] flex-1 text-xs font-bold text-slate-600">Rechercher
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Locataire, e-mail ou logement…" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
    </label>
    <label class="min-w-[160px] text-xs font-bold text-slate-600">État du bail
        <select name="status" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
            <option value="actif" @selected(request('status', 'actif') === 'actif')>Baux actifs</option>
            <option value="termine" @selected(request('status') === 'termine')>Baux terminés</option>
            <option value="tous" @selected(request('status') === 'tous')>Tous les baux</option>
        </select>
    </label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700">Filtrer</button><a href="{{ route('leases.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Effacer</a></div>
</form>

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[880px] text-left text-sm">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Locataire</th><th class="px-5 py-3 font-bold">Logement</th><th class="px-5 py-3 font-bold">Période</th><th class="px-5 py-3 font-bold">Loyer mensuel</th><th class="px-5 py-3 font-bold">Paiements</th><th class="px-5 py-3 font-bold">État</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($leases as $lease)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4"><span class="font-bold text-slate-900">{{ $lease->tenant_name }}</span><a href="mailto:{{ $lease->tenant_email }}" class="mt-0.5 block text-xs text-slate-500 hover:text-brand-700">{{ $lease->tenant_email }}</a></td>
                        <td class="px-5 py-4"><a href="{{ route('properties.show', $lease->property) }}" class="font-semibold text-slate-700 hover:text-brand-700">{{ $lease->property->title }}</a><span class="mt-0.5 block text-xs text-slate-400">{{ $lease->property->address }}</span></td>
                        <td class="px-5 py-4 text-slate-600">{{ $lease->start_date->format('d/m/Y') }}<span class="block text-xs text-slate-400">au {{ $lease->end_date?->format('d/m/Y') ?? '—' }}</span></td>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ number_format((float) $lease->rent_amount, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-4"><a href="{{ route('properties.show', $lease->property) }}" class="font-semibold text-brand-700 hover:underline">{{ $lease->payments_count }} reçu(s)</a></td>
                        <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $lease->status === 'actif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $lease->status === 'actif' ? 'Actif' : 'Terminé' }}</span></td>
                        <td class="px-5 py-4 text-right">
                            @if ($lease->status === 'actif' && auth()->user()->hasAnyRole('admin', 'gestionnaire'))
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('leases.edit', $lease) }}" class="rounded-lg px-2.5 py-2 text-xs font-bold text-brand-700 hover:bg-brand-50">Modifier</a>
                                    <form method="POST" action="{{ route('leases.terminate', $lease) }}" onsubmit="return confirm('Clôturer ce bail ?');">
                                        @csrf<button class="rounded-lg px-2.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Clôturer</button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('properties.show', $lease->property) }}" class="font-bold text-brand-700 hover:underline">Détails</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-14 text-center"><p class="font-bold text-slate-800">Aucun bail dans cette vue</p><p class="mt-1 text-sm text-slate-500">Créez un bail depuis la fiche d’un logement disponible.</p><a href="{{ route('properties.index') }}?status=disponible" class="mt-4 inline-flex rounded-lg bg-brand-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-brand-900">Voir les logements disponibles</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($leases->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $leases->links() }}</div>@endif
</section>
@endsection
