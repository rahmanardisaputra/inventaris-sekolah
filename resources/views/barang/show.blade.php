@extends('layouts.app')

@section('content')
<style>
    /* Style khusus print untuk label QR */
    @media print {
        body * {
            visibility: hidden;
        }
        #label-qr, #label-qr * {
            visibility: visible;
        }
        #label-qr {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            text-align: center;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Detail Barang: {{ $barang->kode_aset }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Nama Barang</th>
                        <td>: {{ $barang->merek }} {{ $barang->nama_barang ?? $barang->masterKodeAset->jenis ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>: {{ $barang->masterKodeAset->kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Saat Ini</th>
                        <td>: <strong>{{ $barang->lokasi->nama_ruangan ?? 'Belum Ditentukan' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Harga Perolehan</th>
                        <td>: Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Perolehan</th>
                        <td>: {{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Kondisi Terkini</th>
                        <td>: 
                            @if($barang->kondisi_terkini === 'Baik')
                                <span class="badge bg-success">{{ $barang->kondisi_terkini }}</span>
                            @else
                                <span class="badge bg-danger">{{ $barang->kondisi_terkini }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status Validasi</th>
                        <td>: 
                            @if($barang->status_validasi === 'approved')
                                <span class="badge bg-success">Disetujui Waka</span>
                            @else
                                <span class="badge bg-warning text-dark">Menunggu Validasi</span>
                            @endif
                        </td>
                    </tr>
                    @if($barang->catatan_waka)
                    <tr>
                        <th>Catatan Waka</th>
                        <td>: <em>{{ $barang->catatan_waka }}</em></td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
    <div class="card shadow-sm text-center">
        <div class="card-header bg-light fw-bold">
            <i class="fas fa-qrcode me-2"></i>Label QR Code Barang
        </div>
        <div class="card-body">
            @if($barang->status_validasi === 'approved')
                
                <!-- Area Label QR yang bisa di-print -->
                <div id="label-qr" class="border p-3 mb-3 bg-white text-start" style="width: 300px; margin: 0 auto;">
                    <!-- QR Code SVG Inline -->
                    <div class="text-center mb-2">
                        @php
    $isiQr = "No Inv: {$barang->kode_aset}\n" .
             "Nama: {$barang->merek} " . ($barang->nama_barang ?? $barang->masterKodeAset->jenis ?? '') . "\n" .
             "Lokasi: " . ($barang->lokasi->nama_ruangan ?? '-') . "\n" .
             "PJ: " . ($barang->lokasi->penanggung_jawab ?? '-') . "\n" .
             "Harga: Rp " . number_format($barang->harga_perolehan, 0, ',', '.') . "\n" .
             "Tgl. Perolehan: " . \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y');
@endphp

{!! QrCode::size(200)->generate($isiQr) !!}
                    </div>
                    
                    <!-- Informasi Barang di Bawah QR -->
                    <div class="small text-center border-top pt-2">
                        <p class="mb-1"><strong>No Inv:</strong> {{ $barang->kode_aset }}</p>
                        <p class="mb-1"><strong>Nama:</strong> {{ $barang->merek }} {{ $barang->nama_barang ?? $barang->masterKodeAset->jenis ?? '' }}</p>
                        <p class="mb-1"><strong>Lokasi:</strong> {{ $barang->lokasi->nama_ruangan ?? '-' }}</p>
                        <p class="mb-1"><strong>PJ:</strong> {{ $barang->lokasi->penanggung_jawab ?? '-' }}</p>
                        <p class="mb-1"><strong>Harga:</strong> Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</p>
                        <p class="mb-0"><strong>Tgl. Perolehan:</strong> {{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y') }}</p>
                    </div>
                </div>

                <br>
                <button onclick="printLabel()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-print"></i> Cetak Label QR
                </button>
                
                <p class="mt-2 small text-muted">Klik tombol di atas untuk mencetak label QR lengkap dengan informasi.</p>

            @else
                <div class="alert alert-warning py-2">
                    <small><i class="fas fa-info-circle"></i> Label QR hanya tersedia setelah barang divalidasi oleh Waka.</small>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script untuk Print Label Saja -->
<script>
function printLabel() {
    var printContents = document.getElementById('label-qr').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = "<div style='text-align:center; padding: 20px;'>" + printContents + "</div>";

    window.print();

    document.body.innerHTML = originalContents;
    location.reload(); // Reload agar event listener tetap aktif
}
</script>
</div>
@endsection