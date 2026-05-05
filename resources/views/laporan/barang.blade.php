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
    <h2>Laporan Daftar Inventaris Barang</h2>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<!-- Area yang akan dicetak -->
<div id="area-cetak">
    <div class="text-center mb-4">
        <h4>LAPORAN DAFTAR BARANG INVENTARIS</h4>
        <h5>SD MUHAMMADIYAH METRO PUSAT</h5>
        @if($lokasiTerpilih)
            <p>Ruangan: <strong>{{ $lokasiTerpilih->nama_ruangan }}</strong></p>
        @else
            <p>Semua Ruangan</p>
        @endif
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
        <hr>
    </div>

    <!-- Form Filter (Tidak ikut tercetak karena class no-print) -->
    <form method="GET" action="{{ route('laporan.barang') }}" class="mb-4 no-print">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter Berdasarkan Ruangan:</label>
                <select name="lokasi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merek</th>
                    <th>Harga Perolehan</th>
                    <th>Tgl Perolehan</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $index => $barang)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $barang->kode_aset }}</td>
                    <td>{{ $barang->jenis }}</td>
                    <td>{{ $barang->kategori }}</td>
                    <td>{{ $barang->merek }}</td>
                    <td class="text-end">Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $barang->kondisi_terkini }}</td>
                    <td>{{ $barang->lokasi->nama_ruangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data barang.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Total Harga Aset:</th>
                    <th class="text-end">Rp {{ number_format($barangs->sum('harga_perolehan'), 0, ',', '.') }}</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-5 row no-print">
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