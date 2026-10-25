<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_the_wondr_catalog(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertInertia(fn ($page) => $page->component('Welcome')->has('games'));
    }
}
