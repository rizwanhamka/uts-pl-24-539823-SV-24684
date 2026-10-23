<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BukuApiTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'judul' => 'Laravel Dasar',
        'penulis' => 'Budi Santoso',
        'tahun_terbit' => 2024,
        'stok' => 10,
    ];

    public function test_endpoint_buku_menolak_permintaan_tanpa_token(): void
    {
        $this->getJson('/api/buku')->assertStatus(401);
    }

    public function test_menambah_buku_mengembalikan_201(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/buku', $this->payload)
            ->assertStatus(201)
            ->assertJsonPath('data.judul', 'Laravel Dasar');

        $this->assertDatabaseHas('bukus', ['judul' => 'Laravel Dasar']);
    }

    public function test_validasi_gagal_mengembalikan_422(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/buku', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'penulis', 'tahun_terbit', 'stok']);
    }

    public function test_menampilkan_mengubah_dan_menghapus_buku(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $buku = Buku::factory()->create();

        $this->getJson("/api/buku/{$buku->id}")->assertOk()->assertJsonPath('data.id', $buku->id);

        $this->putJson("/api/buku/{$buku->id}", $this->payload)->assertOk();
        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'judul' => 'Laravel Dasar']);

        $this->deleteJson("/api/buku/{$buku->id}")->assertOk();
        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }
}
