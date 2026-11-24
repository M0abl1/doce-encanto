<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Cart;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Cart::class, fn ($app) => new Cart($app['session.store']));
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        // Lê um campo como texto; arrays e outros tipos viram '' (evita erro 500 com "campo[]=").
        Request::macro('text', function (string $key): string {
            $value = $this->input($key);

            return is_string($value) ? $value : '';
        });
        Carbon::setLocale(config('app.locale'));

        // Só administradores gerenciam a equipe.
        Gate::define('manage-staff', fn (User $user) => $user->is_admin);

        // Pedidos públicos: no máximo 5 a cada 10 minutos por IP.
        RateLimiter::for('orders', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));

        // Login: além do limite por e-mail + IP no LoginRequest, 20 por minuto por IP.
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));

        View::composer('components.layouts.site', fn ($view) => $view->with('cartCount', app(Cart::class)->count()));
    }
}
