<x-layouts.admin :title="$cake->exists ? 'Editar '.$cake->name : 'Novo item'">
    <a href="{{ route('admin.cakes.index') }}" class="text-sm text-cocoa-500 hover:text-cocoa-900">← Cardápio</a>
    <h1 class="mt-2 font-display text-3xl font-semibold tracking-tight">{{ $cake->exists ? 'Editar item' : 'Novo item' }}</h1>

    @if ($categories->isEmpty())
        <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Cadastre uma <a href="{{ route('admin.categories.index') }}" class="font-semibold underline">categoria</a> antes de criar itens.
        </p>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $cake->exists ? route('admin.cakes.update', $cake) : route('admin.cakes.store') }}" class="mt-6 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        @if ($cake->exists) @method('PUT') @endif

        <div class="card space-y-5 p-6">
            <div>
                <label for="name" class="label">Nome</label>
                <input id="name" name="name" value="{{ old_text('name', $cake->name) }}" required maxlength="100" class="field">
                @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                @error('slug')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="label">Categoria</label>
                    <select id="category_id" name="category_id" required class="field">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $cake->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="price" class="label">Preço (R$)</label>
                    <input id="price" name="price" inputmode="decimal" placeholder="89,90" required
                        value="{{ old_text('price', $cake->price_cents ? number_format($cake->price_cents / 100, 2, ',', '.') : '') }}" class="field">
                    @error('price')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    @error('price_cents')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="summary" class="label">Resumo <span class="font-normal text-cocoa-500">(aparece no cardápio)</span></label>
                <input id="summary" name="summary" value="{{ old_text('summary', $cake->summary) }}" required maxlength="160" class="field">
                @error('summary')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="description" class="label">Descrição completa</label>
                <textarea id="description" name="description" rows="5" maxlength="2000" class="field">{{ old_text('description', $cake->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="serves" class="label">Rendimento</label>
                    <input id="serves" name="serves" value="{{ old_text('serves', $cake->serves) }}" maxlength="40" placeholder="20 fatias" class="field">
                    @error('serves')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="color" class="label">Cor da ilustração</label>
                    <input id="color" name="color" type="color" value="{{ old_text('color', $cake->color) }}" class="h-11 w-full cursor-pointer rounded-xl border border-cocoa-900/15 bg-white p-1">
                    @error('color')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <p class="label">Foto</p>
                <div class="aspect-[6/5] overflow-hidden rounded-xl bg-cream-100">
                    <x-cake-image :cake="$cake" />
                </div>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-4 block w-full text-sm file:mr-3 file:rounded-full file:border-0 file:bg-cream-100 file:px-4 file:py-2 file:font-semibold">
                <p class="mt-2 text-xs text-cocoa-500">JPG, PNG ou WebP até 2 MB. Sem foto, usamos a ilustração na cor escolhida.</p>
                @error('image')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                @if ($cake->image_path)
                    <label class="mt-3 flex items-center gap-2 text-sm"><input type="checkbox" name="remove_image" value="1" class="size-4 accent-berry-500"> Remover foto atual</label>
                @endif
            </div>
            <div class="card space-y-3 p-6 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $cake->is_active)) class="size-4 accent-berry-500"> Visível no cardápio</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $cake->is_featured)) class="size-4 accent-berry-500"> Destacar na página inicial</label>
            </div>
            <button class="btn-primary w-full py-3" @disabled($categories->isEmpty())>{{ $cake->exists ? 'Salvar alterações' : 'Cadastrar item' }}</button>
        </div>
    </form>

    @if ($cake->exists)
        <form method="POST" action="{{ route('admin.cakes.destroy', $cake) }}" class="mt-10 border-t border-cocoa-900/10 pt-6" onsubmit="return confirm('Excluir este item do cardápio?')">
            @csrf @method('DELETE')
            <button class="btn-danger">Excluir item</button>
            <span class="ml-3 text-sm text-cocoa-500">Pedidos antigos continuam com o nome e o preço registrados.</span>
        </form>
    @endif
</x-layouts.admin>
