@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Edit Data Barang: {{ $barang->kode_aset }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- PILIH PREFIX --}}
                    <div class="mb-3">
                        <label class="form-label">Kategori / Master Kode Aset</label>
                        <select name="master_kode_aset_id" class="form-select" required>
                            <option value="">Pilih Kategori / Master Kode</option>
                            @foreach($masterKodes as $mk)
                                <option value="{{ $mk->id }}" data-prefix="{{ $mk->kode_prefix }}" data-next="{{ $mk->next_sequence }}" {{ $barang->master_kode_aset_id == $mk->id ? 'selected' : '' }}>
                                    {{ $mk->kode_prefix }} - {{ $mk->keterangan }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_kode_aset_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- INFO KODE ASET --}}
                    <div class="mb-3">
                        <label class="form-label">Kode Aset Saat Ini / Preview</label>
                        <input type="text" class="form-control" id="kode-preview-input" value="{{ $barang->kode_aset }}" disabled>
                        <small class="text-muted" id="kode-preview-info">*Jika Anda mengubah <strong>Kategori / Master Kode Aset</strong> di atas, sistem akan <strong>otomatis</strong> membuatkan nomor aset baru sesuai urutan kategori tersebut saat data disimpan.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="{{ \Carbon\Carbon::parse($barang->tanggal_perolehan)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Penempatan Sementara</label>
                            <select name="lokasi_id" class="form-select" required>
                                <option value="">Pilih Lokasi Awal</option>
                                @foreach($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ $barang->lokasi_id == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}" placeholder="Contoh: Kursi Guru, Laptop Asus" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merek (Opsional)</label>
                            <input type="text" name="merek" class="form-control" value="{{ old('merek', $barang->merek) }}" placeholder="Contoh: Asus, Olympic, dll">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Perolehan (Rp)</label>
                            <input type="number" name="harga_perolehan" class="form-control" value="{{ old('harga_perolehan', $barang->harga_perolehan) }}" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kondisi Saat Ini</label>
                            <select name="kondisi_terkini" class="form-select" required>
                                <option value="Baik" {{ $barang->kondisi_terkini == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ $barang->kondisi_terkini == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat" {{ $barang->kondisi_terkini == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan_waka" class="form-control" rows="2">{{ old('catatan_waka', $barang->catatan_waka) }}</textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-warning">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const prefixSelect = document.querySelector('select[name="master_kode_aset_id"]');
        const previewInput = document.getElementById('kode-preview-input');
        const previewInfo = document.getElementById('kode-preview-info');
        const originalKode = "{{ $barang->kode_aset }}";
        const originalMasterId = "{{ $barang->master_kode_aset_id }}";

        function updatePreview() {
            const selectedOption = prefixSelect.options[prefixSelect.selectedIndex];
            const selectedValue = prefixSelect.value;
            const prefix = selectedOption ? selectedOption.getAttribute('data-prefix') : null;
            const nextSeq = selectedOption ? selectedOption.getAttribute('data-next') : null;
            
            if (selectedValue == originalMasterId) {
                previewInput.value = originalKode;
                previewInfo.innerHTML = "*Jika Anda mengubah <strong>Kategori / Master Kode Aset</strong> di atas, sistem akan <strong>otomatis</strong> membuatkan nomor aset baru sesuai urutan kategori tersebut saat data disimpan.";
                previewInput.style.backgroundColor = "";
            } else if (prefix && nextSeq) {
                previewInput.value = `${prefix}.${nextSeq}`;
                previewInfo.innerHTML = "<span class='text-danger fw-bold'><i class='fas fa-exclamation-triangle'></i> Perhatian: Kode aset akan otomatis diubah menjadi ini!</span>";
                previewInput.style.backgroundColor = "#fff3cd"; // warna peringatan kuning
            } else {
                previewInput.value = 'Pilih Kategori...';
            }
        }

        prefixSelect.addEventListener('change', updatePreview);
    });
</script>
@endsection