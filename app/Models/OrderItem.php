<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cake_id', 'cake_name', 'quantity', 'unit_price_cents'])]
class OrderItem extends Model
{
    protected function casts(): array
    {
        return ['quantity' => 'integer', 'unit_price_cents' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cake(): BelongsTo
    {
        return $this->belongsTo(Cake::class);
    }

    public function subtotalLabel(): string
    {
        return Money::format($this->quantity * $this->unit_price_cents);
    }
}
