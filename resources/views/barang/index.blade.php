@extends('layouts.app')

@section('content')
<style>
    .table-inventory {
        font-size: 11px;
        border-collapse: collapse;
    }
    .table-inventory thead {
        background-color: #fff3cd;
        background-image: linear-gradient(to bottom, #fff3cd 0%, #ffeeba 100%);
    }
    .table-inventory th {
        border: 1px solid #dee2e6;
        padding: 8px 4px;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    .table-inventory td {
        border: 1px solid #dee2e6;
        padding: 6px 4px;
        vertical-align: middle;
    }
    .table-inventory tbody tr:hover {
        background-color: #f8f9fa;
    }
    .header-laporan {
        background-color: #fff3cd;
        border: 2px solid #dee2e6;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }
    .header-laporan h3 {
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .info-cetak {
        font-size: 12px;
        margin: 5px 0;
    }
    .checkbox-col {
        width: 3%;
    }
    .no-col {
        width: 3%;
    }
    .kode-col {
        width: 12%;
    }
    .aksi-col {
        width: 8%;
    }
    .text-tiny {
        font-size: 10px;
    }
    .filter-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<!-- Header Laporan -->
<div class="header-laporan">
    <h3>LAPORAN BARANG SD Muhammadiyah Metro Pusat</h3>
    <div class="info-cetak">
        <strong>Ruang:</strong> Lab Komputer &nbsp;|&nbsp; 
        <strong>Dicetak pada:</strong> {{ date('d-m-Y') }}
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form action="{{ route('barang.index') }}" method="GET" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter per Ruangan:</label>
                <select name="lokasi_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-redo"></i> Reset Filter
                    </a>
                    <a href="{{ route('barang.export.excel', ['lokasi_id' => request('lokasi_id')]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    @if(Auth::user()->role === 'staff')
                        <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Input Barang Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
    @if(request('lokasi_id'))
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-filter"></i> 
                Menampilkan data untuk ruangan: 
                <strong>{{ $lokasis->find(request('lokasi_id'))->nama_ruangan ?? 'Semua Ruangan' }}</strong>
            </small>
        </div>
    @endif
</div>

<!-- Form untuk Cetak Massal QR -->
<form action="{{ route('laporan.print.qr') }}" method="GET" target="_blank" id="form-print-qr">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Pilih Barang untuk Dicetak QR Code</h6>
                <div>
                    <button type="button" onclick="checkAll(true)" class="btn btn-sm btn-outline-secondary me-2">Centang Semua</button>
                    <button type="button" onclick="checkAll(false)" class="btn btn-sm btn-outline-secondary me-2">Batal Centang</button>
                    <button type="submit" class="btn btn-info btn-sm" id="btn-print-selected" disabled>
                        <i class="fas fa-print"></i> Cetak QR Terpilih (<span id="count-selected">0</span>)
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-inventory">
                    <thead>
                        <tr>
                            <th class="checkbox-col"><input type="checkbox" id="check-all-header" onclick="checkAll(this.checked)"></th>
                            <th class="no-col">NO</th>
                            <th class="kode-col">KODE LOKASI</th>
                            <th class="kode-col">KODE ASET</th>
                            <th>KATEGORI</th>
                            <th>KELOMPOK</th>
                            <th>JENIS</th>
                            <th>NAMA</th>
                            <th>KONDISI</th>
                            <th>PEROLEHAN</th>
                            <th>HARGA</th>
                            <th>TAHUN</th>
                            <th>KET</th>
                            <th class="aksi-col">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $index => $barang)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="barang_ids[]" value="{{ $barang->id }}" class="form-check-input item-checkbox" onchange="updateCount()">
                            </td>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center text-tiny">{{ $barang->lokasi->kode_ruangan }}</td>
                            <td class="text-center"><strong>{{ $barang->kode_aset }}</strong></td>
                            <td>{{ $barang->masterKodeAset->kategori ?? $barang->kategori ?? '-' }}</td>
                            <td>{{ $barang->masterKodeAset->kelompok ?? '-' }}</td>
                            <td>{{ $barang->masterKodeAset->jenis ?? '-' }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td class="text-center">
                                <span class="badge {{ $barang->kondisi_terkini === 'Baik' ? 'bg-success' : ($barang->kondisi_terkini === 'Rusak Ringan' ? 'bg-warning' : 'bg-danger') }} text-tiny">
                                    {{ $barang->kondisi_terkini }}
                                </span>
                            </td>
                            <td class="text-center">{{ $barang->sumber_perolehan ?? 'Beli' }}</td>
                            <td class="text-end text-tiny">Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                            <td class="text-center text-tiny">{{ $barang->tanggal_perolehan ? \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('Y') : '-' }}</td>
                            <td class="text-tiny">{{ Str::limit($barang->catatan_waka ?? '-', 20) }}</td>
                            <td class="text-center">
                                <a href="{{ route('barang.show', $barang) }}" class="btn btn-sm btn-info text-tiny" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="text-center">Belum ada barang yang disetujui.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
function checkAll(state) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = state);
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('count-selected').innerText = checked;
    document.getElementById('btn-print-selected').disabled = checked === 0;
}
</script>
@endsection