@props(['cake'])
<article class="group flex flex-col overflow-hidden rounded-2xl border border-cocoa-900/10 bg-white transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-cocoa-900/5">
    <a href="{{ route('menu.show', $cake) }}" class="relative block aspect-[6/5] overflow-hidden bg-cream-100">
        <x-cake-image :cake="$cake" class="h-full w-full transition duration-500 group-hover:scale-[1.03]" />
        @if ($cake->is_featured)
            <span class="absolute top-3 left-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-berry-600 shadow-sm">Mais pedido</span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-medium text-cocoa-500">{{ $cake->category->name }}</p>
        <h3 class="mt-1 font-display text-lg leading-snug font-semibold">
            <a href="{{ route('menu.show', $cake) }}" class="hover:text-berry-600">{{ $cake->name }}</a>
        </h3>
        <p class="mt-2 line-clamp-2 text-sm text-cocoa-700">{{ $cake->summary }}</p>
        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
            <div>
                <p class="text-lg font-semibold">{{ $cake->priceLabel() }}</p>
                @if ($cake->serves)
                    <p class="text-xs text-cocoa-500">{{ $cake->serves }}</p>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.store') }}">
                @csrf
                <input type="hidden" name="cake_id" value="{{ $cake->id }}">
                <button type="submit" class="btn-primary px-4 py-2" aria-label="Adicionar {{ $cake->name }} à encomenda">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Adicionar
                </button>
            </form>
        </div>
    </div>
</article>
