<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login_pada_semua_halaman_pengelolaan(): void
    {
        foreach (['dashboard', 'buku', 'peminjaman', 'peminjaman/data', 'admin/users'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_register_selalu_membuat_akun_berperan_user(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // upaya menyisipkan role admin harus diabaikan
        ])->assertRedirect(route('dashboard'));

        $this->assertSame(UserRole::User, User::where('email', 'baru@example.com')->firstOrFail()->role);
    }

    public function test_login_berhasil_dan_gagal(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com']);

        $this->post(route('login.store'), ['email' => 'a@example.com', 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->post(route('login.store'), ['email' => 'a@example.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_mendapat_403_pada_halaman_khusus_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('admin/users')->assertForbidden();
        $this->actingAs($user)->get(route('buku.create'))->assertForbidden();
        $this->actingAs($user)->post(route('buku.store'), [])->assertForbidden();
    }

    public function test_dashboard_dapat_dibuka_admin_dan_user(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('dashboard')->assertOk()->assertSee('Total User');
        $this->actingAs(User::factory()->create())->get('dashboard')->assertOk()->assertDontSee('Total User');
    }

    public function test_pengguna_login_tidak_bisa_membuka_login_dan_register(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('login')->assertRedirect(route('dashboard'));
        $this->get('register')->assertRedirect(route('dashboard'));
    }
}
