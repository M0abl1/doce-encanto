<?php

namespace App\Services;

use App\Models\Cake;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Carrinho da encomenda guardado na sessão: apenas [cake_id => quantidade].
 * Preços e nomes sempre vêm do banco, nunca do navegador.
 */
class Cart
{
    public const MAX_PER_ITEM = 20;

    public const MAX_ITEMS = 15;

    private const KEY = 'encomenda.itens';

    public function __construct(private readonly Session $session) {}

    /** @return array<int, int> */
    public function raw(): array
    {
        return $this->session->get(self::KEY, []);
    }

    /** Adiciona o item; retorna false se o carrinho já tem o máximo de itens diferentes. */
    public function add(Cake $cake, int $quantity): bool
    {
        $this->lines(); // remove itens que saíram do cardápio antes de contar as vagas
        $items = $this->raw();
        if (! isset($items[$cake->id]) && count($items) >= self::MAX_ITEMS) {
            return false;
        }
        $items[$cake->id] = min(self::MAX_PER_ITEM, ($items[$cake->id] ?? 0) + max(1, $quantity));
        $this->session->put(self::KEY, $items);

        return true;
    }

    public function update(int $cakeId, int $quantity): void
    {
        $items = $this->raw();
        if (! isset($items[$cakeId])) {
            return;
        }
        if ($quantity < 1) {
            unset($items[$cakeId]);
        } else {
            $items[$cakeId] = min(self::MAX_PER_ITEM, $quantity);
        }
        $this->session->put(self::KEY, $items);
    }

    public function remove(int $cakeId): void
    {
        $this->update($cakeId, 0);
    }

    public function clear(): void
    {
        $this->session->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Linhas do carrinho com bolos ativos carregados do banco.
     * Itens desativados ou excluídos são retirados da sessão.
     *
     * @return Collection<int, array{cake: Cake, quantity: int, subtotal_cents: int}>
     */
    public function lines(): Collection
    {
        $items = $this->raw();
        if ($items === []) {
            return collect();
        }

        $cakes = Cake::active()->with('category')->whereIn('id', array_keys($items))->orderBy('name')->get();

        if ($cakes->count() !== count($items)) {
            $this->session->put(self::KEY, array_intersect_key($items, array_flip($cakes->modelKeys())));
        }

        return $cakes->map(fn (Cake $cake) => [
            'cake' => $cake,
            'quantity' => $items[$cake->id],
            'subtotal_cents' => $cake->price_cents * $items[$cake->id],
        ]);
    }
}
