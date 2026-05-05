@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Daftar Inventaris Barang</h2>
    @if(Auth::user()->role === 'staff')
        <a href="{{ route('barang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Input Barang Baru
        </a>
    @endif
</div>

<!-- Form untuk Cetak Massal -->
<form action="{{ route('laporan.print.qr') }}" method="GET" target="_blank" id="form-print-qr">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Pilih Barang untuk Dicetak QR Code</h5>
                <div>
                    <button type="button" onclick="checkAll(true)" class="btn btn-sm btn-outline-secondary me-2">Centang Semua</button>
                    <button type="button" onclick="checkAll(false)" class="btn btn-sm btn-outline-secondary me-2">Batal Centang</button>
                    <button type="submit" class="btn btn-success" id="btn-print-selected" disabled>
                        <i class="fas fa-print"></i> Cetak QR Terpilih (<span id="count-selected">0</span>)
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%"><input type="checkbox" id="check-all-header" onclick="checkAll(this.checked)"></th>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $barang)
                        @if($barang->status_validasi === 'approved') <!-- Hanya tampilkan yang sudah approved -->
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="barang_ids[]" value="{{ $barang->id }}" class="form-check-input item-checkbox" onchange="updateCount()">
                            </td>
                            <td><strong>{{ $barang->kode_aset }}</strong></td>
                            <td>{{ $barang->merek }} {{ $barang->jenis }}</td>
                            <td>{{ $barang->kategori }}</td>
                            <td>{{ $barang->lokasi->nama_ruangan ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">Disetujui</span>
                            </td>
                            <td>
                                <a href="{{ route('barang.show', $barang) }}" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada barang yang disetujui.</td>
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