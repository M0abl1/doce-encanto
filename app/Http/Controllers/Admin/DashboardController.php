<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cake;
use App\Models\Order;
use App\Support\Money;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $open = [OrderStatus::Novo, OrderStatus::Confirmado, OrderStatus::Pronto];

        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Pedidos novos', 'value' => Order::where('status', OrderStatus::Novo)->count()],
                ['label' => 'Em andamento', 'value' => Order::whereIn('status', $open)->count()],
                ['label' => 'Retiradas hoje', 'value' => Order::whereIn('status', $open)->whereDate('pickup_date', today())->count()],
                ['label' => 'Pedidos do mês (R$)', 'value' => Money::format((int) Order::where('status', '!=', OrderStatus::Cancelado)
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_cents'))],
            ],
            'upcoming' => Order::whereIn('status', $open)->orderBy('pickup_date')->take(8)->get(),
            'activeCakes' => Cake::active()->count(),
        ]);
    }
}
