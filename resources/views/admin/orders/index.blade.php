<x-layouts.admin title="Pedidos">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-display text-3xl font-semibold tracking-tight">Pedidos</h1>
        <nav class="flex flex-wrap gap-2 text-sm" aria-label="Filtrar por status">
            <a href="{{ route('admin.orders.index') }}" @class(['rounded-full border px-3 py-1.5', 'border-berry-500 bg-berry-500 text-white' => ! $status, 'border-cocoa-900/15 bg-white' => $status])>Todos</a>
            @foreach (\App\Enums\OrderStatus::cases() as $case)
                <a href="{{ route('admin.orders.index', ['status' => $case->value]) }}" @class(['rounded-full border px-3 py-1.5', 'border-berry-500 bg-berry-500 text-white' => $status === $case, 'border-cocoa-900/15 bg-white' => $status !== $case])>{{ $case->label() }}</a>
            @endforeach
        </nav>
    </div>

    <div class="card mt-6 overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-cocoa-900/10 text-cocoa-500">
                <tr><th class="px-5 py-3 font-medium">Código</th><th class="px-5 py-3 font-medium">Cliente</th><th class="px-5 py-3 font-medium">Retirada</th><th class="px-5 py-3 font-medium">Itens</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-cocoa-900/10">
                @forelse ($orders as $order)
                    <tr class="hover:bg-cream-50">
                        <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-semibold text-berry-600 hover:underline">{{ $order->code }}</a></td>
                        <td class="px-5 py-3">{{ $order->customer_name }}</td>
                        <td class="px-5 py-3">{{ $order->pickup_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">{{ $order->items_count }}</td>
                        <td class="px-5 py-3 font-medium">{{ $order->totalLabel() }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status->badge() }}">{{ $order->status->label() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-cocoa-500">Nenhum pedido.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-layouts.admin>
