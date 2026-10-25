<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_reads_active_games_from_database(): void
    {
        $game = Game::create(['name' => 'Valorant', 'category' => 'FPS', 'subtitle' => 'Points', 'mark' => 'V', 'accent' => '#ff858b', 'background' => '#4f3037', 'is_active' => true]);

        $this->get('/')->assertOk()->assertInertia(fn ($page) => $page->component('Welcome')->has('games', 1)->where('games.0.name', 'Valorant'));
        $this->get('/games/valorant')->assertOk()->assertInertia(fn ($page) => $page->component('Catalog/Show')->where('game.name', 'Valorant'));
        $this->assertDatabaseHas('games', ['id' => $game->id, 'slug' => 'valorant']);
    }

    public function test_checkout_prices_from_database_and_never_fulfills_sandbox_order(): void
    {
        config(['services.wondr.sandbox_enabled' => true]);
        $game = Game::create(['name' => 'Valorant', 'category' => 'FPS', 'subtitle' => 'Points', 'mark' => 'V', 'accent' => '#ff858b', 'background' => '#4f3037', 'is_active' => true]);
        $product = Product::create(['game_id' => $game->id, 'name' => '475 Points', 'sku' => 'valorant-475', 'price_idr' => 50000, 'is_active' => true]);

        $response = $this->post('/orders', ['game_id' => $game->id, 'product_id' => $product->id, 'player_id' => '123456789', 'customer_email' => 'gamer@example.com', 'payment_method' => 'sandbox', 'terms' => true]);
        $response->assertRedirect();
        $order = \App\Models\Order::firstOrFail();
        $this->assertSame(50000, $order->amount_idr);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('not_started', $order->fulfillment_status);

        $this->post('/orders/'.$order->reference.'/sandbox')->assertRedirect('/orders/'.$order->reference);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'sandbox_paid', 'fulfillment_status' => 'manual_review']);
    }

    public function test_checkout_rejects_client_prices_and_inactive_products(): void
    {
        config(['services.wondr.sandbox_enabled' => true]);
        $game = Game::create(['name' => 'Roblox', 'category' => 'Sandbox', 'subtitle' => 'Robux', 'mark' => 'R', 'accent' => '#e5e8ed', 'background' => '#333740', 'is_active' => true]);
        $product = Product::create(['game_id' => $game->id, 'name' => 'Robux', 'sku' => 'roblox-1', 'price_idr' => 25000, 'is_active' => false]);

        $this->post('/orders', ['game_id' => $game->id, 'product_id' => $product->id, 'player_id' => '123456', 'customer_email' => 'gamer@example.com', 'payment_method' => 'sandbox', 'terms' => true, 'amount_idr' => 1])->assertNotFound();
        $this->assertDatabaseCount('orders', 0);
    }
}
