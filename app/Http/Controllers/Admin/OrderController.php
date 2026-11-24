<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = OrderStatus::tryFrom($request->text('status'));

        return view('admin.orders.index', [
            'orders' => Order::withCount('items')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderByRaw("case when status in ('novo','confirmado','pronto') then 0 else 1 end")
                ->orderBy('pickup_date')
                ->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items')]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);
        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Status do pedido atualizado.');
    }
}
