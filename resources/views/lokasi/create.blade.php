@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary-custom text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Ruangan Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" placeholder="Contoh: Lab Komputer 1" value="{{ old('nama_ruangan') }}" required>
                        @error('nama_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Penanggung Jawab (Opsional)</label>
                        <input type="text" name="penanggung_jawab" class="form-control" placeholder="Contoh: Budi Santoso, S.Kom" value="{{ old('penanggung_jawab') }}">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Ruangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection