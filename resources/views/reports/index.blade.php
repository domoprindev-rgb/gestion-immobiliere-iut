@extends('layouts.app')
@section('title', 'Rapports')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Pilotage financier</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Rapports</h1><p class="mt-2 text-sm text-slate-500">Comparez les loyers attendus, les encaissements et les dépenses sur une période.</p></div>
    <a href="{{ route('reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="rounded-xl bg-brand-700 px-4 py-3 text-sm font-bold text-white hover:bg-brand-900">Télécharger le CSV</a>
</div>
<form method="GET" action="{{ route('reports.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="text-xs font-bold text-slate-600">Du<input name="from" type="date" value="{{ $from->toDateString() }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
    <label class="text-xs font-bold text-slate-600">Au<input name="to" type="date" value="{{ $to->toDateString() }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
    <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Actualiser</button>
</form>
<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Loyers prévus à échéance</p><p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((float) $expectedAmount, 0, ',', ' ') }} FCFA</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Reçu sur les loyers attendus</p><p class="mt-2 text-xl font-bold text-emerald-700">{{ number_format((float) $rentCollectedForInstallments, 0, ',', ' ') }} FCFA</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Solde restant sur ces échéances</p><p class="mt-2 text-xl font-bold text-rose-700">{{ number_format((float) $outstandingAmount, 0, ',', ' ') }} FCFA</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Dépenses enregistrées</p><p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((float) $expenseAmount, 0, ',', ' ') }} FCFA</p><p class="mt-1 text-xs text-slate-500">Encaissements datés de la période : {{ number_format((float) $collectedAmount, 0, ',', ' ') }} FCFA</p></article>
</section>
<section class="mt-6 grid gap-5 xl:grid-cols-2">
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-bold text-slate-900">Encaissements ({{ $payments->count() }})</h2></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[560px] text-left text-sm"><thead class="bg-slate-50 text-[11px] uppercase text-slate-500"><tr><th class="px-5 py-3">Date / référence</th><th class="px-5 py-3">Locataire / logement</th><th class="px-5 py-3 text-right">Montant</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($payments as $payment)<tr><td class="px-5 py-3 text-slate-600">{{ $payment->payment_date->format('d/m/Y') }}<span class="block text-xs text-slate-400">{{ $payment->receipt_number }}</span></td><td class="px-5 py-3 font-semibold text-slate-800">{{ $payment->lease->tenant?->name ?? $payment->lease->tenant_name }}<span class="block text-xs font-normal text-slate-500">{{ $payment->lease->property->title }}</span></td><td class="px-5 py-3 text-right font-bold">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</td></tr>@empty<tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Aucun encaissement sur cette période.</td></tr>@endforelse
        </tbody></table></div>
    </article>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-bold text-slate-900">Dépenses ({{ $expenses->count() }})</h2></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[500px] text-left text-sm"><thead class="bg-slate-50 text-[11px] uppercase text-slate-500"><tr><th class="px-5 py-3">Date / catégorie</th><th class="px-5 py-3">Logement / fournisseur</th><th class="px-5 py-3 text-right">Montant</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($expenses as $expense)<tr><td class="px-5 py-3 text-slate-600">{{ $expense->expense_date->format('d/m/Y') }}<span class="block text-xs text-slate-400">{{ $expense->category }}</span></td><td class="px-5 py-3 font-semibold text-slate-800">{{ $expense->property->title }}<span class="block text-xs font-normal text-slate-500">{{ $expense->vendor ?? $expense->description }}</span></td><td class="px-5 py-3 text-right font-bold text-rose-700">{{ number_format((float) $expense->amount, 0, ',', ' ') }} FCFA</td></tr>@empty<tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Aucune dépense sur cette période.</td></tr>@endforelse
        </tbody></table></div>
    </article>
</section>
@endsection
