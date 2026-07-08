@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Preset Instruksi: {{ $masterKodeAset->kategori }} - {{ $masterKodeAset->jenis }}</h5>
                <a href="{{ route('master-kode-aset.index') }}" class="btn btn-sm btn-light">Kembali</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('instruksi.store', $masterKodeAset) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="instruksi" class="form-control" placeholder="Tambah instruksi baru..." required>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                    @error('instruksi') <small class="text-danger">{{ $message }}</small> @enderror
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Instruksi</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instruksis as $index => $instruksi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <form action="{{ route('instruksi.update', $instruksi) }}" method="POST" id="form-edit-{{ $instruksi->id }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="instruksi" class="form-control form-control-sm" value="{{ $instruksi->instruksi }}" required>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" onclick="document.getElementById('form-edit-{{ $instruksi->id }}').submit()" class="btn btn-sm btn-primary" title="Simpan Perubahan"><i class="fas fa-save"></i></button>
                                        
                                        <form action="{{ route('instruksi.destroy', $instruksi) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus instruksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada preset instruksi untuk kode aset ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
