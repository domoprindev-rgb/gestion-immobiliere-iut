@extends('layouts.app')
@section('title', $property->title)
@section('content')
<a href="{{ route('properties.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">← Retour aux logements</a>
<section class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="text-sm font-semibold uppercase tracking-wider text-blue-700">{{ ucfirst($property->type) }}</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $property->title }}</h1><p class="mt-2 text-slate-500">{{ $property->address }}</p></div>
        <span class="rounded-full px-3 py-1.5 text-sm font-semibold {{ $property->status === 'disponible' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $property->status === 'disponible' ? 'Disponible' : 'Loué' }}</span>
    </div>
    <div class="mt-6 border-t border-slate-100 pt-5"><p class="text-sm text-slate-500">Loyer mensuel</p><p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format((float) $property->price, 0, ',', ' ') }} FCFA</p></div>
</section>

@if ($property->status === 'disponible')
<section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5"><h2 class="text-lg font-semibold text-slate-900">Créer un bail</h2><p class="mt-1 text-sm text-slate-500">Renseignez le locataire et les conditions du bail.</p></div>
    <form method="POST" action="{{ route('leases.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf<input type="hidden" name="property_id" value="{{ $property->id }}">
        <label class="text-sm font-medium text-slate-700">Nom du locataire<input name="tenant_name" value="{{ old('tenant_name') }}" required maxlength="150" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <label class="text-sm font-medium text-slate-700">Adresse e-mail<input name="tenant_email" type="email" value="{{ old('tenant_email') }}" required maxlength="255" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <label class="text-sm font-medium text-slate-700">Date de début<input name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <label class="text-sm font-medium text-slate-700">Date de fin <span class="font-normal text-slate-400">(facultatif)</span><input name="end_date" type="date" value="{{ old('end_date') }}" class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <label class="text-sm font-medium text-slate-700">Loyer mensuel (FCFA)<input name="rent_amount" type="number" min="1" step="100" value="{{ old('rent_amount', $property->price) }}" required class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 font-normal"></label>
        <div class="self-end"><button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Créer le bail</button></div>
    </form>
</section>
@endif

<section class="mt-8">
    <div class="mb-4"><h2 class="text-xl font-bold text-slate-900">Baux et paiements</h2><p class="mt-1 text-sm text-slate-500">Historique des locations et quittances associées.</p></div>
    @forelse ($property->leases as $lease)
        <article class="mb-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Locataire</p><h3 class="mt-1 font-semibold text-slate-900">{{ $lease->tenant_name }}</h3><a class="text-sm text-blue-700 hover:underline" href="mailto:{{ $lease->tenant_email }}">{{ $lease->tenant_email }}</a></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($lease->status) }}</span></div>
            <div class="mt-4 grid gap-3 border-y border-slate-100 py-4 text-sm sm:grid-cols-3"><div><span class="block text-slate-500">Période</span><span class="font-medium text-slate-800">{{ $lease->start_date->format('d/m/Y') }} — {{ $lease->end_date?->format('d/m/Y') ?? 'Sans date de fin' }}</span></div><div><span class="block text-slate-500">Loyer mensuel</span><span class="font-medium text-slate-800">{{ number_format((float) $lease->rent_amount, 0, ',', ' ') }} FCFA</span></div><div><span class="block text-slate-500">Paiements</span><span class="font-medium text-slate-800">{{ $lease->payments->count() }}</span></div></div>
            <h4 class="mb-2 mt-4 text-sm font-semibold text-slate-800">Historique des paiements</h4>
            <div class="divide-y divide-slate-100">
                @forelse ($lease->payments as $payment)
                    <div class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm"><span class="text-slate-600">{{ $payment->payment_date->format('d/m/Y') }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span><span class="font-medium text-slate-900">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA · <a class="text-blue-700 hover:underline" href="{{ route('payments.show', $payment) }}">{{ $payment->receipt_number }}</a></span></div>
                @empty
                    <p class="py-2 text-sm text-slate-500">Aucun paiement enregistré pour ce bail.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('payments.store') }}" class="mt-5 grid items-end gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-4">
                @csrf<input type="hidden" name="lease_id" value="{{ $lease->id }}">
                <label class="text-xs font-semibold text-slate-600">Montant (FCFA)<input name="amount" type="number" min="1" step="100" value="{{ old('lease_id') == $lease->id ? old('amount') : $lease->rent_amount }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal"></label>
                <label class="text-xs font-semibold text-slate-600">Date de paiement<input name="payment_date" type="date" value="{{ old('lease_id') == $lease->id ? old('payment_date') : now()->toDateString() }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal"></label>
                <label class="text-xs font-semibold text-slate-600">Mode de paiement<select name="payment_method" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal"><option value="especes">Espèces</option><option value="virement">Virement</option><option value="mobile_money">Mobile Money</option></select></label>
                <button class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">Enregistrer le paiement</button>
            </form>
        </article>
    @empty
        @if ($property->status !== 'disponible')<div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500">Aucun bail n’est associé à ce logement.</div>@endif
    @endforelse
</section>
@endsection
