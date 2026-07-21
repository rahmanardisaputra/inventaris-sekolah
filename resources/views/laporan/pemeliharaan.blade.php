@extends('layouts.app')

@section('content')
<style>
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
        }
        .no-print {
            display: none !important;
        }
        table {
            font-size: 10pt;
            width: 100%;
        }
        th, td {
            border: 1px solid black !important;
            padding: 4px;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h2>Laporan Riwayat Pemeliharaan Barang</h2>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<!-- Area yang akan dicetak -->
<div id="area-cetak">
    <div class="text-center mb-4">
        <h4>LAPORAN PEMELIHARAAN & PERBAIKAN BARANG</h4>
        <h5>SD MUHAMMADIYAH METRO PUSAT</h5>
        <p>Periode: 
            @if(request('tgl_mulai'))
                {{ \Carbon\Carbon::parse(request('tgl_mulai'))->format('d F Y') }} s/d 
                {{ request('tgl_akhir') ? \Carbon\Carbon::parse(request('tgl_akhir'))->format('d F Y') : 'Sekarang' }}
            @else
                Semua Waktu
            @endif
        </p>
        <hr>
    </div>

    <!-- Form Filter (Tidak ikut tercetak) -->
    <form method="GET" action="{{ route('laporan.pemeliharaan') }}" class="mb-4 no-print">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai:</label>
                <input type="date" name="tgl_mulai" class="form-control" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir:</label>
                <input type="date" name="tgl_akhir" class="form-control" value="{{ request('tgl_akhir') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Tanggal Selesai</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang</th>
                    <th>Kerusakan</th>
                    <th>Tindakan Perbaikan</th>
                    <th>Biaya (Rp)</th>
                    <th>Pelapor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $index => $laporan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $laporan->tanggal_selesai ? \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $laporan->barang->kode_aset }}</td>
                    <td>{{ $laporan->barang->merek }} {{ $laporan->barang->jenis }}</td>
                    <td>{{ Str::limit($laporan->deskripsi_kerusakan, 30) }}</td>
                    <td>{{ $laporan->tindakan_waka ?? '-' }}</td>
                    <td class="text-end">{{ $laporan->biaya_estimasi ? number_format($laporan->biaya_estimasi, 0, ',', '.') : '0' }}</td>
                    <td>{{ $laporan->pelapor->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pemeliharaan pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total Biaya Perbaikan:</th>
                    <th class="text-end">Rp {{ number_format($laporans->sum('biaya_estimasi'), 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-5 row d-print-flex d-none">
        <div class="col-6 text-center">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <br><br><br>
            <p>( ___________________ )</p>
        </div>
        <div class="col-6 text-center">
            <p>Metro, {{ date('d F Y') }}<br>Petugas Sarpras</p>
            <br><br><br>
            <p>( {{ Auth::user()->name }} )</p>
        </div>
    </div>
</div>
@endsection