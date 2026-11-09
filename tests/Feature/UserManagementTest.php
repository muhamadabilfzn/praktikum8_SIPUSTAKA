<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_menambah_mengubah_role_dan_menghapus_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru', 'email' => 'petugas@example.com', 'role' => 'admin',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect(route('admin.users.index'));
        $baru = User::where('email', 'petugas@example.com')->firstOrFail();
        $this->assertSame(UserRole::Admin, $baru->role);

        $this->actingAs($admin)->put(route('admin.users.update', $baru), [
            'name' => 'Petugas Baru', 'email' => 'petugas@example.com', 'role' => 'user',
        ])->assertRedirect(route('admin.users.index'));
        $this->assertSame(UserRole::User, $baru->fresh()->role);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $baru))->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['email' => 'petugas@example.com']);
    }

    public function test_admin_tidak_dapat_menghapus_atau_menurunkan_role_akunnya_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'role' => 'user',
        ])->assertSessionHasErrors('role');
        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }
}
