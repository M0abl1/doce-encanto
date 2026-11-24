<x-layouts.admin title="Equipe">
    <h1 class="font-display text-3xl font-semibold tracking-tight">Equipe</h1>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <ul class="card h-fit divide-y divide-cocoa-900/10">
            @foreach ($users as $user)
                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                    <div>
                        <p class="font-medium">{{ $user->name }} @if ($user->is_admin)<span class="ml-1 rounded-full bg-berry-50 px-2 py-0.5 text-xs font-semibold text-berry-600">Admin</span>@endif</p>
                        <p class="text-sm text-cocoa-500">{{ $user->email }}</p>
                    </div>
                    @unless ($user->is(auth()->user()))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Remover o acesso deste funcionário?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-700 hover:underline">Remover</button>
                        </form>
                    @endunless
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('admin.users.store') }}" class="card h-fit space-y-4 p-6">
            @csrf
            <h2 class="font-semibold">Cadastrar funcionário</h2>
            <div>
                <label for="name" class="label">Nome</label>
                <input id="name" name="name" value="{{ old_text('name') }}" required maxlength="100" class="field">
                @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="label">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old_text('email') }}" required maxlength="150" class="field">
                @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="label">Senha</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="field">
                @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="field">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_admin" value="1" class="size-4 accent-berry-500"> Administrador (pode gerenciar a equipe)</label>
            <button class="btn-primary w-full">Cadastrar</button>
        </form>
    </div>
</x-layouts.admin>
