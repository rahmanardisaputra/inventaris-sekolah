@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header 
                @if(Auth::user()->role === 'waka') bg-primary text-white 
                @elseif(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'approved_waka') bg-success text-white
                @else bg-warning text-dark @endif">
                
                @if(Auth::user()->role === 'waka')
                    <h5 class="mb-0">Instruksi Tindak Lanjut (Waka)</h5>
                @elseif(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'approved_waka')
                    <h5 class="mb-0">Lapor Penyelesaian Perbaikan</h5>
                @else
                    <h5 class="mb-0">Revisi Laporan Kerusakan</h5>
                @endif
            </div>
            
            <div class="card-body">
                @if(Auth::user()->role === 'waka')
                <form action="{{ route('pemeliharaan.waka.instruksi.update', $laporan) }}" method="POST" enctype="multipart/form-data">
                @else
                <form action="{{ route('pemeliharaan.update', $laporan) }}" method="POST" enctype="multipart/form-data">
                @endif  
                    @csrf
                    @method('PUT')

                    {{-- BAGIAN 1: JIKA PENJAGA REVISI DRAFT/REVISI --}}
                    @if(Auth::user()->role === 'penjaga' && in_array($laporan->status_laporan, ['draft', 'revisi']))
                        <div class="alert alert-warning">
                            <strong>Catatan:</strong> Mohon perbaiki deskripsi atau unggah ulang foto bukti yang lebih jelas.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Kerusakan</label>
                            <textarea name="deskripsi_kerusakan" class="form-control" rows="4" required>{{ old('deskripsi_kerusakan', $laporan->deskripsi_kerusakan) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Ulang Foto Bukti (Opsional jika tidak berubah)</label>
                            <input type="file" name="foto_bukti_awal" class="form-control" accept="image/*">
                            @if($laporan->foto_bukti_awal)
                                <div class="mt-2">
                                    <small>Foto Saat Ini:</small><br>
                                    <img src="{{ asset('storage/' . $laporan->foto_bukti_awal) }}" style="max-height: 100px;" class="img-thumbnail">
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- BAGIAN 2: JIKA WAKA BERIKAN INSTRUKSI --}}
                    @if(Auth::user()->role === 'waka' && $laporan->status_laporan === 'validated_staff')
                        <div class="alert alert-info">
                            <strong>Info:</strong> Laporan ini telah divalidasi oleh Staff. Berikan instruksi perbaikan.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Instruksi Tindak Lanjut</label>
                            <textarea name="tindakan_waka" class="form-control" rows="3" placeholder="Contoh: Segera ganti harddisk baru. Koordinasi dengan vendor IT." required>{{ old('tindakan_waka', $laporan->tindakan_waka) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estimasi Biaya (Rp)</label>
                            <input type="number" name="biaya_estimasi" class="form-control" placeholder="0" value="{{ old('biaya_estimasi', $laporan->biaya_estimasi) }}">
                        </div>
                    @endif

                    {{-- BAGIAN 3: JIKA PENJAGA LAPOR SELESAI --}}
                    @if(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'approved_waka')
                        <div class="alert alert-success">
                            <strong>Instruksi Waka:</strong><br>
                            <em>{{ $laporan->tindakan_waka }}</em>
                            <br><strong>Estimasi Biaya:</strong> Rp {{ number_format($laporan->biaya_estimasi ?? 0, 0, ',', '.') }}
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Bukti Setelah Perbaikan (Wajib)</label>
                            <input type="file" name="foto_bukti_akhir" class="form-control" accept="image/*" required>
                            <small class="text-muted">Ambil foto barang setelah diperbaiki sebagai bukti penyelesaian.</small>
                        </div>
                    @endif

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('pemeliharaan.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        
                        @if(Auth::user()->role === 'penjaga' && in_array($laporan->status_laporan, ['draft', 'revisi']))
                            <button type="submit" class="btn btn-warning">Simpan Revisi</button>
                        @elseif(Auth::user()->role === 'waka')
                            <button type="submit" class="btn btn-primary">Setujui & Instruksikan</button>
                        @elseif(Auth::user()->role === 'penjaga' && $laporan->status_laporan === 'approved_waka')
                            <button type="submit" class="btn btn-success">Laporkan Selesai</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection