@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Votre activité</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Tableau de bord</h1>
        <p class="mt-2 text-slate-500">Suivez vos logements et les loyers encaissés.</p>
    </div>
    <a href="{{ route('properties.index') }}#ajouter" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">+ Ajouter un logement</a>
</div>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Logements au total</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $propertyCount }}</p><a href="{{ route('properties.index') }}" class="mt-3 inline-block text-sm font-medium text-blue-700">Gérer les logements →</a></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Logements occupés</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $occupiedCount }}</p><p class="mt-3 text-sm text-slate-500">Sur {{ $propertyCount }} logement(s)</p></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Paiements enregistrés</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $paymentCount }}</p><a href="{{ route('payments.index') }}" class="mt-3 inline-block text-sm font-medium text-blue-700">Voir les paiements →</a></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Montant encaissé</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format((float) $collectedAmount, 0, ',', ' ') }} <span class="text-base font-semibold">FCFA</span></p><p class="mt-3 text-sm text-slate-500">Tous les paiements reçus</p></article>
</section>

<section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div><h2 class="font-semibold text-slate-900">Derniers paiements</h2><p class="mt-1 text-sm text-slate-500">Les six paiements les plus récents</p></div>
        <a href="{{ route('payments.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Tout voir →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3 font-semibold">Locataire</th><th class="px-5 py-3 font-semibold">Logement</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold">Montant</th><th class="px-5 py-3 font-semibold">Quittance</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($recentPayments as $payment)
                    <tr class="hover:bg-slate-50"><td class="px-5 py-4 font-medium text-slate-900">{{ $payment->lease->tenant_name }}</td><td class="px-5 py-4 text-slate-600">{{ $payment->lease->property->title }}</td><td class="px-5 py-4 text-slate-600">{{ $payment->payment_date->format('d/m/Y') }}</td><td class="px-5 py-4 font-medium text-slate-900">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</td><td class="px-5 py-4"><a class="font-medium text-blue-700 hover:underline" href="{{ route('payments.show', $payment) }}">{{ $payment->receipt_number }}</a></td></tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center"><p class="font-medium text-slate-700">Aucun paiement pour le moment</p><p class="mt-1 text-sm text-slate-500">Les paiements enregistrés apparaîtront ici.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
