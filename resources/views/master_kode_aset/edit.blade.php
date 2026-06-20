@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Master Kode Aset</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('master-kode-aset.update', $masterKodeAset) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $masterKodeAset->kategori) }}" required>
                            @error('kategori')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kelompok <span class="text-danger">*</span></label>
                            <input type="text" name="kelompok" class="form-control" value="{{ old('kelompok', $masterKodeAset->kelompok) }}" required>
                            @error('kelompok')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <input type="text" name="jenis" class="form-control" value="{{ old('jenis', $masterKodeAset->jenis) }}" required>
                            @error('jenis')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode Prefix <span class="text-danger">*</span></label>
                            <input type="text" name="kode_prefix" class="form-control" value="{{ old('kode_prefix', $masterKodeAset->kode_prefix) }}" required>
                            @error('kode_prefix')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan <small class="text-muted">(Opsional)</small></label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $masterKodeAset->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('master-kode-aset.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection