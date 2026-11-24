<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}Doce Encanto Confeitaria</title>
    <meta name="description" content="{{ $description ?? 'Bolos e tortas artesanais sob encomenda. Escolha no cardápio e retire na loja.' }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#conteudo" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">Pular para o conteúdo</a>

    <header class="sticky top-0 z-40 border-b border-cocoa-900/10 bg-cream-50/90 backdrop-blur">
        <nav class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6" aria-label="Principal">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-full bg-berry-500 text-white">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 21h16M5 21v-6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6M5 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0M12 13V9m0-4.5c.8.6 1 1.5 0 2.5-1-1-.8-1.9 0-2.5Z"/></svg>
                </span>
                <span class="font-display text-xl font-semibold tracking-tight">Doce Encanto</span>
            </a>
            <div class="flex items-center gap-1 text-sm font-medium sm:gap-2">
                <a href="{{ route('menu.index') }}" @class(['rounded-full px-3 py-2 hover:bg-cream-100', 'text-berry-600' => request()->routeIs('menu.*')])>Cardápio</a>
                <a href="{{ route('cart.show') }}" @class(['relative inline-flex items-center gap-2 rounded-full px-3 py-2 hover:bg-cream-100', 'text-berry-600' => request()->routeIs('cart.*')])>
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 7h12l-1 13H7L6 7Zm3 0V6a3 3 0 0 1 6 0v1"/></svg>
                    <span class="hidden sm:inline">Encomenda</span>
                    @if ($cartCount > 0)
                        <span class="grid min-w-5 place-items-center rounded-full bg-berry-500 px-1.5 text-xs font-semibold text-white" aria-label="{{ $cartCount }} itens">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </nav>
    </header>

    <main id="conteudo" class="flex-1">
        @if (session('status'))
            <div class="mx-auto mt-6 max-w-6xl px-4 sm:px-6">
                <p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
            </div>
        @endif
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-cocoa-900/10 bg-cream-100">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 text-sm text-cocoa-700 sm:grid-cols-3 sm:px-6">
            <div>
                <p class="font-display text-lg font-semibold text-cocoa-900">Doce Encanto</p>
                <p class="mt-2 max-w-xs">Bolos e tortas artesanais feitos sob encomenda.</p>
            </div>
            <div>
                <p class="font-semibold text-cocoa-900">Funcionamento</p>
                <p class="mt-2">Terça a sábado, das 9h às 19h</p>
                <p>Domingo, das 9h às 13h</p>
            </div>
            <div>
                <p class="font-semibold text-cocoa-900">Encomendas</p>
                <p class="mt-2">Faça pelo site com pelo menos 2 dias de antecedência e retire na loja.</p>
                <a href="{{ route('login') }}" class="mt-3 inline-block text-cocoa-500 underline-offset-4 hover:underline">Acesso da equipe</a>
            </div>
        </div>
        <p class="border-t border-cocoa-900/10 py-4 text-center text-xs text-cocoa-500">Projeto de portfólio com dados fictícios · {{ date('Y') }}</p>
    </footer>
</body>
</html>
