@extends('layouts.app')
@section('title', 'Quittance '.$payment->receipt_number)
@section('content')
<div class="print:hidden mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('payments.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-700">← Retour aux paiements</a>
    <button onclick="window.print()" class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Imprimer / enregistrer en PDF</button>
</div>
<article class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
    @php($fullyPaid = $payment->installment && $payment->installment->outstanding_amount <= 0)
    <header class="flex flex-wrap items-start justify-between gap-5 border-b border-slate-200 pb-7">
        <div><div class="flex items-center gap-2 text-sm font-bold text-brand-700"><span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-700 text-sm text-white">I</span> ImmoPilot</div><p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Justificatif de paiement</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $fullyPaid ? 'Quittance de loyer' : 'Reçu de paiement' }}</h1><p class="mt-2 text-sm text-slate-500">Paiement reçu le {{ $payment->payment_date->format('d/m/Y') }}</p></div>
        <div class="rounded-xl bg-slate-50 px-4 py-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Référence</p><p class="mt-1 font-bold text-slate-900">{{ $payment->receipt_number }}</p></div>
    </header>
    <section class="grid gap-6 py-7 sm:grid-cols-2">
        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Reçu de</p><p class="mt-2 font-bold text-slate-900">{{ $payment->lease->tenant?->name ?? $payment->lease->tenant_name }}</p><p class="mt-0.5 text-sm text-slate-500">{{ $payment->lease->tenant?->email ?? $payment->lease->tenant_email }}</p></div>
        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pour le logement</p><p class="mt-2 font-bold text-slate-900">{{ $payment->lease->property->title }}</p><p class="mt-0.5 text-sm text-slate-500">{{ $payment->lease->property->address }}</p></div>
    </section>
    <div class="rounded-2xl bg-brand-50 p-5 sm:p-6"><p class="text-xs font-bold uppercase tracking-wider text-brand-700">Montant reçu</p><p class="mt-2 text-3xl font-bold tracking-tight text-brand-900">{{ number_format((float) $payment->amount, 0, ',', ' ') }} <span class="text-base">FCFA</span></p><p class="mt-3 text-sm text-brand-800">Mode de paiement : <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></p>@if ($payment->installment)<p class="mt-2 text-sm text-brand-800">Période : <span class="font-semibold">{{ $payment->installment->period_start->locale('fr')->translatedFormat('F Y') }}</span> · Solde restant : <span class="font-semibold">{{ number_format($payment->installment->outstanding_amount, 0, ',', ' ') }} FCFA</span></p>@endif</div>
    <p class="mt-6 text-sm leading-6 text-slate-600">@if ($fullyPaid)Cette quittance atteste le règlement intégral du loyer de la période indiquée.@else Ce reçu atteste uniquement le montant effectivement reçu ; un solde peut rester dû.@endif</p>
    <div class="mt-12 grid gap-8 sm:grid-cols-2">
        <div><p class="text-xs font-semibold text-slate-500">Signature du locataire</p><div class="mt-12 border-b border-slate-200"></div></div>
        <div><p class="text-xs font-semibold text-slate-500">Signature du gestionnaire</p><div class="mt-12 border-b border-slate-200"></div></div>
    </div>
    <div class="print:hidden mt-8 flex flex-wrap gap-4 border-t border-slate-100 pt-5"><a href="{{ route('payments.receipt', $payment) }}" class="text-sm font-bold text-brand-700 hover:underline">Télécharger le PDF →</a><a href="{{ route('properties.show', $payment->lease->property) }}" class="text-sm font-bold text-brand-700 hover:underline">Voir le dossier du logement →</a></div>
</article>
@endsection
