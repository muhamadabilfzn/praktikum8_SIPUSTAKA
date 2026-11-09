<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Buku\Models\Buku;
use Tests\TestCase;

class BukuTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'judul' => 'Pemrograman Web',
            'penulis' => 'Budi',
            'penerbit' => 'Informatika',
            'tahun_terbit' => 2022,
            'stok' => 3,
        ], $override);
    }

    public function test_admin_dapat_menambah_mengubah_dan_menghapus_buku(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('buku.store'), $this->payload())
            ->assertRedirect(route('buku.index'))
            ->assertSessionHas('success');
        $buku = Buku::firstOrFail();

        $this->actingAs($admin)->put(route('buku.update', $buku), $this->payload(['stok' => 9]))
            ->assertRedirect(route('buku.index'));
        $this->assertSame(9, $buku->fresh()->stok);

        $this->actingAs($admin)->delete(route('buku.destroy', $buku))->assertRedirect(route('buku.index'));
        $this->assertDatabaseCount('bukus', 0);
    }

    public function test_validasi_buku_menampilkan_error(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('buku.store'), [])
            ->assertSessionHasErrors(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok']);

        $this->actingAs($admin)->post(route('buku.store'), $this->payload(['tahun_terbit' => 1800, 'stok' => -1]))
            ->assertSessionHasErrors(['tahun_terbit', 'stok']);
    }

    public function test_user_hanya_melihat_daftar_buku_tanpa_tombol_kelola(): void
    {
        Buku::factory()->create(['judul' => 'Buku Contoh']);

        $this->actingAs(User::factory()->create())->get(route('buku.index'))
            ->assertOk()
            ->assertSee('Buku Contoh')
            ->assertDontSee('Tambah Buku')
            ->assertDontSee('btn-outline-danger', false)
            ->assertDontSee('btn-outline-primary', false);
    }

    public function test_pencarian_dan_pagination(): void
    {
        Buku::factory()->count(12)->create();
        Buku::factory()->create(['judul' => 'Laravel Mendalam']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('buku.index', ['search' => 'Laravel Mendalam']))
            ->assertOk()->assertSee('Laravel Mendalam');

        $response = $this->actingAs($user)->get(route('buku.index'));
        $this->assertCount(10, $response->viewData('bukus')->items());
        $this->assertSame(13, $response->viewData('bukus')->total());
    }
}
