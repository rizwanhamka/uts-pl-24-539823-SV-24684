<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil_mengembalikan_token(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'rahasia123'])
            ->assertOk()
            ->assertJsonStructure(['message', 'token']);
    }

    public function test_login_dengan_password_salah_ditolak_401(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'salah'])
            ->assertStatus(401);
    }

    public function test_login_tanpa_data_mengembalikan_422(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_menghapus_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('uji')->plainTextToken;

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
