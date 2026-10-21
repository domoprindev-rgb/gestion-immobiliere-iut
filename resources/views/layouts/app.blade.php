<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Immobilière') · ImmoPilot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f5f7fb; }
        @media print { nav, .print\:hidden { display: none !important; } body { background: white; } }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-semibold tracking-tight text-slate-900">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-700 text-lg text-white">I</span>
                <span>ImmoPilot <span class="block text-xs font-normal tracking-normal text-slate-500">Gestion immobilière</span></span>
            </a>
            <div class="flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Vue d’ensemble</a>
                <a href="{{ route('properties.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('properties.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Logements</a>
                <a href="{{ route('payments.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('payments.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Paiements</a>
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-5 py-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold">Vérifiez les informations saisies :</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
