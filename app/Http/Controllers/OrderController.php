<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(app()->environment(['local', 'testing']) && config('services.wondr.sandbox_enabled'), 404);

        $data = $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'player_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{3,50}$/'],
            'server_id' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]{1,30}$/'],
            'customer_email' => ['required', 'email:rfc', 'max:190'],
            'payment_method' => ['required', 'in:sandbox'],
            'terms' => ['accepted'],
        ]);

        $game = Game::query()->whereKey($data['game_id'])->where('is_active', true)->firstOrFail();
        $product = Product::query()->whereKey($data['product_id'])->where('game_id', $game->id)->where('is_active', true)->firstOrFail();

        $order = DB::transaction(fn () => Order::create([
            'game_id' => $game->id,
            'product_id' => $product->id,
            'player_id' => $data['player_id'],
            'server_id' => $data['server_id'] ?? null,
            'customer_email' => $data['customer_email'],
            'payment_method' => $data['payment_method'],
            'amount_idr' => $product->price_idr,
            'payment_status' => 'pending',
            'fulfillment_status' => 'not_started',
            'expires_at' => now()->addMinutes(30),
        ]));

        return redirect()->route('orders.sandbox', $order);
    }

    public function sandbox(Order $order): Response
    {
        $this->assertSandbox($order);
        return Inertia::render('Orders/Sandbox', ['order' => $this->publicOrder($order)]);
    }

    public function confirmSandbox(Order $order): RedirectResponse
    {
        abort_unless(app()->environment(['local', 'testing']) && config('services.wondr.sandbox_enabled'), 404);

        DB::transaction(function () use ($order): void {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_if($locked->payment_status !== 'pending' || $locked->expires_at?->isPast(), 409);
            $locked->update(['payment_status' => 'sandbox_paid', 'fulfillment_status' => 'manual_review']);
        });

        return redirect()->route('orders.show', $order)->with('status', 'Simulasi dicatat. Tidak ada pembayaran atau top-up nyata.');
    }

    public function show(Order $order): Response
    {
        $this->assertSandbox($order);
        return Inertia::render('Orders/Show', ['order' => $this->publicOrder($order)]);
    }

    private function assertSandbox(Order $order): void
    {
        abort_unless(app()->environment(['local', 'testing']) && config('services.wondr.sandbox_enabled'), 404);
    }

    private function publicOrder(Order $order): array
    {
        return [
            'reference' => $order->reference,
            'game' => $order->game()->value('name'),
            'product' => $order->product()->value('name'),
            'player_id' => str_repeat('*', max(0, mb_strlen($order->player_id) - 2)) . mb_substr($order->player_id, -2),
            'amount_idr' => $order->amount_idr,
            'payment_status' => $order->payment_status,
            'fulfillment_status' => $order->fulfillment_status,
            'expires_at' => $order->expires_at?->toIso8601String(),
        ];
    }
}
