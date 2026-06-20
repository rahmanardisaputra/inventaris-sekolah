@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Ruangan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.update', $lokasi) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" value="{{ old('nama_ruangan', $lokasi->nama_ruangan) }}" required>
                        @error('nama_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Ruangan</label>
                        <input type="text" name="kode_ruangan" class="form-control @error('kode_ruangan') is-invalid @enderror" value="{{ old('kode_ruangan', $lokasi->kode_ruangan) }}" required>
                        @error('kode_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Penanggung Jawab (Opsional)</label>
                        <input type="text" name="penanggung_jawab" class="form-control" value="{{ old('penanggung_jawab', $lokasi->penanggung_jawab) }}">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-warning">Update Ruangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection