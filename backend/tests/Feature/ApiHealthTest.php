<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    public function test_api_up_returns_json_status_ok(): void
    {
        $response = $this->getJson('/api/up');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    public function test_api_non_existing_route_returns_json_404(): void
    {
        $response = $this->getJson('/api/non-existing-endpoint');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Data tidak ditemukan.',
            ]);
    }
}
