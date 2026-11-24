<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\Cart;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, Cart $cart): RedirectResponse
    {
        // Chave de idempotência: o formulário traz um token único por checkout.
        // Reenvios (clique duplo, voltar e enviar de novo) com o mesmo token abrem o pedido já criado.
        $token = $request->validated('checkout_token');

        // O token precisa ser o desta sessão: ninguém reaproveita o token de outra pessoa.
        if (! hash_equals((string) $request->session()->get('checkout_token'), $token)) {
            return redirect()->route('cart.show')
                ->withErrors(['checkout_token' => 'O formulário expirou. Confira sua encomenda e envie de novo.']);
        }

        if ($existing = Order::where('checkout_token', $token)->first()) {
            return $this->redirectToConfirmation($existing);
        }

        try {
            $order = DB::transaction(function () use ($request, $cart, $token) {
                $lines = $cart->lines(); // preços e itens lidos do banco dentro da transação
                if ($lines->isEmpty()) {
                    return null;
                }

                $order = new Order([
                    ...$request->safe()->except('checkout_token'),
                    'status' => 'novo',
                    'total_cents' => $lines->sum('subtotal_cents'),
                ]);
                $order->checkout_token = $token;
                $order->save();

                foreach ($lines as $line) {
                    $order->items()->create([
                        'cake_id' => $line['cake']->id,
                        'cake_name' => $line['cake']->name,
                        'quantity' => $line['quantity'],
                        'unit_price_cents' => $line['cake']->price_cents,
                    ]);
                }

                return $order;
            });
        } catch (UniqueConstraintViolationException) {
            // Outro envio com o mesmo token terminou primeiro.
            return $this->redirectToConfirmation(Order::where('checkout_token', $token)->firstOrFail());
        }

        if (! $order) {
            return redirect()->route('menu.index')->with('status', 'Sua encomenda está vazia. Escolha um bolo primeiro.');
        }

        $cart->clear();
        $request->session()->forget('checkout_token');

        return $this->redirectToConfirmation($order);
    }

    public function confirmation(string $code): View
    {
        // A assinatura é validada pelo middleware antes desta busca.
        $order = Order::where('code', $code)->with('items')->firstOrFail();

        return view('orders.confirmation', ['order' => $order]);
    }

    /** Link assinado e com validade: só quem fez o pedido vê a confirmação. */
    private function redirectToConfirmation(Order $order): RedirectResponse
    {
        return redirect()->to(URL::temporarySignedRoute('orders.confirmation', now()->addDays(30), ['code' => $order->code]));
    }
}
