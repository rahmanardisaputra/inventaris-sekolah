@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Master Kode Aset</h2>
    <a href="{{ route('master-kode-aset.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kode Aset
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kategori</th>
                        <th>Kelompok</th>
                        <th>Jenis</th>
                        <th>Kode Prefix</th>
                        <th>Keterangan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterKodes as $index => $mk)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $mk->kategori }}</td>
                        <td>{{ $mk->kelompok }}</td>
                        <td>{{ $mk->jenis }}</td>
                        <td class="text-center"><code class="fs-6">{{ $mk->kode_prefix }}</code></td>
                        <td>{{ $mk->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('master-kode-aset.edit', $mk) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('master-kode-aset.destroy', $mk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data master kode aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection