@extends('layouts.app')
@section('title', 'Échéances de loyer')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Loyers et impayés</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Échéances de loyer</h1><p class="mt-2 text-sm text-slate-500">Suivez les montants dus, les règlements partiels et les retards.</p></div>
    <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-3.5"><p class="text-[11px] font-bold uppercase tracking-wider text-rose-700">En retard</p><p class="mt-1 text-xl font-bold text-rose-900">{{ $overdueCount }} échéance(s)</p></div>
</div>
<section class="mb-5 grid gap-3 sm:grid-cols-2">
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Loyers planifiés</p><p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((float) $expectedAmount, 0, ',', ' ') }} FCFA</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">Montant déjà reçu sur ces échéances</p><p class="mt-2 text-xl font-bold text-emerald-700">{{ number_format((float) $collectedAmount, 0, ',', ' ') }} FCFA</p></article>
</section>
<form method="GET" action="{{ route('rents.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="min-w-[220px] flex-1 text-xs font-bold text-slate-600">Rechercher<input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Locataire ou logement…" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal"></label>
    <label class="min-w-[160px] text-xs font-bold text-slate-600">État<select name="status" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-normal"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Toutes</option><option value="due" @selected(($filters['status'] ?? '') === 'due')>À payer / partielle</option><option value="overdue" @selected(($filters['status'] ?? '') === 'overdue')>En retard</option><option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Payée</option></select></label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Filtrer</button><a href="{{ route('rents.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Effacer</a></div>
</form>
<section class="grid gap-4">
    @forelse ($installments as $installment)
        @php($lease = $installment->lease)
        @php($outstanding = $installment->outstanding_amount)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div><a href="{{ route('properties.show', $lease->property) }}" class="font-bold text-slate-900 hover:text-brand-700">{{ $lease->property->title }}</a><p class="mt-1 text-sm text-slate-600">{{ $lease->tenant?->name ?? $lease->tenant_name }}</p><p class="mt-1 text-xs text-slate-400">{{ $installment->period_start->locale('fr')->translatedFormat('F Y') }} · échéance le {{ $installment->due_date->format('d/m/Y') }}</p></div>
                <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $installment->payment_status === 'Payée' ? 'bg-emerald-50 text-emerald-700' : ($installment->payment_status === 'En retard' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-800') }}">{{ $installment->payment_status }}</span>
            </div>
            <div class="mt-4 grid gap-3 border-t border-slate-100 pt-4 text-sm sm:grid-cols-3">
                <p><span class="block text-xs text-slate-400">Montant dû</span><strong class="mt-1 block">{{ number_format((float) $installment->amount_due, 0, ',', ' ') }} FCFA</strong></p>
                <p><span class="block text-xs text-slate-400">Déjà reçu</span><strong class="mt-1 block text-emerald-700">{{ number_format((float) ($installment->paid_amount ?? 0), 0, ',', ' ') }} FCFA</strong></p>
                <p><span class="block text-xs text-slate-400">Solde restant</span><strong class="mt-1 block {{ $outstanding > 0 ? 'text-rose-700' : 'text-slate-800' }}">{{ number_format($outstanding, 0, ',', ' ') }} FCFA</strong></p>
            </div>
            @if ($outstanding > 0 && $lease->status === 'actif' && auth()->user()->hasAnyRole('admin', 'gestionnaire', 'comptable'))
                <form method="POST" action="{{ route('payments.store') }}" class="mt-4 grid items-end gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-4">
                    @csrf
                    <input type="hidden" name="rent_installment_id" value="{{ $installment->id }}">
                    <label class="text-xs font-bold text-slate-600">Montant reçu (FCFA)<input name="amount" type="number" min="1" max="{{ $outstanding }}" step="0.01" value="{{ old('rent_installment_id') == $installment->id ? old('amount') : $outstanding }}" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal"></label>
                    <label class="text-xs font-bold text-slate-600">Date du paiement<input name="payment_date" type="date" max="{{ today()->toDateString() }}" value="{{ old('rent_installment_id') == $installment->id ? old('payment_date') : today()->toDateString() }}" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal"></label>
                    <label class="text-xs font-bold text-slate-600">Mode<select name="payment_method" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal"><option value="especes">Espèces</option><option value="virement">Virement</option><option value="mobile_money">Mobile Money</option></select></label>
                    <button class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Enregistrer le paiement</button>
                </form>
            @endif
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center"><p class="font-bold text-slate-800">Aucune échéance dans cette vue</p><p class="mt-1 text-sm text-slate-500">Les échéances actives sont générées automatiquement.</p></div>
    @endforelse
</section>
@if ($installments->hasPages())<div class="mt-5 rounded-2xl border border-slate-200 bg-white px-5 py-4">{{ $installments->links() }}</div>@endif
@endsection
