<x-layouts.admin title="Cardápio">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-display text-3xl font-semibold tracking-tight">Cardápio</h1>
        <a href="{{ route('admin.cakes.create') }}" class="btn-primary">Novo item</a>
    </div>
    <form method="GET" class="mt-6 flex max-w-md gap-2" role="search">
        <label for="busca" class="sr-only">Buscar</label>
        <input id="busca" name="busca" type="search" value="{{ $search }}" placeholder="Buscar pelo nome…" class="field">
        <button class="btn-ghost">Buscar</button>
    </form>

    <div class="card mt-6 overflow-x-auto">
        <table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b border-cocoa-900/10 text-cocoa-500">
                <tr><th class="px-5 py-3 font-medium">Item</th><th class="px-5 py-3 font-medium">Categoria</th><th class="px-5 py-3 font-medium">Preço</th><th class="px-5 py-3 font-medium">Situação</th><th class="px-5 py-3"><span class="sr-only">Ações</span></th></tr>
            </thead>
            <tbody class="divide-y divide-cocoa-900/10">
                @forelse ($cakes as $cake)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="size-11 shrink-0 overflow-hidden rounded-lg bg-cream-100"><x-cake-image :cake="$cake" /></span>
                                <span class="font-medium">{{ $cake->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">{{ $cake->category->name }}</td>
                        <td class="px-5 py-3">{{ $cake->priceLabel() }}</td>
                        <td class="px-5 py-3">
                            @if ($cake->is_active)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Ativo</span>
                            @else
                                <span class="rounded-full bg-stone-200 px-2.5 py-1 text-xs font-semibold text-stone-700">Oculto</span>
                            @endif
                            @if ($cake->is_featured)
                                <span class="ml-1 rounded-full bg-berry-50 px-2.5 py-1 text-xs font-semibold text-berry-600">Destaque</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.cakes.edit', $cake) }}" class="font-semibold text-berry-600 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-cocoa-500">Nenhum item encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $cakes->links() }}</div>
</x-layouts.admin>
