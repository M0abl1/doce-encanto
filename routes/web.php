<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Site público
Route::get('/', HomeController::class)->name('home');
Route::get('/cardapio', [MenuController::class, 'index'])->name('menu.index');
Route::get('/bolos/{cake}', [MenuController::class, 'show'])->name('menu.show');

Route::prefix('encomenda')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/itens', [CartController::class, 'store'])->name('store');
    Route::patch('/itens/{cake}', [CartController::class, 'update'])->whereNumber('cake')->name('update');
    Route::delete('/itens/{cake}', [CartController::class, 'destroy'])->whereNumber('cake')->name('destroy');
});

Route::post('/encomenda', [OrderController::class, 'store'])->middleware('throttle:orders')->name('orders.store');
Route::get('/encomenda/confirmada/{code}', [OrderController::class, 'confirmation'])->where('code', '[A-Z0-9]{8}')
    ->middleware('signed')->name('orders.confirmation');

// Login da equipe (não existe cadastro público)
Route::middleware('guest')->group(function () {
    Route::get('/entrar', [LoginController::class, 'create'])->name('login');
    Route::post('/entrar', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});
Route::post('/sair', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Painel administrativo
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::resource('bolos', Admin\CakeController::class)->except('show')
        ->parameters(['bolos' => 'cake'])->names('cakes');
    Route::resource('categorias', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['categorias' => 'category'])->names('categories');
    Route::resource('pedidos', Admin\OrderController::class)->only(['index', 'show', 'update'])
        ->parameters(['pedidos' => 'order'])->names('orders');
    Route::resource('equipe', Admin\UserController::class)->only(['index', 'store', 'destroy'])
        ->parameters(['equipe' => 'user'])->names('users');
});
