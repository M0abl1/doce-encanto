<x-layouts.site>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 pt-12 pb-16 sm:px-6 md:grid-cols-[1.1fr_1fr] md:pt-20">
            <div>
                <p class="eyebrow">Confeitaria artesanal</p>
                <h1 class="mt-4 font-display text-4xl leading-[1.05] font-semibold tracking-tight text-balance sm:text-6xl">
                    Bolos e tortas feitos sob encomenda, do jeito que você imaginou.
                </h1>
                <p class="mt-6 max-w-lg text-lg text-cocoa-700">
                    Escolha no cardápio, diga quando quer retirar e a gente prepara tudo fresquinho para o seu dia.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('menu.index') }}" class="btn-primary px-6 py-3 text-base">Ver o cardápio</a>
                    <a href="#como-funciona" class="btn-ghost px-6 py-3 text-base">Como funciona</a>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-md">
                <div class="absolute -inset-6 -z-10 rounded-[3rem] bg-berry-50"></div>
                <div class="aspect-[6/5] overflow-hidden rounded-[2rem] border border-cocoa-900/10 bg-white shadow-xl shadow-berry-500/10">
                    <x-cake-art color="#C2416B" title="Naked Cake de Frutas Vermelhas" />
                </div>
            </div>
        </div>
    </section>

    {{-- Categorias --}}
    @if ($categories->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 sm:px-6" aria-label="Categorias">
            <div class="flex flex-wrap gap-2">
                @foreach ($categories as $category)
                    <a href="{{ route('menu.index', ['categoria' => $category->slug]) }}" class="rounded-full border border-cocoa-900/15 bg-white px-4 py-2 text-sm font-medium hover:border-berry-500 hover:text-berry-600">{{ $category->name }}</a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Destaques --}}
    <section class="mx-auto mt-14 max-w-6xl px-4 sm:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Os mais pedidos</p>
                <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight">Favoritos da casa</h2>
            </div>
            <a href="{{ route('menu.index') }}" class="hidden text-sm font-semibold text-berry-600 hover:underline sm:inline">Cardápio completo →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($featured as $cake)
                <x-cake-card :cake="$cake" />
            @empty
                <p class="text-cocoa-700">O cardápio está sendo preparado. Volte em breve!</p>
            @endforelse
        </div>
    </section>

    {{-- Como funciona --}}
    <section id="como-funciona" class="mx-auto mt-20 max-w-6xl scroll-mt-24 px-4 sm:px-6">
        <div class="rounded-[2rem] bg-cocoa-900 px-6 py-12 text-cream-50 sm:px-12">
            <p class="eyebrow text-caramel-400">Encomenda sem complicação</p>
            <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight">Como funciona</h2>
            <ol class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['Escolha', 'Navegue pelo cardápio com mais de 50 bolos e tortas e adicione o que quiser à encomenda.'],
                    ['Agende', 'Informe seu nome, telefone e o dia da retirada, com pelo menos 2 dias de antecedência.'],
                    ['Retire', 'Nossa equipe confirma o pedido e prepara tudo fresquinho para a data combinada.'],
                ] as $i => [$step, $text])
                    <li>
                        <span class="grid size-10 place-items-center rounded-full bg-berry-500 font-display text-lg font-semibold">{{ $i + 1 }}</span>
                        <p class="mt-4 text-lg font-semibold">{{ $step }}</p>
                        <p class="mt-2 text-sm text-cream-200/80">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
</x-layouts.site>
