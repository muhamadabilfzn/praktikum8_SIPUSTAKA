@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <a href="{{ route('buku.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Kembali ke daftar buku
            </a>
            <h4 class="mt-2 mb-4">Edit Buku</h4>

            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('buku.update', $buku) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('buku::partials.form', ['buku' => $buku])

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('buku.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
