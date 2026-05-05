@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Form Lapor Kerusakan Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pemeliharaan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang yang Rusak</label>
                        <select name="barang_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->kode_aset }} - {{ $barang->merek }} {{ $barang->jenis }} ({{ $barang->lokasi->nama_ruangan }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kerusakan</label>
                        <textarea name="deskripsi_kerusakan" class="form-control" rows="4" placeholder="Jelaskan apa yang rusak, misalnya: Layar laptop retak, tidak bisa nyala, dll." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Bukti Kerusakan (Wajib)</label>
                        <input type="file" name="foto_bukti_awal" class="form-control" accept="image/*" required>
                        <small class="text-muted">Format: JPG/PNG, Maksimal 2MB.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('pemeliharaan.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection