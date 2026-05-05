@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Input Barang Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Aset (Unik)</label>
                            <input type="text" name="kode_aset" class="form-control" placeholder="Contoh: INV-2026-001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Mebel">Mebel</option>
                                <option value="Alat Tulis Kantor">Alat Tulis Kantor</option>
                                <option value="Perlengkapan Kebersihan">Perlengkapan Kebersihan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merek</label>
                            <input type="text" name="merek" class="form-control" placeholder="Contoh: Asus, Honda, dll" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis/Nama Barang</label>
                            <input type="text" name="jenis" class="form-control" placeholder="Contoh: Laptop VivoBook, Kursi Guru" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Perolehan (Rp)</label>
                            <input type="number" name="harga_perolehan" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Penempatan Sementara</label>
                        <select name="lokasi_id" class="form-select" required>
                            <option value="">Pilih Lokasi Awal</option>
                            @foreach($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama_ruangan }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">*Lokasi final akan ditentukan oleh Waka saat validasi.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection