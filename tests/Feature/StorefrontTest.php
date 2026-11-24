<?php

namespace Tests\Feature;

use App\Models\Cake;
use App\Models\Category;
use App\Models\Order;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    /** Abre a tela da encomenda (gera o token) e devolve dados válidos do formulário. */
    private function checkoutData(array $overrides = []): array
    {
        $this->get(route('cart.show'));

        return [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '75999990000',
            'pickup_date' => now()->addDays(3)->toDateString(),
            'checkout_token' => session('checkout_token'),
            ...$overrides,
        ];
    }

    public function test_catalog_seeder_creates_fifty_items(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->assertSame(50, Cake::count());
        $this->assertTrue(Category::where('slug', 'tortas-doces')->exists());
        $this->assertTrue(Category::where('slug', 'tortas-salgadas')->exists());
    }

    public function test_home_menu_and_item_pages_render(): void
    {
        $this->seed(CatalogSeeder::class);
        $cake = Cake::where('slug', 'torta-holandesa')->firstOrFail();

        $this->get('/')->assertOk()->assertSee('Favoritos da casa');
        $this->get('/cardapio')->assertOk()->assertSee('50 itens encontrados')->assertSee('Mostrando');
        $this->get('/cardapio?categoria=tortas-salgadas')->assertOk()->assertSee('Quiche Lorraine')->assertDontSee('Red Velvet');
        $this->get('/cardapio?busca=lim%C3%A3o')->assertOk()->assertSee('Torta de Limão');
        $this->get(route('menu.show', $cake))->assertOk()->assertSee('Torta Holandesa')->assertSee('R$ 99,90');
    }

    public function test_site_has_no_testimonials(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->get('/')->assertOk()->assertDontSee('depoimento', false)->assertDontSee('avaliação', false);
    }

    public function test_inactive_items_are_hidden(): void
    {
        $cake = Cake::factory()->inactive()->create(['name' => 'Bolo Secreto', 'slug' => 'bolo-secreto']);

        $this->get('/cardapio')->assertDontSee('Bolo Secreto');
        $this->get(route('menu.show', $cake))->assertNotFound();
        $this->post(route('cart.store'), ['cake_id' => $cake->id])->assertNotFound();
    }

    public function test_search_wildcards_are_escaped(): void
    {
        Cake::factory()->create(['name' => 'Bolo Simples', 'slug' => 'bolo-simples']);

        $this->get('/cardapio?busca=%25')->assertOk()->assertSee('Nada encontrado');
    }

    public function test_output_is_escaped(): void
    {
        Cake::factory()->create(['name' => '<script>alert(1)</script>', 'slug' => 'xss', 'summary' => '<b>x</b>']);

        $this->get('/cardapio')->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_full_order_flow_uses_database_prices(): void
    {
        $cake = Cake::factory()->create(['price_cents' => 8990]);
        $other = Cake::factory()->create(['price_cents' => 5000]);

        $this->post(route('cart.store'), ['cake_id' => $cake->id, 'quantity' => 2])->assertRedirect(route('cart.show'));
        $this->post(route('cart.store'), ['cake_id' => $other->id]);
        $this->get(route('cart.show'))->assertOk()->assertSee('R$ 229,80');

        $response = $this->post(route('orders.store'), $this->checkoutData([
            'customer_phone' => '(75) 99999-0000',
            'notes' => 'Escrever Parabéns',
            'total_cents' => 1, // ignorado: o total vem do banco
        ]));

        $order = Order::with('items')->sole();
        $response->assertRedirect();
        $this->assertStringContainsString('/encomenda/confirmada/'.$order->code, $response->headers->get('Location'));
        $this->assertSame(22980, $order->total_cents);
        $this->assertSame('75999990000', $order->customer_phone);
        $this->assertSame('novo', $order->status->value);
        $this->assertCount(2, $order->items);

        $this->get($response->headers->get('Location'))->assertOk()->assertSee($order->code)->assertSee('R$ 229,80');
        $this->get(route('cart.show'))->assertSee('Sua encomenda está vazia');
    }

    public function test_confirmation_requires_valid_signed_url(): void
    {
        $order = Order::factory()->create();

        $this->get('/encomenda/confirmada/'.$order->code)->assertForbidden();
        $this->get('/encomenda/confirmada/ZZZZZZZZ')->assertForbidden(); // inexistente: mesma resposta
        $url = URL::temporarySignedRoute('orders.confirmation', now()->addDays(30), ['code' => $order->code]);
        $this->get($url)->assertOk()->assertSee($order->code);

        $this->travel(31)->days();
        $this->get($url)->assertForbidden();
    }

    public function test_duplicate_submit_returns_the_same_order(): void
    {
        $cake = Cake::factory()->create();
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);
        $data = $this->checkoutData();

        // Simula o segundo clique que ainda carrega a sessão antiga (carrinho e token iguais).
        $session = session()->all();
        $first = $this->post(route('orders.store'), $data);
        $this->withSession($session);
        $second = $this->post(route('orders.store'), $data);

        $this->assertSame(1, Order::count());
        $code = Order::sole()->code;
        $this->assertStringContainsString('/encomenda/confirmada/'.$code, $first->headers->get('Location'));
        $this->assertStringContainsString('/encomenda/confirmada/'.$code, $second->headers->get('Location'));
    }

    public function test_token_from_another_session_is_rejected(): void
    {
        $cake = Cake::factory()->create();
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);
        $data = $this->checkoutData(['checkout_token' => str_repeat('x', 40)]);

        $this->post(route('orders.store'), $data)->assertRedirect(route('cart.show'))->assertSessionHasErrors('checkout_token');
        $this->assertSame(0, Order::count());
    }

    public function test_old_input_arrays_do_not_break_forms(): void
    {
        $cake = Cake::factory()->create();
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);
        $this->from(route('cart.show'))->post(route('orders.store'), $this->checkoutData(['customer_name' => ['x'], 'notes' => ['y']]))
            ->assertRedirect(route('cart.show'));
        $this->get(route('cart.show'))->assertOk();

        $this->from(route('login'))->post(route('login.store'), ['email' => ['a'], 'password' => 'x'])->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
    }

    public function test_array_inputs_do_not_cause_errors(): void
    {
        $this->get('/cardapio?busca[]=x&categoria[]=y')->assertOk();
        $this->post(route('orders.store'), ['customer_name' => ['x'], 'customer_phone' => ['1'], 'checkout_token' => ['z']])->assertRedirect();
    }

    public function test_unavailable_items_leave_the_cart(): void
    {
        $cake = Cake::factory()->create();
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);
        $cake->update(['is_active' => false]);

        $this->get(route('cart.show'))->assertSee('Sua encomenda está vazia');
        $this->assertSame([], session('encomenda.itens'));
    }

    public function test_order_validation(): void
    {
        $cake = Cake::factory()->create();
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);

        $this->from(route('cart.show'))->post(route('orders.store'), $this->checkoutData([
            'customer_name' => 'A',
            'customer_phone' => '123',
            'customer_email' => 'nao-e-email',
            'pickup_date' => now()->toDateString(),
        ]))->assertRedirect(route('cart.show'))
            ->assertSessionHasErrors(['customer_name', 'customer_phone', 'customer_email', 'pickup_date']);

        $this->assertSame(0, Order::count());
        $this->get(route('cart.show'))->assertSee('2 dias de antecedência');
    }

    public function test_empty_cart_cannot_create_order(): void
    {
        $this->post(route('orders.store'), $this->checkoutData())->assertRedirect(route('menu.index'));

        $this->assertSame(0, Order::count());
    }

    public function test_cart_quantity_is_capped_and_removable(): void
    {
        $cake = Cake::factory()->create();

        $this->post(route('cart.store'), ['cake_id' => $cake->id, 'quantity' => 999])->assertSessionHasErrors('quantity');
        $this->post(route('cart.store'), ['cake_id' => $cake->id, 'quantity' => 20]);
        $this->post(route('cart.store'), ['cake_id' => $cake->id, 'quantity' => 5]);
        $this->assertSame([$cake->id => 20], session('encomenda.itens'));

        $this->delete(route('cart.destroy', $cake->id));
        $this->assertSame([], session('encomenda.itens'));
    }

    public function test_orders_are_rate_limited(): void
    {
        $cake = Cake::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('cart.store'), ['cake_id' => $cake->id]);
            $this->post(route('orders.store'), $this->checkoutData())->assertRedirect();
        }
        $this->post(route('cart.store'), ['cake_id' => $cake->id]);
        $this->post(route('orders.store'), $this->checkoutData())->assertStatus(429);
        $this->assertSame(5, Order::count());
    }
}
