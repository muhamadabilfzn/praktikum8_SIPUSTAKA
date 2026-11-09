<?php

use Illuminate\Support\Facades\Route;
use Modules\Buku\Http\Controllers\BukuController;

// Hanya admin: tambah, ubah, dan hapus buku
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('buku', BukuController::class)->except(['index', 'show']);
});

// Admin dan user: melihat daftar buku
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
});
