<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#064e3b">
    <title>@yield('title', 'Tableau de bord') · ImmoPilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    @php
        $navigation = [
            ['label' => 'Vue d’ensemble', 'route' => 'dashboard', 'icon' => '◫', 'active' => request()->routeIs('dashboard')],
            ['label' => 'Logements', 'route' => 'properties.index', 'icon' => '⌂', 'active' => request()->routeIs('properties.*')],
            ['label' => 'Locataires', 'route' => 'tenants.index', 'icon' => '♙', 'active' => request()->routeIs('tenants.*')],
            ['label' => 'Baux', 'route' => 'leases.index', 'icon' => '▤', 'active' => request()->routeIs('leases.*')],
            ['label' => 'Échéances de loyer', 'route' => 'rents.index', 'icon' => '◷', 'active' => request()->routeIs('rents.*')],
            ['label' => 'Paiements', 'route' => 'payments.index', 'icon' => '↗', 'active' => request()->routeIs('payments.*', 'receipts.*')],
            ['label' => 'Dépenses', 'route' => 'expenses.index', 'icon' => '−', 'active' => request()->routeIs('expenses.*')],
            ['label' => 'Interventions', 'route' => 'maintenance.index', 'icon' => '⚒', 'active' => request()->routeIs('maintenance.*')],
            ['label' => 'Rapports', 'route' => 'reports.index', 'icon' => '▥', 'active' => request()->routeIs('reports.*')],
        ];
        if (auth()->user()->isAdministrator()) {
            $navigation[] = ['label' => 'Utilisateurs', 'route' => 'users.index', 'icon' => '♧', 'active' => request()->routeIs('users.*')];
        }
    @endphp

    <aside class="print:hidden fixed inset-y-0 left-0 z-30 hidden w-72 flex-col bg-slate-950 text-white md:flex">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 border-b border-white/10 px-7 py-7">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-600 text-xl font-black text-white shadow-lg shadow-brand-900/40">I</span>
            <span class="text-lg font-bold tracking-tight">ImmoPilot<span class="mt-0.5 block text-xs font-medium tracking-wide text-slate-400">GESTION LOCATIVE</span></span>
        </a>
        <div class="px-5 pt-8">
            <p class="px-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Espace de travail</p>
            <nav class="mt-3 grid gap-1.5">
                @foreach ($navigation as $item)
                    <a href="{{ route($item['route']) }}" @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold',
                        'bg-brand-600 text-white shadow-lg shadow-brand-900/30' => $item['active'],
                        'text-slate-400 hover:bg-white/5 hover:text-white' => ! $item['active'],
                    ])>
                        <span class="grid h-6 w-6 place-items-center text-lg leading-none">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="mt-auto p-5">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold text-slate-300">Un suivi, sans oubli.</p>
                <p class="mt-1.5 text-xs leading-5 text-slate-500">Baux, loyers et quittances réunis dans un seul espace.</p>
            </div>
            <div class="mt-4 flex items-center justify-between gap-2 border-t border-white/10 pt-4">
                <span class="min-w-0"><span class="block truncate text-xs font-semibold text-slate-300">{{ auth()->user()->name }}</span><span class="mt-0.5 block text-[10px] text-slate-500">{{ match (auth()->user()->role) {'admin' => 'Administrateur', 'gestionnaire' => 'Gestionnaire', 'comptable' => 'Comptable', default => 'Utilisateur'} }}</span></span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg px-2.5 py-2 text-[11px] font-bold text-slate-400 hover:bg-white/10 hover:text-white">Quitter</button></form>
            </div>
            <p class="px-1 pt-3 text-[11px] text-slate-600">© {{ now()->format('Y') }} ImmoPilot</p>
        </div>
    </aside>

    <div class="min-h-screen md:pl-72">
        <header class="print:hidden sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
            <div class="flex min-h-[72px] items-center justify-between gap-4 px-5 sm:px-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold tracking-tight text-slate-900 md:hidden">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-lg text-white">I</span>
                    ImmoPilot
                </a>
                <div class="hidden md:block">
                    <p class="text-xs font-medium text-slate-400">ESPACE DE GESTION</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-700">@yield('title', 'Tableau de bord')</p>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <span class="hidden text-sm font-medium capitalize text-slate-500 sm:block">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</span>
                    <span class="hidden text-sm font-semibold text-slate-600 sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="md:hidden">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">Déconnexion</button>
                    </form>
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-50 text-sm font-bold uppercase text-brand-700">{{ str(auth()->user()->name)->substr(0, 1) }}</span>
                </div>
            </div>
            <nav class="flex gap-1 overflow-x-auto px-4 pb-3 md:hidden">
                @foreach ($navigation as $item)
                    <a href="{{ route($item['route']) }}" @class([
                        'shrink-0 rounded-lg px-3 py-2 text-xs font-semibold',
                        'bg-brand-50 text-brand-700' => $item['active'],
                        'text-slate-500 hover:bg-slate-100' => ! $item['active'],
                    ])>{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </header>

        <main class="mx-auto max-w-[1600px] px-4 py-6 sm:px-7 sm:py-9">
            @if (session('success'))
                <div role="status" class="print:hidden mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800">
                    <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-emerald-600 text-xs font-bold text-white">✓</span>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="print:hidden mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="print:hidden mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-800">
                    <p class="font-semibold">Vérifiez les informations saisies :</p>
                    <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <style>
        @media print {
            body { background: white !important; }
            .print\:hidden, aside, header { display: none !important; }
            main { max-width: none !important; padding: 0 !important; }
        }
    </style>
</body>
</html>
