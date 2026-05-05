@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-door-open me-2 text-primary-custom"></i>Manajemen Ruangan</h2>
    @if(in_array(Auth::user()->role, ['staff', 'waka']))
        <a href="{{ route('lokasi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Ruangan Baru
        </a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Penanggung Jawab</th>
                        <th>Jumlah Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lokasis as $index => $lokasi)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $lokasi->nama_ruangan }}</strong></td>
                        <td>{{ $lokasi->penanggung_jawab ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $lokasi->barangs()->where('status_validasi', 'approved')->count() }} Item</span>
                        </td>
                        <td>
                            <a href="{{ route('lokasi.show', $lokasi) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            
                            @if(in_array(Auth::user()->role, ['staff', 'waka']))
                                <a href="{{ route('lokasi.edit', $lokasi) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Hapus hanya jika tidak ada barang -->
                                @if($lokasi->barangs()->count() == 0)
                                    <form action="{{ route('lokasi.destroy', $lokasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus ruangan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Masih ada barang di ruangan ini">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data ruangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection