<div class="mb-3">
    <label for="judul" class="form-label">Judul Buku <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $buku?->judul) }}" required>
    @error('judul')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('penulis') is-invalid @enderror" id="penulis" name="penulis" value="{{ old('penulis', $buku?->penulis) }}" required>
        @error('penulis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="penerbit" class="form-label">Penerbit <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('penerbit') is-invalid @enderror" id="penerbit" name="penerbit" value="{{ old('penerbit', $buku?->penerbit) }}" required>
        @error('penerbit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="tahun_terbit" class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('tahun_terbit') is-invalid @enderror" id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $buku?->tahun_terbit) }}" min="1900" max="{{ date('Y') }}" placeholder="Contoh: 2022" required>
        @error('tahun_terbit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $buku?->stok ?? 1) }}" min="0" required>
        @error('stok')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
