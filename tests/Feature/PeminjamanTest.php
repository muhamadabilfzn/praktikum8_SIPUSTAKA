<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Peminjaman\Models\Peminjaman;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use RefreshDatabase;

    private array $json = ['Accept' => 'application/json'];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'nama_peminjam' => 'Siti',
            'judul_buku' => 'Clean Code',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_kembali' => '2026-11-08',
            'status' => 'Dipinjam',
            'berkas' => UploadedFile::fake()->create('bukti.jpg', 50, 'image/jpeg'),
        ], $override);
    }

    public function test_admin_dan_user_dapat_menambah_data_beserta_berkas(): void
    {
        foreach ([User::factory()->admin()->create(), User::factory()->create()] as $user) {
            $this->actingAs($user)->post(route('peminjaman.store'), $this->payload(), $this->json)
                ->assertOk()->assertJsonStructure(['success']);
        }

        $this->assertDatabaseCount('peminjamans', 2);
        foreach (Peminjaman::all() as $item) {
            Storage::disk('public')->assertExists($item->berkas);
        }
    }

    public function test_data_json_memuat_berkas_url(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('peminjaman.store'), $this->payload(), $this->json)->assertOk();

        $this->actingAs($user)->getJson(route('peminjaman.data'))
            ->assertOk()
            ->assertJsonPath('0.nama_peminjam', 'Siti')
            ->assertJsonPath('0.tanggal_pinjam', '2026-11-01')
            ->assertJsonStructure([['id', 'berkas_url', 'nama_berkas']]);
    }

    public function test_validasi_422_pada_form_ajax(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('peminjaman.store'), [], $this->json)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'berkas']);

        $this->actingAs($user)->post(route('peminjaman.store'), $this->payload([
            'tanggal_kembali' => '2026-10-01',
            'status' => 'Hilang',
            'berkas' => UploadedFile::fake()->create('rahasia.zip', 10, 'application/zip'),
        ]), $this->json)->assertStatus(422)->assertJsonValidationErrors(['tanggal_kembali', 'status', 'berkas']);

        $this->actingAs($user)->post(route('peminjaman.store'), $this->payload([
            'berkas' => UploadedFile::fake()->create('besar.pdf', 3000, 'application/pdf'),
        ]), $this->json)->assertStatus(422)->assertJsonValidationErrors(['berkas']);
    }

    public function test_edit_mengganti_berkas_lama_dan_boleh_tanpa_berkas_baru(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('peminjaman.store'), $this->payload(), $this->json)->assertOk();
        $item = Peminjaman::firstOrFail();
        $berkasLama = $item->berkas;

        // Tanpa berkas baru: berkas lama dipertahankan
        $this->actingAs($user)->post(route('peminjaman.update', $item), $this->payload([
            '_method' => 'PUT', 'status' => 'Dikembalikan', 'berkas' => null,
        ]), $this->json)->assertOk();
        $this->assertSame($berkasLama, $item->fresh()->berkas);
        $this->assertSame('Dikembalikan', $item->fresh()->status);

        // Dengan berkas baru: berkas lama dihapus dari storage
        $this->actingAs($user)->post(route('peminjaman.update', $item), $this->payload([
            '_method' => 'PUT', 'berkas' => UploadedFile::fake()->create('baru.pdf', 20, 'application/pdf'),
        ]), $this->json)->assertOk();
        Storage::disk('public')->assertMissing($berkasLama);
        Storage::disk('public')->assertExists($item->fresh()->berkas);
    }

    public function test_hapus_data_ikut_menghapus_berkas_di_storage(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('peminjaman.store'), $this->payload(), $this->json)->assertOk();
        $item = Peminjaman::firstOrFail();

        $this->actingAs($user)->deleteJson(route('peminjaman.destroy', $item))->assertOk();

        $this->assertDatabaseCount('peminjamans', 0);
        Storage::disk('public')->assertMissing($item->berkas);
    }

    public function test_tamu_tidak_dapat_memakai_endpoint_ajax(): void
    {
        $this->getJson(route('peminjaman.data'))->assertUnauthorized();
    }
}
