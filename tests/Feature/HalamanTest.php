<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Buku\Models\Buku;
use Tests\TestCase;

class HalamanTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_tamu_dapat_dirender(): void
    {
        $this->get('login')->assertOk()->assertSee('Masuk');
        $this->get('register')->assertOk()->assertSee('Buat Akun');
    }

    public function test_seluruh_halaman_admin_dapat_dirender(): void
    {
        $admin = User::factory()->admin()->create();
        $buku = Buku::factory()->create();
        $this->actingAs($admin);

        foreach ([
            route('dashboard'),
            route('buku.index'),
            route('buku.create'),
            route('buku.edit', $buku),
            route('peminjaman.index'),
            route('admin.users.index'),
            route('admin.users.create'),
            route('admin.users.edit', $admin),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_halaman_peminjaman_memuat_script_ajax_dan_saran_judul_buku(): void
    {
        Buku::factory()->create(['judul' => 'Judul Saran Uji']);

        $this->actingAs(User::factory()->create())->get(route('peminjaman.index'))
            ->assertOk()
            ->assertSee('Judul Saran Uji')
            ->assertSee('processData: false', false)
            ->assertSee('X-CSRF-TOKEN', false);
    }
}
