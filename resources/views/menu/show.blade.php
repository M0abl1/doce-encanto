<x-layouts.site :title="$cake->name" :description="$cake->summary">
    <div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6">
        <nav class="text-sm text-cocoa-500" aria-label="Você está em">
            <a href="{{ route('menu.index') }}" class="hover:text-berry-600">Cardápio</a>
            <span aria-hidden="true"> / </span>
            <a href="{{ route('menu.index', ['categoria' => $cake->category->slug]) }}" class="hover:text-berry-600">{{ $cake->category->name }}</a>
        </nav>

        <div class="mt-6 grid gap-10 md:grid-cols-2">
            <div class="aspect-[6/5] overflow-hidden rounded-[2rem] border border-cocoa-900/10 bg-white">
                <x-cake-image :cake="$cake" />
            </div>
            <div class="flex flex-col">
                @if ($cake->is_featured)
                    <span class="w-fit rounded-full bg-berry-50 px-3 py-1 text-xs font-semibold text-berry-600">Mais pedido</span>
                @endif
                <h1 class="mt-3 font-display text-4xl font-semibold tracking-tight">{{ $cake->name }}</h1>
                <p class="mt-4 text-lg text-cocoa-700">{{ $cake->summary }}</p>
                @if ($cake->description)
                    <p class="mt-4 leading-relaxed text-cocoa-700">{{ $cake->description }}</p>
                @endif

                <dl class="mt-8 grid grid-cols-2 gap-4 rounded-2xl bg-cream-100 p-5 text-sm">
                    <div>
                        <dt class="text-cocoa-500">Preço</dt>
                        <dd class="mt-1 text-2xl font-semibold">{{ $cake->priceLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-cocoa-500">Rendimento</dt>
                        <dd class="mt-1 text-2xl font-semibold">{{ $cake->serves ?? '—' }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('cart.store') }}" class="mt-6 flex flex-wrap items-end gap-3">
                    @csrf
                    <input type="hidden" name="cake_id" value="{{ $cake->id }}">
                    <div>
                        <label for="quantity" class="label">Quantidade</label>
                        <input id="quantity" name="quantity" type="number" min="1" max="{{ \App\Services\Cart::MAX_PER_ITEM }}" value="1" class="field w-24">
                    </div>
                    <button class="btn-primary px-6 py-3 text-base">Adicionar à encomenda</button>
                </form>
                <p class="mt-4 text-sm text-cocoa-500">Encomendas com no mínimo 2 dias de antecedência. Pagamento na retirada.</p>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-20">
                <h2 class="font-display text-2xl font-semibold tracking-tight">Você também pode gostar</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-cake-card :cake="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.site>
