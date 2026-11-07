<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class HealthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_mengembalikan_200_saat_sehat(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('status', 'healthy')
            ->assertJsonPath('checks.database.status', 'up')
            ->assertJsonPath('checks.cache.status', 'up');
    }

    public function test_health_mengembalikan_503_dan_menyebut_komponen_bermasalah(): void
    {
        Cache::shouldReceive('put')->andThrow(new \RuntimeException('cache mati'));

        $this->getJson('/health')
            ->assertStatus(503)
            ->assertJsonPath('status', 'unhealthy')
            ->assertJsonPath('failed', ['cache'])
            ->assertJsonPath('checks.database.status', 'up');
    }

    public function test_log_login_gagal_tidak_memuat_password_atau_email(): void
    {
        Log::spy();
        User::factory()->create(['email' => 'rahasia@example.com', 'password' => 'benar12345']);

        $this->postJson('/api/login', ['email' => 'rahasia@example.com', 'password' => 'salah-sekali'])
            ->assertStatus(401);

        Log::shouldHaveReceived('warning')->withArgs(function ($message, $context) {
            $dump = json_encode($context);

            return $message === 'Login gagal'
                && ! str_contains($dump, 'salah-sekali')
                && ! str_contains($dump, 'rahasia@example.com');
        });
    }
}
