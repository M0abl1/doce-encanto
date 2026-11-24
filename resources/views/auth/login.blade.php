<x-layouts.site title="Acesso da equipe">
    <div class="mx-auto max-w-md px-4 pt-16 sm:px-6">
        <div class="card p-8">
            <h1 class="font-display text-2xl font-semibold tracking-tight">Acesso da equipe</h1>
            <p class="mt-1 text-sm text-cocoa-500">Área restrita aos funcionários da confeitaria.</p>
            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="label">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old_text('email') }}" required autofocus autocomplete="username" class="field" @error('email') aria-invalid="true" aria-describedby="e-email" @enderror>
                    @error('email')<p id="e-email" class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="label">Senha</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="field">
                    @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-cocoa-700">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-berry-500"> Manter conectado
                </label>
                <button class="btn-primary w-full py-3">Entrar</button>
            </form>
        </div>
    </div>
</x-layouts.site>
