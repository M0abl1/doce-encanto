<x-layouts.admin :title="'Pedido '.$order->code">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-cocoa-500 hover:text-cocoa-900">← Pedidos</a>
    <div class="mt-2 flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-display text-3xl font-semibold tracking-tight">Pedido <span class="font-mono">{{ $order->code }}</span></h1>
        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $order->status->badge() }}">{{ $order->status->label() }}</span>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <section class="card p-6">
            <h2 class="font-semibold">Itens</h2>
            <ul class="mt-4 divide-y divide-cocoa-900/10 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3">
                        <span>{{ $item->quantity }}× {{ $item->cake_name }} <span class="text-cocoa-500">({{ \App\Support\Money::format($item->unit_price_cents) }})</span></span>
                        <span class="font-medium">{{ $item->subtotalLabel() }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="flex justify-between border-t border-cocoa-900/10 pt-4 text-lg font-semibold"><span>Total</span><span>{{ $order->totalLabel() }}</span></div>
            @if ($order->notes)
                <h3 class="mt-6 text-sm font-semibold">Observações do cliente</h3>
                <p class="mt-2 rounded-xl bg-cream-100 p-3 text-sm whitespace-pre-line">{{ $order->notes }}</p>
            @endif
        </section>

        <div class="space-y-6">
            <section class="card p-6 text-sm">
                <h2 class="font-semibold">Cliente</h2>
                <dl class="mt-4 space-y-3">
                    <div><dt class="text-cocoa-500">Nome</dt><dd class="font-medium">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-cocoa-500">Telefone</dt><dd class="font-medium">{{ $order->customer_phone }}</dd></div>
                    @if ($order->customer_email)
                        <div><dt class="text-cocoa-500">E-mail</dt><dd class="font-medium break-all">{{ $order->customer_email }}</dd></div>
                    @endif
                    <div><dt class="text-cocoa-500">Retirada</dt><dd class="font-medium">{{ $order->pickup_date->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-cocoa-500">Feito em</dt><dd class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</dd></div>
                </dl>
            </section>
            <section class="card p-6">
                <h2 class="font-semibold">Atualizar status</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 flex gap-2">
                    @csrf @method('PATCH')
                    <label for="status" class="sr-only">Status</label>
                    <select id="status" name="status" class="field">
                        @foreach (\App\Enums\OrderStatus::cases() as $case)
                            <option value="{{ $case->value }}" @selected($order->status === $case)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary">Salvar</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
