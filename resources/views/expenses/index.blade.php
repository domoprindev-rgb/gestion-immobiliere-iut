@extends('layouts.app')
@section('title', 'Dépenses')
@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Charges du patrimoine</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Dépenses</h1><p class="mt-2 text-sm text-slate-500">Gardez une trace des charges, travaux et prestataires par logement.</p></div><div class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5"><p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total filtré</p><p class="mt-1 text-lg font-bold">{{ number_format((float) $totalAmount, 0, ',', ' ') }} FCFA</p></div></div>
@if (auth()->user()->hasAnyRole('admin', 'comptable'))
<section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <h2 class="font-bold text-slate-900">Enregistrer une dépense</h2>
    <form method="POST" action="{{ route('expenses.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @csrf
        <label class="text-xs font-bold text-slate-600">Logement<select name="property_id" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Choisir…</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->title }}</option>@endforeach</select></label>
        <label class="text-xs font-bold text-slate-600">Catégorie<select name="category" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Choisir…</option>@foreach (['Entretien', 'Réparation', 'Assurance', 'Impôt', 'Charges', 'Autre'] as $category)<option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></label>
        <label class="text-xs font-bold text-slate-600">Montant (FCFA)<input name="amount" type="number" min="1" step="0.01" value="{{ old('amount') }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Description<input name="description" value="{{ old('description') }}" maxlength="255" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Fournisseur<input name="vendor" value="{{ old('vendor') }}" maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Date<input name="expense_date" type="date" max="{{ today()->toDateString() }}" value="{{ old('expense_date', today()->toDateString()) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <div class="sm:col-span-2 lg:col-span-3"><button class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Ajouter la dépense</button></div>
    </form>
</section>
@endif
<form method="GET" action="{{ route('expenses.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <label class="text-xs font-bold text-slate-600">Logement<select name="property_id" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="">Tous</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(($filters['property_id'] ?? '') == $property->id)>{{ $property->title }}</option>@endforeach</select></label>
    <label class="text-xs font-bold text-slate-600">Du<input name="from" type="date" value="{{ $filters['from'] ?? '' }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
    <label class="text-xs font-bold text-slate-600">Au<input name="to" type="date" value="{{ $filters['to'] ?? '' }}" class="mt-1.5 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
    <div class="flex gap-2"><button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Filtrer</button><a href="{{ route('expenses.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Effacer</a></div>
</form>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm">
    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Date</th><th class="px-5 py-3 font-bold">Logement</th><th class="px-5 py-3 font-bold">Dépense</th><th class="px-5 py-3 font-bold">Fournisseur</th><th class="px-5 py-3 text-right font-bold">Montant</th><th class="px-5 py-3"></th></tr></thead>
    <tbody class="divide-y divide-slate-100">
        @forelse ($expenses as $expense)
            <tr><td class="px-5 py-4 text-slate-500">{{ $expense->expense_date->format('d/m/Y') }}</td><td class="px-5 py-4 font-semibold text-slate-800">{{ $expense->property->title }}</td><td class="px-5 py-4"><span class="font-bold text-slate-800">{{ $expense->description }}</span><span class="mt-0.5 block text-xs text-slate-500">{{ $expense->category }}</span></td><td class="px-5 py-4 text-slate-600">{{ $expense->vendor ?? '—' }}</td><td class="px-5 py-4 text-right font-bold text-slate-900">{{ number_format((float) $expense->amount, 0, ',', ' ') }} FCFA</td><td class="px-5 py-4 text-right">@if (auth()->user()->isAdministrator())<form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Supprimer cette dépense ?');">@csrf @method('DELETE')<button class="text-xs font-bold text-rose-700">Supprimer</button></form>@endif</td></tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">Aucune dépense pour ces filtres.</td></tr>
        @endforelse
    </tbody>
</table></div>
@if ($expenses->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $expenses->links() }}</div>@endif
</section>
@endsection
