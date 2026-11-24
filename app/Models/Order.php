<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'customer_name', 'customer_phone', 'customer_email', 'pickup_date', 'notes', 'status', 'total_cents',
])]
class Order extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            do {
                $order->code = Str::upper(Str::random(8));
            } while (static::where('code', $order->code)->exists());
        });
    }

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'status' => OrderStatus::class,
            'total_cents' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totalLabel(): string
    {
        return Money::format($this->total_cents);
    }
}
