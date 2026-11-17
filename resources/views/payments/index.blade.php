@extends('layouts.app')
@section('title', 'Paiements')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Suivi des encaissements</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Paiements & quittances</h1><p class="mt-2 text-sm text-slate-500">Retrouvez chaque loyer enregistré et téléchargez sa quittance.</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Encaissé ce mois</p><p class="mt-1 text-lg font-bold text-slate-900">{{ number_format((float) $collectedThisMonth, 0, ',', ' ') }} FCFA</p></div>
</div>

<section class="mb-5 grid gap-3 sm:grid-cols-2">
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Total des encaissements</p><p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((float) $collectedAmount, 0, ',', ' ') }} <span class="text-xs font-semibold">FCFA</span></p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Quittances affichées</p><p class="mt-2 text-xl font-bold text-slate-900">{{ $payments->total() }}</p></article>
</section>

<form method="GET" action="{{ route('payments.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="min-w-[220px] flex-1 text-xs font-bold text-slate-600">Rechercher
        <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="N° de quittance, locataire, logement…" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
    </label>
    <label class="min-w-[150px] text-xs font-bold text-slate-600">Mode de paiement
        <select name="payment_method" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
            <option value="">Tous les modes</option>
            <option value="especes" @selected(($filters['payment_method'] ?? '') === 'especes')>Espèces</option>
            <option value="virement" @selected(($filters['payment_method'] ?? '') === 'virement')>Virement</option>
            <option value="mobile_money" @selected(($filters['payment_method'] ?? '') === 'mobile_money')>Mobile Money</option>
        </select>
    </label>
    <label class="text-xs font-bold text-slate-600">Du<input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100"></label>
    <label class="text-xs font-bold text-slate-600">Au<input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100"></label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700">Filtrer</button><a href="{{ route('payments.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Effacer</a></div>
</form>

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[790px] text-left text-sm">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Quittance</th><th class="px-5 py-3 font-bold">Locataire</th><th class="px-5 py-3 font-bold">Logement</th><th class="px-5 py-3 font-bold">Date</th><th class="px-5 py-3 font-bold">Mode</th><th class="px-5 py-3 text-right font-bold">Montant</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4"><a href="{{ route('payments.show', $payment) }}" class="font-bold text-brand-700 hover:underline">{{ $payment->receipt_number }}</a></td>
                        <td class="px-5 py-4 font-semibold text-slate-800">{{ $payment->lease->tenant?->name ?? $payment->lease->tenant_name }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $payment->lease->property->title }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-4"><span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                        <td class="px-5 py-4 text-right font-bold text-slate-900">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-4 text-right"><a href="{{ route('payments.show', $payment) }}" class="font-bold text-brand-700 hover:text-brand-900">Quittance →</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-14 text-center"><p class="font-bold text-slate-800">{{ request()->filled('search') || request()->filled('payment_method') || request()->filled('from') || request()->filled('to') ? 'Aucun paiement ne correspond aux filtres' : 'Aucun paiement enregistré' }}</p><p class="mt-1 text-sm text-slate-500">Les paiements et leurs quittances apparaîtront ici.</p><a class="mt-4 inline-flex text-sm font-bold text-brand-700 hover:underline" href="{{ route('properties.index') }}">Parcourir les logements →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($payments->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $payments->links() }}</div>@endif
</section>
@endsection
