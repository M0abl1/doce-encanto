<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Cake;
use App\Models\Order;
use Illuminate\Database\Seeder;

/**
 * Cardápio (50 bolos e tortas) e alguns pedidos de demonstração com clientes fictícios.
 * Nenhum usuário é criado aqui: use `php artisan loja:criar-admin`.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CatalogSeeder::class);

        $cakes = Cake::all();
        foreach ([OrderStatus::Novo, OrderStatus::Novo, OrderStatus::Confirmado, OrderStatus::Pronto, OrderStatus::Entregue] as $status) {
            $order = Order::factory()->create(['status' => $status]);
            $total = 0;
            foreach ($cakes->random(fake()->numberBetween(1, 3)) as $cake) {
                $quantity = fake()->numberBetween(1, 2);
                $order->items()->create([
                    'cake_id' => $cake->id,
                    'cake_name' => $cake->name,
                    'quantity' => $quantity,
                    'unit_price_cents' => $cake->price_cents,
                ]);
                $total += $quantity * $cake->price_cents;
            }
            $order->update(['total_cents' => $total]);
        }
    }
}
