@extends('layouts.app')
@section('title', 'Modifier '.$property->title)
@section('content')
<a href="{{ route('properties.show', $property) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-700">← Retour à la fiche</a>
<section class="mx-auto mt-5 max-w-3xl rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-700">Fiche logement</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Modifier le logement</h1>
    <p class="mt-1 text-sm text-slate-500">Mettez à jour les informations principales de {{ $property->title }}.</p>
    <form method="POST" action="{{ route('properties.update', $property) }}" class="mt-7 grid gap-4 sm:grid-cols-2">
        @csrf @method('PUT')
        <label class="text-sm font-semibold text-slate-700">Nom du logement<input name="title" value="{{ old('title', $property->title) }}" maxlength="150" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Adresse<input name="address" value="{{ old('address', $property->address) }}" maxlength="255" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Loyer mensuel (FCFA)<input name="price" type="number" min="0" step="100" value="{{ old('price', $property->price) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <label class="text-sm font-semibold text-slate-700">Type de bien<input name="type" value="{{ old('type', $property->type) }}" maxlength="80" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 font-normal outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100"></label>
        <div class="flex flex-wrap gap-3 pt-2 sm:col-span-2"><button class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-900">Enregistrer les modifications</button><a href="{{ route('properties.show', $property) }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Annuler</a></div>
    </form>
    @if (auth()->user()->isAdministrator() && $property->leases_count === 0 && $property->expenses_count === 0 && $property->maintenance_requests_count === 0)
        <div class="mt-8 border-t border-slate-100 pt-6">
            <h2 class="font-bold text-slate-900">Supprimer ce logement</h2>
            <p class="mt-1 text-sm text-slate-500">Cette action est irréversible. Seul un logement sans bail, dépense ni intervention peut être supprimé.</p>
            <form method="POST" action="{{ route('properties.destroy', $property) }}" class="mt-3" onsubmit="return confirm('Supprimer définitivement ce logement ?');">
                @csrf @method('DELETE')
                <button class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Supprimer le logement</button>
            </form>
        </div>
    @elseif (auth()->user()->isAdministrator())
        <div class="mt-8 border-t border-slate-100 pt-6"><h2 class="font-bold text-slate-900">Suppression indisponible</h2><p class="mt-1 text-sm text-slate-500">Un logement avec un historique locatif ou financier doit être conservé.</p></div>
    @endif
</section>
@endsection
