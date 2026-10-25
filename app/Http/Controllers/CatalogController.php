<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(): Response
    {
        $games = Game::query()->where('is_active', true)->withCount(['products' => fn ($query) => $query->where('is_active', true)])->orderBy('sort_order')->get();

        return Inertia::render('Welcome', [
            'games' => $games->map(fn (Game $game) => [
                'name' => $game->name, 'category' => $game->category, 'subtitle' => $game->subtitle,
                'mark' => $game->mark, 'color' => $game->accent, 'tone' => $game->background,
                'url' => route('games.show', $game),
            ]),
        ]);
    }

    public function show(Game $game): Response
    {
        abort_unless($game->is_active, 404);

        return Inertia::render('Catalog/Show', [
            'game' => [
                'id' => $game->id, 'name' => $game->name, 'category' => $game->category,
                'mark' => $game->mark, 'color' => $game->accent, 'tone' => $game->background,
                'products' => $game->products()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'price_idr']),
            ],
        ]);
    }
}
