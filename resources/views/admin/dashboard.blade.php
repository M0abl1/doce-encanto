<x-layouts.admin title="Início">
    <h1 class="font-display text-3xl font-semibold tracking-tight">Olá, {{ strtok(auth()->user()->name, ' ') }}</h1>
    <p class="mt-1 text-cocoa-500">{{ $activeCakes }} itens ativos no cardápio.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="card p-5">
                <p class="text-sm text-cocoa-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <section class="card mt-8 overflow-hidden">
        <div class="flex items-center justify-between border-b border-cocoa-900/10 px-5 py-4">
            <h2 class="font-semibold">Próximas retiradas</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-berry-600 hover:underline">Todos os pedidos</a>
        </div>
        @if ($upcoming->isEmpty())
            <p class="p-5 text-sm text-cocoa-500">Nenhum pedido em andamento.</p>
        @else
            <ul class="divide-y divide-cocoa-900/10">
                @foreach ($upcoming as $order)
                    <li>
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 hover:bg-cream-50">
                            <span>
                                <span class="font-medium">{{ $order->customer_name }}</span>
                                <span class="ml-2 font-mono text-xs text-cocoa-500">{{ $order->code }}</span>
                            </span>
                            <span class="flex items-center gap-3 text-sm">
                                <span>{{ $order->pickup_date->format('d/m') }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status->badge() }}">{{ $order->status->label() }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layouts.admin>
