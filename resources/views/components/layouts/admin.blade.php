@props(['title' => 'Painel'])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · Painel Doce Encanto</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-100">
    @php
        $links = [
            ['admin.dashboard', 'admin.dashboard', 'Início'],
            ['admin.orders.index', 'admin.orders.*', 'Pedidos'],
            ['admin.cakes.index', 'admin.cakes.*', 'Cardápio'],
            ['admin.categories.index', 'admin.categories.*', 'Categorias'],
        ];
        if (auth()->user()->can('manage-staff')) {
            $links[] = ['admin.users.index', 'admin.users.*', 'Equipe'];
        }
    @endphp
    <header class="border-b border-cocoa-900/10 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('admin.dashboard') }}" class="font-display text-lg font-semibold">Doce Encanto <span class="text-sm font-sans font-medium text-cocoa-500">· painel</span></a>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('home') }}" class="text-cocoa-500 hover:text-cocoa-900">Ver site ↗</a>
                <span class="hidden text-cocoa-500 sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-ghost px-3 py-1.5 text-xs">Sair</button>
                </form>
            </div>
        </div>
        <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 sm:px-6" aria-label="Painel">
            @foreach ($links as [$route, $pattern, $label])
                <a href="{{ route($route) }}" @class(['shrink-0 border-b-2 px-3 py-2.5 text-sm font-medium',
                    'border-berry-500 text-berry-600' => request()->routeIs($pattern),
                    'border-transparent text-cocoa-700 hover:text-cocoa-900' => ! request()->routeIs($pattern)])>{{ $label }}</a>
            @endforeach
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @if (session('status'))
            <p role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p role="alert" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</p>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
