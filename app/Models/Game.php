<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = ['name', 'slug', 'category', 'subtitle', 'mark', 'accent', 'background', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(fn (Game $game) => $game->slug ??= Str::slug($game->name));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
