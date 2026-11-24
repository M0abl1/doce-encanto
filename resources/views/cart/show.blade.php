<x-layouts.site title="Sua encomenda">
    <div class="mx-auto max-w-6xl px-4 pt-12 sm:px-6">
        <p class="eyebrow">Encomenda</p>
        <h1 class="mt-2 font-display text-4xl font-semibold tracking-tight">Sua encomenda</h1>

        @if ($lines->isEmpty())
            <div class="card mt-8 p-10 text-center">
                <p class="font-display text-xl font-semibold">Sua encomenda está vazia</p>
                <p class="mt-2 text-cocoa-700">Escolha bolos e tortas no cardápio para começar.</p>
                <a href="{{ route('menu.index') }}" class="btn-primary mt-6">Ver o cardápio</a>
            </div>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[1.4fr_1fr]">
                <section aria-label="Itens">
                    <ul class="card divide-y divide-cocoa-900/10">
                        @foreach ($lines as $line)
                            @php($cake = $line['cake'])
                            <li class="flex gap-4 p-4">
                                <a href="{{ route('menu.show', $cake) }}" class="size-20 shrink-0 overflow-hidden rounded-xl bg-cream-100">
                                    <x-cake-image :cake="$cake" />
                                </a>
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <a href="{{ route('menu.show', $cake) }}" class="font-semibold hover:text-berry-600">{{ $cake->name }}</a>
                                            <p class="text-sm text-cocoa-500">{{ $cake->priceLabel() }} cada</p>
                                        </div>
                                        <p class="font-semibold whitespace-nowrap">{{ \App\Support\Money::format($line['subtotal_cents']) }}</p>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <form method="POST" action="{{ route('cart.update', $cake->id) }}" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <label for="q{{ $cake->id }}" class="sr-only">Quantidade de {{ $cake->name }}</label>
                                            <input id="q{{ $cake->id }}" name="quantity" type="number" min="0" max="{{ \App\Services\Cart::MAX_PER_ITEM }}" value="{{ $line['quantity'] }}" class="field w-20 py-1.5">
                                            <button class="btn-ghost px-3 py-1.5 text-xs">Atualizar</button>
                                        </form>
                                        <form method="POST" action="{{ route('cart.destroy', $cake->id) }}">
                                            @csrf @method('DELETE')
                                            <button class="px-2 py-1.5 text-xs font-semibold text-rose-700 hover:underline">Remover</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex items-center justify-between rounded-2xl bg-cream-100 px-5 py-4">
                        <span class="font-semibold">Total</span>
                        <span class="text-2xl font-semibold">{{ \App\Support\Money::format($total) }}</span>
                    </div>
                    <a href="{{ route('menu.index') }}" class="mt-4 inline-block text-sm font-semibold text-berry-600 hover:underline">← Continuar escolhendo</a>
                </section>

                <section class="card h-fit p-6" aria-labelledby="dados">
                    <h2 id="dados" class="font-display text-xl font-semibold">Dados para retirada</h2>
                    <p class="mt-1 text-sm text-cocoa-500">Pagamento na loja, no dia da retirada.</p>
                    <form method="POST" action="{{ route('orders.store') }}" class="mt-6 space-y-4" novalidate>
                        @csrf
                        <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
                        @error('checkout_token')<p class="rounded-xl bg-rose-50 p-3 text-sm text-rose-800">{{ $message }}</p>@enderror
                        <div>
                            <label for="customer_name" class="label">Nome completo</label>
                            <input id="customer_name" name="customer_name" value="{{ old_text('customer_name') }}" required maxlength="100" autocomplete="name" class="field" @error('customer_name') aria-invalid="true" aria-describedby="e-name" @enderror>
                            @error('customer_name')<p id="e-name" class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="customer_phone" class="label">Telefone / WhatsApp</label>
                            <input id="customer_phone" name="customer_phone" value="{{ old_text('customer_phone') }}" required inputmode="tel" autocomplete="tel" placeholder="(75) 99999-9999" class="field" @error('customer_phone') aria-invalid="true" aria-describedby="e-phone" @enderror>
                            @error('customer_phone')<p id="e-phone" class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="customer_email" class="label">E-mail <span class="font-normal text-cocoa-500">(opcional)</span></label>
                            <input id="customer_email" name="customer_email" type="email" value="{{ old_text('customer_email') }}" maxlength="150" autocomplete="email" class="field" @error('customer_email') aria-invalid="true" aria-describedby="e-email" @enderror>
                            @error('customer_email')<p id="e-email" class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pickup_date" class="label">Data de retirada</label>
                            <input id="pickup_date" name="pickup_date" type="date" min="{{ $minDate }}" value="{{ old_text('pickup_date', $minDate) }}" required class="field" @error('pickup_date') aria-invalid="true" aria-describedby="e-date" @enderror>
                            @error('pickup_date')<p id="e-date" class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="notes" class="label">Observações <span class="font-normal text-cocoa-500">(opcional)</span></label>
                            <textarea id="notes" name="notes" rows="3" maxlength="500" placeholder="Ex.: escrever “Feliz aniversário, Ana” no bolo" class="field">{{ old_text('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <button class="btn-primary w-full py-3 text-base">Enviar encomenda</button>
                    </form>
                </section>
            </div>
        @endif
    </div>
</x-layouts.site>
