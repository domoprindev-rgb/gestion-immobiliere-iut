@extends('layouts.app')
@section('title', 'Vue d’ensemble')
@section('content')
@php($occupancyRate = $propertyCount > 0 ? (int) round($occupiedCount / $propertyCount * 100) : 0)
<div class="mb-8 flex flex-wrap items-end justify-between gap-5">
    <div>
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Votre activité immobilière</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Bonjour, bienvenue 👋</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Retrouvez en un coup d’œil l’état de votre patrimoine, vos loyers et les échéances importantes.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('properties.index') }}#ajouter" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-900/15 hover:bg-brand-900">
            <span class="text-lg leading-none">+</span> Ajouter un logement
        </a>
        <a href="{{ route('leases.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Voir les baux</a>
    </div>
</div>

<section class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-500">Logements</p><span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-lg text-blue-700">⌂</span></div>
        <p class="mt-5 text-3xl font-bold tracking-tight text-slate-950">{{ $propertyCount }}</p>
        <p class="mt-2 text-xs font-medium text-slate-500">{{ $availableCount }} disponible(s) <span class="px-1 text-slate-300">·</span> {{ $occupiedCount }} occupé(s)</p>
        <a href="{{ route('properties.index') }}" class="mt-4 inline-flex text-xs font-bold text-brand-700 hover:text-brand-900">Gérer le patrimoine <span class="ml-1">→</span></a>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-500">Baux en cours</p><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-lg text-violet-700">▤</span></div>
        <p class="mt-5 text-3xl font-bold tracking-tight text-slate-950">{{ $activeLeaseCount }}</p>
        <p class="mt-2 text-xs font-medium text-slate-500">Loyers attendus / mois</p>
        <p class="mt-0.5 text-sm font-bold text-slate-800">{{ number_format((float) $expectedMonthlyRent, 0, ',', ' ') }} FCFA</p>
        <a href="{{ route('leases.index') }}" class="mt-3 inline-flex text-xs font-bold text-brand-700 hover:text-brand-900">Suivre les baux <span class="ml-1">→</span></a>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-500">Encaissé ce mois</p><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-lg text-emerald-700">↗</span></div>
        <p class="mt-5 break-words text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $collectedThisMonth, 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></p>
        <p class="mt-2 text-xs font-medium text-slate-500">Sur {{ now()->locale('fr')->translatedFormat('F Y') }}</p>
        <a href="{{ route('payments.index') }}" class="mt-4 inline-flex text-xs font-bold text-brand-700 hover:text-brand-900">Consulter les paiements <span class="ml-1">→</span></a>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-500">Taux d’occupation</p><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-lg text-amber-700">◒</span></div>
        <p class="mt-5 text-3xl font-bold tracking-tight text-slate-950">{{ $occupancyRate }}<span class="ml-1 text-xl">%</span></p>
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-600" style="width: {{ $occupancyRate }}%"></div></div>
        <p class="mt-2 text-xs font-medium text-slate-500">{{ $occupiedCount }} logement(s) loué(s) sur {{ $propertyCount }}</p>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-500">Loyers en retard</p><span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-50 text-lg text-rose-700">!</span></div>
        <p class="mt-5 text-3xl font-bold tracking-tight text-slate-950">{{ $overdueInstallmentCount }}</p>
        <p class="mt-2 text-xs font-medium text-slate-500">Échéance dépassée avec un solde</p>
        <a href="{{ route('rents.index', ['status' => 'overdue']) }}" class="mt-4 inline-flex text-xs font-bold text-brand-700 hover:text-brand-900">Relancer le suivi <span class="ml-1">→</span></a>
    </article>
</section>

<section class="mt-6 grid gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.8fr)]">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><h2 class="font-bold text-slate-900">Revenus locatifs</h2><p class="mt-1 text-sm text-slate-500">Montants encaissés sur les six derniers mois</p></div>
            <span class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700">FCFA</span>
        </div>
        <div class="mt-8 grid h-48 grid-cols-6 items-end gap-3 sm:gap-5">
            @foreach ($monthlyIncome as $month)
                @php($barHeight = $month['amount'] > 0 ? max(8, (int) round($month['amount'] / $monthlyIncomeMax * 100)) : 3)
                <div class="flex h-full flex-col items-center justify-end gap-2">
                    <span class="text-center text-[10px] font-semibold text-slate-500 sm:text-xs">{{ $month['amount'] > 0 ? number_format($month['amount'] / 1000, 0, ',', ' ').'k' : '—' }}</span>
                    <div class="flex h-[72%] w-full items-end rounded-t-lg bg-slate-50"><div class="w-full rounded-t-lg bg-brand-600 transition-all" style="height: {{ $barHeight }}%"></div></div>
                    <span class="text-xs font-semibold capitalize text-slate-500">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div><h2 class="font-bold text-slate-900">Échéances à suivre</h2><p class="mt-1 text-sm text-slate-500">Fin de bail dans les 30 prochains jours</p></div>
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-50 text-amber-700">!</span>
        </div>
        <div class="mt-5 divide-y divide-slate-100">
            @forelse ($expiringLeases as $lease)
                <a href="{{ route('properties.show', $lease->property) }}" class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0 hover:text-brand-700">
                    <span class="min-w-0"><span class="block truncate text-sm font-semibold text-slate-800">{{ $lease->tenant_name }}</span><span class="mt-0.5 block truncate text-xs text-slate-500">{{ $lease->property->title }}</span></span>
                    <span class="shrink-0 rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-800">{{ $lease->end_date->format('d/m/Y') }}</span>
                </a>
            @empty
                <div class="py-7 text-center">
                    <span class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-emerald-50 text-lg text-emerald-700">✓</span>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Tout est à jour</p>
                    <p class="mt-1 text-xs text-slate-500">Aucune fin de bail imminente.</p>
                </div>
            @endforelse
        </div>
        <a href="{{ route('leases.index') }}" class="mt-5 inline-flex text-xs font-bold text-brand-700 hover:text-brand-900">Tous les baux <span class="ml-1">→</span></a>
    </article>
</section>

<section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
        <div><h2 class="font-bold text-slate-900">Derniers paiements</h2><p class="mt-1 text-sm text-slate-500">Les encaissements les plus récents</p></div>
        <a href="{{ route('payments.index') }}" class="rounded-lg px-3 py-2 text-xs font-bold text-brand-700 hover:bg-brand-50">Tout l’historique →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[690px] text-left text-sm">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold sm:px-6">Locataire</th><th class="px-5 py-3 font-bold">Logement</th><th class="px-5 py-3 font-bold">Date</th><th class="px-5 py-3 text-right font-bold sm:px-6">Montant</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($recentPayments as $payment)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4 sm:px-6"><span class="font-semibold text-slate-800">{{ $payment->lease->tenant_name }}</span><span class="mt-0.5 block text-xs text-slate-400">{{ $payment->receipt_number }}</span></td>
                        <td class="px-5 py-4 text-slate-600">{{ $payment->lease->property->title }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 text-right font-bold text-slate-900 sm:px-6">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center">
                        <p class="font-semibold text-slate-700">Aucun encaissement pour le moment</p>
                        <p class="mt-1 text-sm text-slate-500">Créez un logement, ajoutez un bail et enregistrez votre premier loyer.</p>
                        <a href="{{ route('properties.index') }}#ajouter" class="mt-4 inline-flex rounded-lg bg-brand-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-brand-900">Commencer maintenant</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
