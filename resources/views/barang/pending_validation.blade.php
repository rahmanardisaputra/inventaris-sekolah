@extends('layouts.app')

@section('content')
<h2 class="mb-4">Validasi Penempatan Barang Baru</h2>

@if($barangs->isEmpty())
    <div class="alert alert-info">
        Tidak ada barang yang menunggu validasi.
    </div>
@else
    <div class="row">
        @foreach($barangs as $barang)
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">
                    <strong>Pending:</strong> {{ $barang->kode_aset }}
                </div>
                <div class="card-body">
                    <p><strong>Barang:</strong> {{ $barang->merek }} {{ $barang->jenis }}</p>
                    <p><strong>Kategori:</strong> {{ $barang->kategori }}</p>
                    <p><strong>Harga:</strong> Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</p>
                    <p><strong>Lokasi Awal (Staff):</strong> {{ $barang->lokasi->nama_ruangan }}</p>
                    
                    <hr>
                    
                    <form action="{{ route('barang.approve', $barang) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tentukan Lokasi Final Penempatan:</label>
                            <select name="lokasi_id_final" class="form-select" required>
                                @foreach($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ $lokasi->id == $barang->lokasi_id ? 'selected' : '' }}>
                                        {{ $lokasi->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan untuk Staff (Opsional):</label>
                            <textarea name="catatan_waka" class="form-control" rows="2" placeholder="Misal: Pastikan kabel tertata rapi"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Validasi & Setujui Penempatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection