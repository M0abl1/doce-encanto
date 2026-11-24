<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'category_id', 'name', 'slug', 'summary', 'description', 'price_cents',
    'serves', 'color', 'image_path', 'is_featured', 'is_active',
])]
class Cake extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Busca literal por texto: %, _ e ! digitados não viram curingas.
     * Usa ESCAPE explícito, que funciona igual no SQLite e no MySQL.
     *
     * @param  list<'name'|'summary'>  $columns
     */
    public function scopeSearch(Builder $query, string $term, array $columns = ['name', 'summary']): void
    {
        $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';

        $query->where(function (Builder $query) use ($columns, $like) {
            foreach (array_intersect($columns, ['name', 'summary']) as $column) {
                $query->orWhereRaw($column." LIKE ? ESCAPE '!'", [$like]);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function priceLabel(): string
    {
        return Money::format($this->price_cents);
    }

    /** Tipo de ilustração usado quando o item não tem foto: bolo, torta (doce) ou salgada. */
    public function artKind(): string
    {
        $slug = (string) $this->category?->slug;

        return match (true) {
            str_contains($slug, 'salgad') => 'salgada',
            str_starts_with($slug, 'torta') => 'torta',
            default => 'bolo',
        };
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
