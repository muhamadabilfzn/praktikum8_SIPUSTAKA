<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Buku\Models\Buku;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Anggota Perpustakaan',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        // Data awal buku (12 data agar pagination 10 data per halaman terlihat)
        $books = [
            ['Pemrograman Web dengan Laravel', 'Budi Raharjo', 'Informatika', 2022, 5],
            ['Basis Data: Konsep dan Implementasi', 'Fathansyah', 'Informatika', 2018, 4],
            ['Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika', 2016, 6],
            ['Rekayasa Perangkat Lunak', 'Rosa A. S.', 'Informatika', 2020, 3],
            ['Jaringan Komputer', 'Andrew S. Tanenbaum', 'Pearson', 2011, 2],
            ['Sistem Operasi Modern', 'Andrew S. Tanenbaum', 'Pearson', 2014, 3],
            ['Kecerdasan Buatan', 'Stuart Russell', 'Pearson', 2020, 2],
            ['Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 4],
            ['Pengantar Statistika', 'Ronald E. Walpole', 'ITB Press', 2015, 7],
            ['Matematika Diskrit', 'Rinaldi Munir', 'Informatika', 2019, 5],
            ['Interaksi Manusia dan Komputer', 'Jenny Preece', 'Andi', 2017, 3],
            ['Keamanan Informasi', 'Man Young Rhee', 'Andi', 2021, 2],
        ];

        foreach ($books as [$judul, $penulis, $penerbit, $tahun, $stok]) {
            Buku::create([
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun,
                'stok' => $stok,
            ]);
        }
    }
}
