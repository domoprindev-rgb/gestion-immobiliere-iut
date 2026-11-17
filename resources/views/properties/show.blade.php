@extends('layouts.app')
@section('title', $property->title)
@section('content')
<a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-700">← Retour aux logements</a>

<section class="mt-4 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-brand-900 px-5 py-7 text-white sm:px-8 sm:py-9">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-300">{{ ucfirst($property->type) }}</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $property->title }}</h1>
                <p class="mt-2 flex items-center gap-2 text-sm text-slate-300"><span>⌖</span>{{ $property->address }}</p>
            </div>
            <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $property->status === 'disponible' ? 'bg-emerald-400/15 text-emerald-200 ring-1 ring-emerald-300/30' : 'bg-blue-400/15 text-blue-200 ring-1 ring-blue-300/30' }}">{{ $property->status === 'disponible' ? 'Disponible à la location' : 'Actuellement occupé' }}</span>
        </div>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-8">
        <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Loyer mensuel indicatif</p><p class="mt-1 text-2xl font-bold text-slate-950">{{ number_format((float) $property->price, 0, ',', ' ') }} <span class="text-sm font-semibold text-slate-500">FCFA</span></p></div>
        @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
            <a href="{{ route('properties.edit', $property) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Modifier la fiche</a>
        @endif
    </div>
</section>

@if ($property->status === 'disponible' && auth()->user()->hasAnyRole('admin', 'gestionnaire'))
    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex items-start gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-50 text-lg text-brand-700">＋</span>
            <div><h2 class="text-lg font-bold text-slate-900">Créer un bail</h2><p class="mt-1 text-sm text-slate-500">Associez un locataire à ce logement et définissez les conditions de location.</p></div>
        </div>
        <form method="POST" action="{{ route('leases.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf<input type="hidden" name="property_id" value="{{ $property->id }}">
            <label class="text-xs font-bold text-slate-600">Locataire<select name="tenant_id" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"><option value="">Choisir un dossier…</option>@foreach ($tenants as $tenant)<option value="{{ $tenant->id }}" @selected(old('tenant_id') == $tenant->id)>{{ $tenant->name }}{{ $tenant->email ? ' · '.$tenant->email : '' }}</option>@endforeach</select></label>
            <label class="text-xs font-bold text-slate-600">Loyer mensuel (FCFA)<input name="rent_amount" type="number" min="1" step="100" value="{{ old('rent_amount', $property->price) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
            <label class="text-xs font-bold text-slate-600">Début du bail<input name="start_date" type="date" max="{{ now()->toDateString() }}" value="{{ old('start_date', now()->toDateString()) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
            <label class="text-xs font-bold text-slate-600">Fin du bail <span class="font-normal text-slate-400">(facultatif)</span><input name="end_date" type="date" value="{{ old('end_date') }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
            <label class="text-xs font-bold text-slate-600">Jour d’échéance<select name="due_day" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal">@foreach (range(1, 28) as $day)<option value="{{ $day }}" @selected(old('due_day', 5) == $day)>{{ $day }} du mois</option>@endforeach</select></label>
            <div class="flex items-end"><button class="w-full rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Créer le bail</button></div>
        </form>
        @if ($tenants->isEmpty())
            <p class="mt-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">Ajoutez d’abord un dossier locataire pour créer le bail. <a href="{{ route('tenants.index') }}" class="font-bold underline">Créer un locataire</a></p>
        @endif
    </section>
@endif

<section class="mt-8">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Historique locatif</p><h2 class="mt-1 text-xl font-bold text-slate-950">Baux et paiements</h2></div>
        <a href="{{ route('leases.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-900">Consulter tous les baux →</a>
    </div>
    <div class="grid gap-4">
        @forelse ($property->leases as $lease)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">{{ str($lease->tenant_name)->substr(0, 1)->upper() }}</span>
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Locataire</p><h3 class="mt-1 font-bold text-slate-900">{{ $lease->tenant_name }}</h3><a class="text-sm text-brand-700 hover:underline" href="mailto:{{ $lease->tenant_email }}">{{ $lease->tenant_email }}</a></div>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $lease->status === 'actif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $lease->status === 'actif' ? 'Bail en cours' : 'Bail terminé' }}</span>
                </div>
                <div class="mt-5 grid gap-4 border-y border-slate-100 py-4 text-sm sm:grid-cols-3">
                    <div><span class="block text-xs font-medium text-slate-400">Période</span><span class="mt-1 block font-semibold text-slate-800">{{ $lease->start_date->format('d/m/Y') }} — {{ $lease->end_date?->format('d/m/Y') ?? 'Durée indéterminée' }}</span></div>
                    <div><span class="block text-xs font-medium text-slate-400">Loyer mensuel</span><span class="mt-1 block font-semibold text-slate-800">{{ number_format((float) $lease->rent_amount, 0, ',', ' ') }} FCFA</span></div>
                    <div><span class="block text-xs font-medium text-slate-400">Paiements reçus</span><span class="mt-1 block font-semibold text-slate-800">{{ $lease->payments->count() }}</span></div>
                </div>
                <h4 class="mb-2 mt-4 text-xs font-bold uppercase tracking-wide text-slate-500">Historique des paiements</h4>
                <div class="divide-y divide-slate-100">
                    @forelse ($lease->payments as $payment)
                        <div class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                            <span class="text-slate-600">{{ $payment->payment_date->format('d/m/Y') }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} · {{ $payment->installment?->period_start->locale('fr')->translatedFormat('F Y') ?? 'Historique' }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA · <a class="text-brand-700 hover:underline" href="{{ route('payments.show', $payment) }}">{{ $payment->receipt_number }}</a></span>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-slate-500">Aucun paiement enregistré pour ce bail.</p>
                    @endforelse
                </div>
                @if ($lease->status === 'actif')
                    <h4 class="mb-2 mt-5 text-xs font-bold uppercase tracking-wide text-slate-500">Échéances récentes</h4>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @forelse ($lease->installments as $installment)
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-sm">
                                <div class="flex items-center justify-between gap-2"><strong class="text-slate-800">{{ $installment->period_start->locale('fr')->translatedFormat('F Y') }}</strong><span class="text-xs font-bold {{ $installment->payment_status === 'Payée' ? 'text-emerald-700' : ($installment->payment_status === 'En retard' ? 'text-rose-700' : 'text-amber-700') }}">{{ $installment->payment_status }}</span></div>
                                <p class="mt-1 text-xs text-slate-500">Échéance {{ $installment->due_date->format('d/m/Y') }} · solde {{ number_format($installment->outstanding_amount, 0, ',', ' ') }} FCFA</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucune échéance générée.</p>
                        @endforelse
                    </div>
                    @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('leases.edit', $lease) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Modifier le bail</a>
                        </div>
                    @endif
                    <a href="{{ route('rents.index', ['search' => $lease->tenant_name]) }}" class="mt-4 inline-flex text-sm font-bold text-brand-700 hover:underline">Enregistrer un paiement depuis les échéances →</a>
                    @if (auth()->user()->hasAnyRole('admin', 'gestionnaire'))
                        <form method="POST" action="{{ route('leases.terminate', $lease) }}" class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4" onsubmit="return confirm('Clôturer ce bail ? Le logement redeviendra disponible.');">
                            @csrf
                            <p class="text-xs text-slate-500">La clôture conserve les quittances déjà émises.</p>
                            <button class="rounded-lg px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Clôturer le bail</button>
                        </form>
                    @endif
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
                <p class="font-bold text-slate-800">Aucun bail associé à ce logement</p>
                <p class="mt-1 text-sm text-slate-500">Créez un bail lorsque vous aurez trouvé un locataire.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
