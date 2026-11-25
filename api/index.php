<?php

/**
 * Entrada da função serverless na Vercel (runtime vercel-php).
 *
 * Na Vercel só /tmp aceita escrita, então o banco SQLite é criado ali a cada
 * "cold start": roda as migrations, carrega o cardápio e cria o administrador
 * definido em ADMIN_EMAIL / ADMIN_PASSWORD. Os dados não são permanentes.
 */

use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$database = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';

if (getenv('DB_CONNECTION') === 'sqlite' && ! file_exists($database)) {
    touch($database);

    $setup = require __DIR__.'/../bootstrap/app.php';
    $console = $setup->make(Kernel::class);
    $console->call('migrate', ['--force' => true]);
    $console->call('db:seed', ['--class' => CatalogSeeder::class, '--force' => true]);

    if (getenv('ADMIN_EMAIL') && getenv('ADMIN_PASSWORD')) {
        $admin = new User([
            'name' => 'Administrador',
            'email' => mb_strtolower(trim(getenv('ADMIN_EMAIL'))),
            'password' => getenv('ADMIN_PASSWORD'),
        ]);
        $admin->is_admin = true;
        $admin->save();
    }
}

$app = require __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
