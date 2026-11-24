<x-layouts.site :title="$current?->name ?? 'Cardápio'">
    <section class="mx-auto max-w-6xl px-4 pt-12 sm:px-6">
        <p class="eyebrow">Cardápio</p>
        <h1 class="mt-2 font-display text-4xl font-semibold tracking-tight">{{ $current?->name ?? 'Bolos e tortas' }}</h1>
        <p class="mt-3 max-w-2xl text-cocoa-700">Todos os itens são feitos sob encomenda. Os preços são por unidade inteira.</p>

        <form method="GET" action="{{ route('menu.index') }}" class="mt-8 flex flex-col gap-3 sm:flex-row" role="search">
            @if ($current)
                <input type="hidden" name="categoria" value="{{ $current->slug }}">
            @endif
            <label for="busca" class="sr-only">Buscar no cardápio</label>
            <input id="busca" name="busca" type="search" value="{{ $search }}" placeholder="Buscar por nome ou sabor…" class="field sm:max-w-sm">
            <button class="btn-ghost">Buscar</button>
        </form>

        <nav class="mt-6 flex gap-2 overflow-x-auto pb-2" aria-label="Filtrar por categoria">
            <a href="{{ route('menu.index', array_filter(['busca' => $search])) }}" @class(['shrink-0 rounded-full border px-4 py-2 text-sm font-medium',
                'border-berry-500 bg-berry-500 text-white' => ! $current, 'border-cocoa-900/15 bg-white hover:border-berry-500' => $current])>Todos</a>
            @foreach ($categories as $category)
                <a href="{{ route('menu.index', array_filter(['categoria' => $category->slug, 'busca' => $search])) }}" @class(['shrink-0 rounded-full border px-4 py-2 text-sm font-medium',
                    'border-berry-500 bg-berry-500 text-white' => $current?->is($category), 'border-cocoa-900/15 bg-white hover:border-berry-500' => ! $current?->is($category)])>{{ $category->name }}</a>
            @endforeach
        </nav>
    </section>

    <section class="mx-auto mt-6 max-w-6xl px-4 sm:px-6">
        <p class="text-sm text-cocoa-500">{{ $cakes->total() }} {{ $cakes->total() === 1 ? 'item encontrado' : 'itens encontrados' }}</p>
        <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($cakes as $cake)
                <x-cake-card :cake="$cake" />
            @empty
                <div class="card col-span-full p-10 text-center">
                    <p class="font-display text-xl font-semibold">Nada encontrado</p>
                    <p class="mt-2 text-cocoa-700">Tente outro termo ou <a class="font-semibold text-berry-600 underline" href="{{ route('menu.index') }}">veja o cardápio completo</a>.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $cakes->links() }}</div>
    </section>
</x-layouts.site>
