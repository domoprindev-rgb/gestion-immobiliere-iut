@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('content')
<div class="mb-7"><p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-700">Accès à l’application</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Utilisateurs & rôles</h1><p class="mt-2 text-sm text-slate-500">Créez des comptes d’équipe et limitez leurs actions selon leur responsabilité.</p></div>
<section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <h2 class="font-bold text-slate-900">Ajouter un utilisateur</h2>
    <p class="mt-1 text-sm text-slate-500">Communiquez son mot de passe de manière sécurisée. Il pourra le remplacer depuis « Mot de passe oublié ».</p>
    <form method="POST" action="{{ route('users.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @csrf
        <label class="text-xs font-bold text-slate-600">Nom<input name="name" value="{{ old('name') }}" required maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">E-mail<input name="email" type="email" value="{{ old('email') }}" required maxlength="255" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Rôle<select name="role" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"><option value="gestionnaire">Gestionnaire</option><option value="comptable">Comptable</option><option value="admin">Administrateur</option></select></label>
        <label class="text-xs font-bold text-slate-600">Mot de passe initial<input name="password" type="password" minlength="12" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <label class="text-xs font-bold text-slate-600">Confirmation<input name="password_confirmation" type="password" minlength="12" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-normal"></label>
        <div class="flex items-end"><button class="w-full rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-900">Créer le compte</button></div>
    </form>
</section>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto"><table class="w-full min-w-[700px] text-left text-sm">
        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-bold">Utilisateur</th><th class="px-5 py-3 font-bold">Rôle et état</th><th class="px-5 py-3 font-bold">Créé le</th><th class="px-5 py-3"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($users as $user)
                <tr>
                    <td class="px-5 py-4"><span class="font-bold text-slate-900">{{ $user->name }}</span><span class="mt-0.5 block text-xs text-slate-500">{{ $user->email }}</span>@if ($user->is(auth()->user()))<span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wide text-brand-700">Votre compte</span>@endif</td>
                    <td class="px-5 py-4">
                        <select name="role" form="update-user-{{ $user->id }}" class="rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs">
                                <option value="admin" @selected($user->role === 'admin')>Administrateur</option><option value="gestionnaire" @selected($user->role === 'gestionnaire')>Gestionnaire</option><option value="comptable" @selected($user->role === 'comptable')>Comptable</option>
                        </select>
                        <input type="hidden" name="is_active" value="0" form="update-user-{{ $user->id }}">
                        <label class="ml-2 inline-flex items-center gap-1.5 text-xs text-slate-600"><input type="checkbox" name="is_active" value="1" form="update-user-{{ $user->id }}" @checked($user->is_active) class="rounded border-slate-300 text-brand-700"> Actif</label>
                    </td>
                    <td class="px-5 py-4 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-right"><form id="update-user-{{ $user->id }}" method="POST" action="{{ route('users.update', $user) }}">@csrf @method('PUT')<button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white hover:bg-slate-700">Enregistrer</button></form></td>
                </tr>
            @endforeach
        </tbody>
    </table></div>
    @if ($users->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>@endif
</section>
@endsection
