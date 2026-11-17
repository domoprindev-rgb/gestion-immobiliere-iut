<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · ImmoPilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-950 px-4 py-10">
    <main class="w-full max-w-md">
        <a href="{{ route('login') }}" class="mb-6 flex items-center justify-center gap-3 text-white">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-600 text-xl font-black">I</span>
            <span class="text-xl font-bold">ImmoPilot<span class="mt-0.5 block text-[10px] font-semibold tracking-[0.18em] text-slate-400">GESTION LOCATIVE</span></span>
        </a>
        @if (session('status'))
            <div role="status" class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
