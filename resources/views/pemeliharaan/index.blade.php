@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Laporan Pemeliharaan Barang</h2>
    @if(Auth::user()->role === 'penjaga')
        <a href="{{ route('pemeliharaan.create') }}" class="btn btn-danger">
            <i class="fas fa-exclamation-triangle"></i> Lapor Kerusakan Baru
        </a>
    @endif
</div>

<!-- Filter Tab Sederhana -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('pemeliharaan.index') }}">Semua</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'draft' ? 'active' : '' }}" href="{{ route('pemeliharaan.index', ['status' => 'draft']) }}">Draft/Perlu Validasi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'validated_staff' ? 'active' : '' }}" href="{{ route('pemeliharaan.index', ['status' => 'validated_staff']) }}">Menunggu Waka</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'approved_waka' ? 'active' : '' }}" href="{{ route('pemeliharaan.index', ['status' => 'approved_waka']) }}">Perlu Ditindaklanjuti</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'selesai' ? 'active' : '' }}" href="{{ route('pemeliharaan.index', ['status' => 'selesai']) }}">Selesai</a>
    </li>
</ul>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Aset</th>
                        <th>Nama Barang</th>
                        <th>Pelapor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $laporan)
                    <tr>
                        <td>{{ $laporan->created_at->format('d/m/Y') }}</td>
                        <td><strong>{{ $laporan->barang->kode_aset }}</strong></td>
                        <td>{{ $laporan->barang->merek }} {{ $laporan->barang->jenis }}</td>
                        <td>{{ $laporan->pelapor->name }}</td>
                        <td>
                            @php
                                $badgeClass = match($laporan->status_laporan) {
                                    'draft' => 'bg-secondary',
                                    'revisi' => 'bg-warning text-dark',
                                    'validated_staff' => 'bg-info text-dark',
                                    'approved_waka' => 'bg-primary',
                                    'selesai' => 'bg-success',
                                    default => 'bg-light'
                                };
                                $statusText = match($laporan->status_laporan) {
                                    'draft' => 'Draft',
                                    'revisi' => 'Perlu Revisi',
                                    'validated_staff' => 'Menunggu Waka',
                                    'approved_waka' => 'Disetujui Waka',
                                    'selesai' => 'Selesai',
                                    default => $laporan->status_laporan
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            <!-- Detail Button -->
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $laporan->id }}">
                                Lihat
                            </button>

                            <!-- AKSI BERDASARKAN ROLE & STATUS -->
                            
                            {{-- 1. PENJAGA: Edit Draft/Revisi --}}
                            @if(Auth::user()->role === 'penjaga' && in_array($laporan->status_laporan, ['draft', 'revisi']))
                                <a href="{{ route('pemeliharaan.edit', $laporan) }}" class="btn btn-sm btn-warning">Revisi/Edit</a>
                            @endif

                            {{-- 2. PENJAGA: Selesaikan Perbaikan (Jika Approved Waka) --}}
                            @if(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'approved_waka')
                                <a href="{{ route('pemeliharaan.edit', $laporan) }}" class="btn btn-sm btn-success">Lapor Selesai</a>
                            @endif

                            {{-- 3. STAFF: Validasi atau Minta Revisi (Jika Draft) --}}
                            @if(Auth::user()->role === 'staff' && $laporan->status_laporan === 'draft')
                                <form action="{{ route('pemeliharaan.validate.staff', $laporan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Validasi</button>
                                </form>
                                <form action="{{ route('pemeliharaan.request.revision', $laporan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Kirim kembali ke penjaga untuk revisi?')">Minta Revisi</button>
                                </form>
                            @endif

                            {{-- 4. WAKA: Setujui Tindak Lanjut (Jika Validated Staff) --}}
                            @if(Auth::user()->role === 'waka' && $laporan->status_laporan === 'validated_staff')
                                <a href="{{ route('pemeliharaan.waka.instruksi.edit', $laporan) }}" class="btn btn-sm btn-primary">Berikan Instruksi</a>
                            @endif

                            {{-- 5. HAPUS (Hanya Draft & Milik Sendiri) --}}
                            @if(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'draft' && $laporan->pelapor_id === Auth::id())
                                <form action="{{ route('pemeliharaan.destroy', $laporan) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan laporan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>

                   

                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data laporan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $laporans->links() }}
    </div>
</div>


<!-- PINDAHKAN SEMUA MODAL KE SINI (DI LUAR CARD & TABLE) -->
@foreach($laporans as $laporan)
<div class="modal fade" id="modalDetail{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Laporan #{{ $laporan->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Barang:</strong> {{ $laporan->barang->merek }} {{ $laporan->barang->jenis }}</p>
                        <p><strong>Lokasi:</strong> {{ $laporan->barang->lokasi->nama_ruangan }}</p>
                        <p><strong>Deskripsi Kerusakan:</strong><br>{{ $laporan->deskripsi_kerusakan }}</p>
                        <p><strong>Foto Awal:</strong><br>
                            @if($laporan->foto_bukti_awal)
                                <img src="{{ asset('storage/' . $laporan->foto_bukti_awal) }}" class="img-thumbnail" style="max-height: 150px;">
                            @else
                                <em>Tidak ada foto</em>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        @php
                            $statusText = match($laporan->status_laporan) {
                                'draft' => 'Draft',
                                'revisi' => 'Perlu Revisi',
                                'validated_staff' => 'Menunggu Waka',
                                'approved_waka' => 'Disetujui Waka',
                                'selesai' => 'Selesai',
                                default => $laporan->status_laporan
                            };
                        @endphp
                        <p><strong>Status:</strong> {{ $statusText }}</p>
                        
                        @if($laporan->tindakan_waka)
                            <div class="alert alert-primary">
                                <strong>Instruksi Waka:</strong><br>
                                {{ $laporan->tindakan_waka }}
                                <br><small>Estimasi Biaya: Rp {{ number_format($laporan->biaya_estimasi ?? 0, 0, ',', '.') }}</small>
                            </div>
                        @endif

                        @if($laporan->foto_bukti_akhir)
                            <p><strong>Foto Setelah Perbaikan:</strong><br>
                                <img src="{{ asset('storage/' . $laporan->foto_bukti_akhir) }}" class="img-thumbnail" style="max-height: 150px;">
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection


