@extends('layouts.app')

@section('title', 'Data Buku')

@section('content')
    @php($isAdmin = auth()->user()->isAdmin())

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Data Buku</h4>
            <p class="text-muted mb-0 small">
                {{ $isAdmin ? 'Tambah, ubah, dan hapus koleksi buku perpustakaan.' : 'Daftar koleksi buku perpustakaan kampus.' }}
            </p>
        </div>
        @if ($isAdmin)
            <a href="{{ route('buku.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Buku
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('buku.index') }}" class="row g-2 align-items-center">
                <div class="col-sm-8 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari judul, penulis, atau penerbit...">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light border">Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('buku.index') }}" class="btn btn-link text-decoration-none">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        @if ($isAdmin)
                            <th class="text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bukus as $buku)
                        <tr>
                            <td class="text-muted">{{ $bukus->firstItem() + $loop->index }}</td>
                            <td class="fw-medium">{{ $buku->judul }}</td>
                            <td>{{ $buku->penulis }}</td>
                            <td>{{ $buku->penerbit }}</td>
                            <td>{{ $buku->tahun_terbit }}</td>
                            <td>
                                <span class="badge {{ $buku->stok > 0 ? 'text-bg-success' : 'text-bg-danger' }}">{{ $buku->stok }}</span>
                            </td>
                            @if ($isAdmin)
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('buku.edit', $buku) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="d-inline" data-confirm="Buku &quot;{{ $buku->judul }}&quot; akan dihapus dan tidak bisa dikembalikan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}">
                                <div class="empty-state">
                                    <i class="bi bi-journal-x"></i>
                                    <p class="mt-2 mb-0">
                                        {{ $search !== '' ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada data buku.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bukus->hasPages())
            <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <small class="text-muted">Menampilkan {{ $bukus->firstItem() }}-{{ $bukus->lastItem() }} dari {{ $bukus->total() }} buku</small>
                {{ $bukus->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
