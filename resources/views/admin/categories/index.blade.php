<x-layouts.admin title="Categorias">
    <h1 class="font-display text-3xl font-semibold tracking-tight">Categorias</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="card mt-6 flex flex-wrap items-end gap-3 p-5">
        @csrf
        <div class="min-w-48 flex-1">
            <label for="new-name" class="label">Nova categoria</label>
            <input id="new-name" name="name" value="{{ old_text('name') }}" required maxlength="60" class="field">
        </div>
        <div class="w-24">
            <label for="new-position" class="label">Ordem</label>
            <input id="new-position" name="position" type="number" min="0" max="999" value="{{ old_text('position', 0) }}" class="field">
        </div>
        <button class="btn-primary">Adicionar</button>
        @if ($errors->any())
            <p class="w-full text-sm text-rose-700">{{ $errors->first() }}</p>
        @endif
    </form>

    <ul class="card mt-6 divide-y divide-cocoa-900/10">
        @forelse ($categories as $category)
            <li class="flex flex-wrap items-center gap-3 px-5 py-3">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-1 flex-wrap items-center gap-2">
                    @csrf @method('PUT')
                    <label class="sr-only" for="n{{ $category->id }}">Nome</label>
                    <input id="n{{ $category->id }}" name="name" value="{{ $category->name }}" maxlength="60" class="field max-w-xs py-1.5">
                    <label class="sr-only" for="p{{ $category->id }}">Ordem</label>
                    <input id="p{{ $category->id }}" name="position" type="number" min="0" max="999" value="{{ $category->position }}" class="field w-20 py-1.5">
                    <button class="btn-ghost px-3 py-1.5 text-xs">Salvar</button>
                    <span class="text-sm text-cocoa-500">{{ $category->cakes_count }} {{ $category->cakes_count === 1 ? 'item' : 'itens' }}</span>
                </form>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Excluir esta categoria?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-semibold text-rose-700 hover:underline disabled:opacity-40" @disabled($category->cakes_count > 0) title="{{ $category->cakes_count > 0 ? 'Só é possível excluir categorias vazias' : '' }}">Excluir</button>
                </form>
            </li>
        @empty
            <li class="px-5 py-8 text-center text-cocoa-500">Nenhuma categoria cadastrada.</li>
        @endforelse
    </ul>
</x-layouts.admin>
