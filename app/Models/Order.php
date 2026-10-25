<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = ['reference', 'game_id', 'product_id', 'player_id', 'server_id', 'customer_email', 'payment_method', 'amount_idr', 'payment_status', 'fulfillment_status', 'expires_at'];

    protected function casts(): array
    {
        return ['amount_idr' => 'integer', 'expires_at' => 'immutable_datetime'];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->reference ??= 'WNDR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(16));
        });
    }
}
