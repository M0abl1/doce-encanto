<?php

namespace Tests\Feature;

use App\Models\Cake;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** PNG 40x30 real (o container de testes não tem a extensão GD). */
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAACgAAAAeCAIAAADRv8uKAAAARklEQVR4nO3QQREAIBDDwKO6UIJiZPGlBtJPY2Bnsu4+k0gRdQqDicT+CmOJo7zCWOIorzCWOMorjCWO8gpjiaO8wlix1Q8ZYwGa7EXXkgAAAABJRU5ErkJggg==';

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/admin', '/admin/bolos', '/admin/pedidos', '/admin/categorias', '/admin/equipe'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
        $this->post('/admin/bolos', [])->assertRedirect(route('login'));
    }

    public function test_there_is_no_public_registration(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/cadastro')->assertNotFound();
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'equipe@exemplo.test', 'password' => 'senha-forte-1']);

        $this->post(route('login.store'), ['email' => 'equipe@exemplo.test', 'password' => 'errada'])
            ->assertSessionHasErrors(['email' => 'E-mail ou senha inválidos.']);
        $this->assertGuest();

        $this->post(route('login.store'), ['email' => 'equipe@exemplo.test', 'password' => 'senha-forte-1'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_logout_requires_post(): void
    {
        $this->actingAs(User::factory()->create())->get('/sair')->assertStatus(405);
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        User::factory()->create(['email' => 'equipe@exemplo.test', 'password' => 'senha-forte-1']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), ['email' => 'equipe@exemplo.test', 'password' => 'errada'.$i]);
        }
        $this->post(route('login.store'), ['email' => 'equipe@exemplo.test', 'password' => 'senha-forte-1'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertStringContainsString('Muitas tentativas', session('errors')->first('email'));
    }

    public function test_staff_can_create_update_and_delete_cakes_with_photo(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.cakes.store'), [
            'category_id' => $category->id, 'name' => 'Bolo de Teste', 'summary' => 'Resumo',
            'price' => '1.299,90', 'color' => '#AABBCC', 'is_active' => '1',
            'image' => UploadedFile::fake()->createWithContent('bolo.png', base64_decode(self::PNG)),
            'is_admin' => '1', 'id' => 999, // campos extras são ignorados
        ])->assertRedirect(route('admin.cakes.index'));

        $cake = Cake::sole();
        $this->assertSame('bolo-de-teste', $cake->slug);
        $this->assertSame(129990, $cake->price_cents);
        $this->assertNotSame(999, $cake->id);
        Storage::disk('public')->assertExists($cake->image_path);

        $old = $cake->image_path;
        $this->put(route('admin.cakes.update', $cake), [
            'category_id' => $category->id, 'name' => 'Bolo de Teste', 'summary' => 'Novo resumo',
            'price' => '89,90', 'color' => '#AABBCC', 'remove_image' => '1',
        ])->assertRedirect();
        $cake->refresh();
        $this->assertSame(8990, $cake->price_cents);
        $this->assertFalse($cake->is_active);
        $this->assertNull($cake->image_path);
        Storage::disk('public')->assertMissing($old);

        $this->delete(route('admin.cakes.destroy', $cake))->assertRedirect();
        $this->assertSame(0, Cake::count());
    }

    public function test_cake_validation_rejects_bad_price_and_files(): void
    {
        $category = Category::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.cakes.store'), [
            'category_id' => $category->id, 'name' => 'Bolo X', 'summary' => 'Resumo',
            'price' => 'grátis', 'color' => 'red',
            'image' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors(['price_cents', 'color', 'image']);
        $this->assertSame(0, Cake::count());
    }

    public function test_category_with_cakes_cannot_be_deleted(): void
    {
        $cake = Cake::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->delete(route('admin.categories.destroy', $cake->category))->assertSessionHas('error');
        $this->assertTrue($cake->category()->exists());
    }

    public function test_order_status_can_be_updated_with_valid_values_only(): void
    {
        $order = Order::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee($order->code);
        $this->patch(route('admin.orders.update', $order), ['status' => 'hackeado'])->assertSessionHasErrors('status');
        $this->patch(route('admin.orders.update', $order), ['status' => 'pronto'])->assertRedirect();
        $this->assertSame('pronto', $order->fresh()->status->value);
    }

    public function test_only_admins_manage_staff(): void
    {
        $staff = User::factory()->create();
        $this->actingAs($staff);

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->post(route('admin.users.store'), [
            'name' => 'Intruso', 'email' => 'x@exemplo.test', 'password' => 'senha1234', 'password_confirmation' => 'senha1234', 'is_admin' => '1',
        ])->assertForbidden();
        $this->delete(route('admin.users.destroy', $this->admin()))->assertForbidden();
        $this->assertFalse(User::where('email', 'x@exemplo.test')->exists());
    }

    public function test_admin_creates_staff_and_cannot_delete_self(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'Nova Pessoa', 'email' => 'Nova@Exemplo.test', 'password' => 'senha1234', 'password_confirmation' => 'senha1234',
        ])->assertRedirect();
        $created = User::where('email', 'nova@exemplo.test')->sole();
        $this->assertFalse($created->is_admin);
        $this->assertNotSame('senha1234', $created->password);

        $this->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->assertModelExists($admin);
        $this->delete(route('admin.users.destroy', $created))->assertRedirect();
        $this->assertModelMissing($created);
    }

    public function test_create_admin_command(): void
    {
        $this->artisan('loja:criar-admin', ['email' => 'dona@exemplo.test', '--nome' => 'Dona da Loja'])
            ->expectsQuestion('Senha (mínimo 8 caracteres, com letras e números)', 'senha-forte-1')
            ->expectsQuestion('Confirme a senha', 'senha-forte-1')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'dona@exemplo.test')->sole()->is_admin);

        $this->artisan('loja:criar-admin', ['email' => 'outra@exemplo.test'])
            ->expectsQuestion('Senha (mínimo 8 caracteres, com letras e números)', 'curta')
            ->expectsQuestion('Confirme a senha', 'curta')
            ->assertFailed();
    }

    public function test_dashboard_renders(): void
    {
        Order::factory()->count(3)->create();
        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->assertSee('Próximas retiradas');
        $this->get(route('admin.cakes.index'))->assertOk();
        $this->get(route('admin.cakes.create'))->assertOk();
        $this->get(route('admin.categories.index'))->assertOk();
        $this->get(route('admin.orders.index'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
    }
}
