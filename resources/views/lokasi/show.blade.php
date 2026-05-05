@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-door-open me-2 text-primary-custom"></i>{{ $lokasi->nama_ruangan }}</h2>
        <p class="text-muted mb-0">Penanggung Jawab: <strong>{{ $lokasi->penanggung_jawab ?? '-' }}</strong></p>
    </div>
    <a href="{{ route('lokasi.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">
        <i class="fas fa-boxes me-2"></i>Daftar Barang di Ruangan Ini
    </div>
    <div class="card-body">
        @if($barangs->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-box-open fa-3x mb-3"></i>
                <p>Belum ada barang yang ditempatkan di ruangan ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Merek</th>
                            <th>Harga</th>
                            <th>Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangs as $barang)
                        <tr>
                            <td><a href="{{ route('barang.show', $barang) }}">{{ $barang->kode_aset }}</a></td>
                            <td>{{ $barang->jenis }}</td>
                            <td>{{ $barang->kategori }}</td>
                            <td>{{ $barang->merek }}</td>
                            <td>Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                            <td>
                                @if($barang->kondisi_terkini === 'Baik')
                                    <span class="badge bg-success">Baik</span>
                                @else
                                    <span class="badge bg-danger">{{ $barang->kondisi_terkini }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total Aset:</th>
                            <th colspan="2">{{ $barangs->count() }} Item</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection