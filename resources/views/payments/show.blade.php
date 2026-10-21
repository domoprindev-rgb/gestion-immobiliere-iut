@extends('layouts.app')
@section('title', 'Quittance '.$payment->receipt_number)
@section('content')
<div class="print:hidden mb-5"><a href="{{ route('payments.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">← Retour aux paiements</a></div>
<article class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-12">
    <header class="flex flex-wrap items-start justify-between gap-5 border-b border-slate-200 pb-7">
        <div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">ImmoPilot · Gestion immobilière</p><h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Quittance de loyer</h1><p class="mt-2 text-sm text-slate-500">Paiement reçu le {{ $payment->payment_date->format('d/m/Y') }}</p></div>
        <div class="rounded-xl bg-slate-50 px-4 py-3 text-right"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Référence</p><p class="mt-1 font-bold text-slate-900">{{ $payment->receipt_number }}</p></div>
    </header>
    <section class="grid gap-6 py-7 sm:grid-cols-2"><div><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Reçu de</p><p class="mt-2 font-semibold text-slate-900">{{ $payment->lease->tenant_name }}</p><p class="text-sm text-slate-500">{{ $payment->lease->tenant_email }}</p></div><div><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pour le logement</p><p class="mt-2 font-semibold text-slate-900">{{ $payment->lease->property->title }}</p><p class="text-sm text-slate-500">{{ $payment->lease->property->address }}</p></div></section>
    <div class="rounded-xl bg-blue-50 p-5"><p class="text-sm font-medium text-blue-800">Montant reçu</p><p class="mt-1 text-3xl font-bold text-blue-950">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</p><p class="mt-3 text-sm text-blue-800">Mode de paiement : {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p></div>
    <p class="mt-6 text-sm leading-6 text-slate-600">La présente quittance atteste la réception du montant indiqué au titre du loyer du logement mentionné ci-dessus.</p>
    <div class="print:hidden mt-8 flex flex-wrap gap-3"><button onclick="window.print()" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Imprimer la quittance</button><a href="{{ route('properties.show', $payment->lease->property) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Retour au logement</a></div>
</article>
@endsection
