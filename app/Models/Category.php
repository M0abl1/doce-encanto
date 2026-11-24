<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'position'])]
class Category extends Model
{
    use HasFactory;

    public function cakes(): HasMany
    {
        return $this->hasMany(Cake::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
