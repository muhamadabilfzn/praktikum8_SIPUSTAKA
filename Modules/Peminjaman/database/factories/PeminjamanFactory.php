<?php

namespace Modules\Peminjaman\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Peminjaman\Models\Peminjaman;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Peminjaman::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pinjam = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'nama_peminjam' => fake()->name(),
            'judul_buku' => fake()->sentence(3),
            'tanggal_pinjam' => $pinjam->format('Y-m-d'),
            'tanggal_kembali' => (clone $pinjam)->modify('+7 days')->format('Y-m-d'),
            'status' => fake()->randomElement(['Dipinjam', 'Dikembalikan']),
        ];
    }
}
