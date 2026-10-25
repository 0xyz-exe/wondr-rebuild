<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            ['Mobile Legends', 'MOBA', 'Diamonds', 'ML', '#74d8cf', '#173b45'],
            ['Free Fire', 'Battle Royale', 'Diamonds', 'FF', '#ffb078', '#503427'],
            ['PUBG Mobile', 'Battle Royale', 'UC', 'P', '#f4c24e', '#51452a'],
            ['Genshin Impact', 'RPG', 'Genesis Crystals', 'G', '#82bde8', '#24394a'],
            ['Honkai: Star Rail', 'RPG', 'Oneiric Shards', 'HSR', '#d8a8e8', '#3d304b'],
            ['Wuthering Waves', 'RPG', 'Lunites', 'W', '#b1d7ef', '#2d4051'],
            ['Valorant', 'FPS', 'Points', 'V', '#ff858b', '#4f3037'],
            ['Call of Duty: Mobile', 'FPS', 'CP', 'COD', '#b8c889', '#344233'],
            ['Honor of Kings', 'MOBA', 'Tokens', 'HOK', '#e6c774', '#49412e'],
            ['Roblox', 'Sandbox', 'Robux', 'R', '#e5e8ed', '#333740'],
            ['Steam Wallet', 'Voucher', 'Wallet Code', 'S', '#85b8d8', '#293c4a'],
            ['EA SPORTS FC Mobile', 'Sports', 'FC Points', 'FC', '#b7d6a2', '#334637'],
        ];

        foreach ($games as $position => [$name, $category, $subtitle, $mark, $accent, $background]) {
            $game = Game::create([
                'name' => $name,
                'category' => $category,
                'subtitle' => $subtitle,
                'mark' => $mark,
                'accent' => $accent,
                'background' => $background,
                'is_active' => false,
                'sort_order' => $position,
            ]);

            Product::create([
                'game_id' => $game->id,
                'name' => 'Paket contoh (nonaktif)',
                'sku' => 'sample-' . $game->slug,
                'price_idr' => 0,
                'is_active' => false,
                'sort_order' => 0,
            ]);
        }
    }
}
