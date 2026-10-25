<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['game_id', 'name', 'sku', 'price_idr', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['price_idr' => 'integer', 'is_active' => 'boolean'];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
