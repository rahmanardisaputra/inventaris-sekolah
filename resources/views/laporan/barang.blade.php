@extends('layouts.app')

@section('content')
<style>
    .table-laporan {
        font-size: 11px;
        border-collapse: collapse;
        width: 100%;
    }
    .table-laporan thead {
        background-color: #fff3cd;
        background-image: linear-gradient(to bottom, #fff3cd 0%, #ffeeba 100%);
    }
    .table-laporan th {
        border: 1px solid #dee2e6;
        padding: 8px 4px;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    .table-laporan td {
        border: 1px solid #dee2e6;
        padding: 6px 4px;
        vertical-align: middle;
    }
    .table-laporan tbody tr:hover {
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
    .filter-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
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
    .text-tiny {
        font-size: 10px;
    }
    
    /* Style Khusus Print */
    @media print {
        body * {
            visibility: hidden;
        }
        #area-cetak, #area-cetak * {
            visibility: visible;
        }
        #area-cetak {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 10px;
        }
        .no-print {
            display: none !important;
        }
        .table-laporan {
            font-size: 8pt;
            width: 100%;
        }
        .table-laporan th, 
        .table-laporan td {
            border: 1px solid black !important;
            padding: 3px;
        }
        .header-laporan {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }
        @page {
            size: landscape;
            margin: 1cm;
        }
    }
</style>

<div id="area-cetak">
<!-- Header Laporan -->
<div class="header-laporan">
    <h3>LAPORAN BARANG SD Muhammadiyah Metro Pusat</h3>
    <div class="info-cetak">
        <strong>Ruang:</strong> {{ $lokasiTerpilih->nama_ruangan ?? 'Semua Ruangan' }} &nbsp;|&nbsp; 
        <strong>Dicetak pada:</strong> {{ date('d-m-Y') }}
        @if(request('tahun'))
            &nbsp;|&nbsp; <strong>Tahun:</strong> {{ request('tahun') }}
        @endif
    </div>
</div>

<!-- Form Filter -->
<div class="filter-section no-print">
    <form method="GET" action="{{ route('laporan.barang') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-3 mb-2">
                <label class="form-label fw-semibold">Filter Ruangan:</label>
                <select name="lokasi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label fw-semibold">Filter Tahun:</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label fw-semibold">Tanggal Mulai:</label>
                <input type="date" name="tgl_mulai" class="form-control" 
                       value="{{ request('tgl_mulai') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label fw-semibold">Tanggal Akhir:</label>
                <input type="date" name="tgl_akhir" class="form-control" 
                       value="{{ request('tgl_akhir') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-1 mb-2">
                <a href="{{ route('laporan.barang') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tombol Aksi -->
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h5 class="mb-0">Daftar Inventaris Barang</h5>
    <div>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <a href="{{ route('barang.export.excel', request()->all()) }}" class="btn btn-success btn-sm ms-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-laporan">
                <thead>
                    <tr>
                        <th class="no-col">NO</th>
                        <th class="kode-col">LOKASI</th>
                        <th class="kode-col">KODE ASET</th>
                        <th>KATEGORI</th>
                        <th>KELOMPOK</th>
                        <th>JENIS</th>
                        <th>NAMA</th>
                        <th>KONDISI</th>
                        <th>PEROLEHAN</th>
                        <th>HARGA</th>
                        <th>TANGGAL</th>
                        <th>KET</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $index => $barang)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center text-tiny">
                            <strong>{{ $barang->lokasi->kode_ruangan ?? '-' }}</strong><br>
                            {{ $barang->lokasi->nama_ruangan ?? '' }}
                        </td>
                        <td class="text-center"><strong>{{ $barang->kode_aset }}</strong></td>
                        <td>{{ $barang->masterKodeAset->kategori ?? $barang->kategori ?? '-' }}</td>
                        <td>{{ $barang->masterKodeAset->kelompok ?? '-' }}</td>
                        <td>{{ $barang->masterKodeAset->jenis ?? $barang->jenis ?? '-' }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td class="text-center">
                            <span class="badge {{ $barang->kondisi_terkini === 'Baik' ? 'bg-success' : ($barang->kondisi_terkini === 'Rusak Ringan' ? 'bg-warning' : 'bg-danger') }} text-tiny">
                                {{ $barang->kondisi_terkini }}
                            </span>
                        </td>
                        <td class="text-center">{{ $barang->sumber_perolehan ?? 'Beli' }}</td>
                        <td class="text-end text-tiny">Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                        <td class="text-center text-tiny">{{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d-m-Y') }}</td>
                        <td class="text-tiny">{{ Str::limit($barang->sumber_dana ?? '-', 20) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="9" class="text-end">Total Harga Aset:</th>
                        <th class="text-end">Rp {{ number_format($barangs->sum('harga_perolehan'), 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Rekapitulasi per Kelompok & Jenis -->
@php
    $rekapKelompok = $barangs->groupBy(function($b) {
        return $b->masterKodeAset->kelompok ?? 'Lainnya';
    })->map->count();

    $rekapJenis = $barangs->groupBy(function($b) {
        return $b->masterKodeAset->jenis ?? 'Lainnya';
    })->map->count();
@endphp

@if($barangs->count() > 0)
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><strong>Rekapitulasi Jumlah per Kelompok</strong></h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kelompok Aset</th>
                            <th class="text-center" width="30%">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapKelompok as $kelompok => $jumlah)
                        <tr>
                            <td>{{ $kelompok }}</td>
                            <td class="text-center">{{ $jumlah }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th>Total Keseluruhan</th>
                            <th class="text-center">{{ $barangs->count() }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><strong>Rekapitulasi Jumlah per Jenis</strong></h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Aset</th>
                            <th class="text-center" width="30%">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapJenis as $jenis => $jumlah)
                        <tr>
                            <td>{{ $jenis }}</td>
                            <td class="text-center">{{ $jumlah }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th>Total Keseluruhan</th>
                            <th class="text-center">{{ $barangs->count() }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tanda Tangan (Hanya untuk print) -->
<div class="mt-5 row d-print-block d-none">
    <div class="col-6 text-center">
        <p>Mengetahui,<br>Kepala Sekolah</p>
        <br><br><br>
        <p>( ___________________ )</p>
    </div>
    <div class="col-6 text-center">
        <p>Metro, {{ date('d F Y') }}<br>Petugas Inventaris</p>
        <br><br><br>
        <p>( {{ Auth::user()->name }} )</p>
    </div>
</div>
</div>
@endsection