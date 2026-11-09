# SiPustaka (Laravel 12 + HMVC)

Aplikasi perpustakaan kampus untuk Tugas Praktikum Modul 8 (HMVC, Authorization, dan AJAX).
Project ini sudah lengkap: `.env`, modul (`Modules/`), migration, seeder, dan tes otomatis sudah tersedia.
Folder `vendor/` tidak disertakan, jadi jalankan `composer install` terlebih dahulu.

## Cara menjalankan
1. Nyalakan Apache dan MySQL (XAMPP/Laragon), lalu buat database kosong bernama **db_sipustaka** di phpMyAdmin.
   (Jika MySQL Anda memakai password, isi `DB_PASSWORD` di file `.env`.)
2. Buka terminal di folder project ini, lalu jalankan:
   ```bash
   composer install
   php artisan migrate:fresh --seed
   php artisan storage:link
   php artisan serve
   ```
3. Buka http://127.0.0.1:8000

## Akun uji
| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@example.com | password |
| User  | user@example.com  | password |

## Perintah untuk bukti di laporan
```bash
php artisan module:list
php artisan route:list --except-vendor
php artisan test
```

## Pembagian modul
| Bagian | Letak | Jenis | Akses |
|---|---|---|---|
| Register, Login, Logout, Dashboard | `app/`, `routes/web.php` | Form biasa | Tamu / semua yang login |
| Middleware `role` | `app/Http/Middleware/EnsureUserHasRole.php` | Authorization | - |
| Buku | `Modules/Buku` | CRUD synchronous (+ pencarian, pagination) | Admin: tambah/ubah/hapus. User: lihat daftar |
| Peminjaman | `Modules/Peminjaman` | CRUD AJAX + upload bukti | Admin dan User |
| UserManagement | `Modules/UserManagement` | CRUD synchronous | Admin |

## Checklist screenshot untuk laporan
1. `/register` dan `/login` (termasuk pesan error).
2. Dashboard admin (ada kartu Total User) dan dashboard user.
3. Login sebagai user, buka `/admin/users` atau `/buku/create` -> error **403**.
4. Modul Buku: tambah, edit, hapus, pesan error validasi, pencarian, pagination.
5. Modul Peminjaman: modal tambah/edit, error 422 (kosongkan form, upload .zip, upload > 2 MB), tabel berubah tanpa refresh, tautan bukti terbuka, berkas diganti saat edit.
6. DevTools > Network > Fetch/XHR saat menambah/mengubah/menghapus peminjaman.
7. Folder `storage/app/public/berkas_peminjaman` dan kolom `berkas` pada tabel `peminjamans` di phpMyAdmin.
8. Modul UserManagement: tambah/ubah (termasuk role)/hapus user; coba hapus akun sendiri.
9. Hasil `php artisan module:list` dan `php artisan route:list --except-vendor`.

## Jika ada error
- `Class "Modules\..." not found` -> `composer dump-autoload`.
- Error 419 pada AJAX -> muat ulang halaman (token CSRF kedaluwarsa).
- Berkas bukti tidak bisa dibuka (404) -> `php artisan storage:link`.
- `Access denied` / `Unknown database` -> cek database `db_sipustaka` sudah dibuat dan `DB_USERNAME`/`DB_PASSWORD` di `.env` benar.
