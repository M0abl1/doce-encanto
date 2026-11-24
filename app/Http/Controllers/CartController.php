<?php

namespace App\Http\Controllers;

use App\Models\Cake;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Request $request, Cart $cart): View
    {
        $lines = $cart->lines();
        if (! $request->session()->has('checkout_token')) {
            $request->session()->put('checkout_token', Str::random(40));
        }

        return view('cart.show', [
            'lines' => $lines,
            'total' => (int) $lines->sum('subtotal_cents'),
            'minDate' => now()->addDays(2)->toDateString(),
            'checkoutToken' => $request->session()->get('checkout_token'),
        ]);
    }

    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'cake_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.Cart::MAX_PER_ITEM],
        ]);
        $cake = Cake::active()->findOrFail($data['cake_id']);

        if (! $cart->add($cake, (int) ($data['quantity'] ?? 1))) {
            return redirect()->route('cart.show')
                ->with('status', 'Sua encomenda já tem '.Cart::MAX_ITEMS.' itens diferentes. Finalize esta ou remova algum item.');
        }

        return redirect()->route('cart.show')->with('status', "{$cake->name} foi adicionado à sua encomenda.");
    }

    public function update(Request $request, Cart $cart, int $cake): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.Cart::MAX_PER_ITEM]]);
        $cart->update($cake, (int) $data['quantity']);

        return redirect()->route('cart.show');
    }

    public function destroy(Cart $cart, int $cake): RedirectResponse
    {
        $cart->remove($cake);

        return redirect()->route('cart.show');
    }
}
