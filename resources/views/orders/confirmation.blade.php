<x-layouts.site title="Encomenda recebida">
    <div class="mx-auto max-w-2xl px-4 pt-14 sm:px-6">
        <div class="card p-8 text-center sm:p-10">
            <span class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
            </span>
            <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight">Encomenda recebida!</h1>
            <p class="mt-3 text-cocoa-700">Obrigado, {{ $order->customer_name }}. Vamos confirmar pelo telefone informado.</p>
            <p class="mt-6 text-sm text-cocoa-500">Código da encomenda</p>
            <p class="font-mono text-2xl font-semibold tracking-widest">{{ $order->code }}</p>
        </div>

        <div class="card mt-6 p-6">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-cocoa-500">Retirada</dt><dd class="mt-1 font-semibold">{{ $order->pickup_date->translatedFormat('l, d \d\e F') }}</dd></div>
                <div><dt class="text-cocoa-500">Status</dt><dd class="mt-1"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status->badge() }}">{{ $order->status->label() }}</span></dd></div>
            </dl>
            <ul class="mt-6 divide-y divide-cocoa-900/10 border-t border-cocoa-900/10 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3"><span>{{ $item->quantity }}× {{ $item->cake_name }}</span><span class="font-medium">{{ $item->subtotalLabel() }}</span></li>
                @endforeach
            </ul>
            <div class="flex justify-between border-t border-cocoa-900/10 pt-4 font-semibold"><span>Total</span><span>{{ $order->totalLabel() }}</span></div>
            @if ($order->notes)
                <p class="mt-4 rounded-xl bg-cream-100 p-3 text-sm text-cocoa-700">{{ $order->notes }}</p>
            @endif
        </div>
        <p class="mt-6 text-center"><a href="{{ route('home') }}" class="text-sm font-semibold text-berry-600 hover:underline">Voltar para o início</a></p>
    </div>
</x-layouts.site>
