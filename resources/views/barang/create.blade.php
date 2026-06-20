@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Input Barang Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    
                    {{-- PILIH PREFIX --}}
                    <div class="mb-3">
                        <label class="form-label">Master Kode Aset (Prefix)</label>
                        <select name="master_kode_aset_id" id="master_kode_aset_id" class="form-select" required>
                            <option value="">Pilih Master Kode Aset</option>
                            @foreach($masterKodes as $mk)
                                {{-- Tambahkan data-prefix untuk dibaca JavaScript --}}
                                <option value="{{ $mk->id }}" data-prefix="{{ $mk->kode_prefix }}">
                                    {{ $mk->kode_prefix }} - {{ $mk->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- INPUT NOMOR URUT MANUAL --}}
                    <div class="mb-3">
                        <label class="form-label">Nomor Urut (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text" id="kode-preview">Pilih prefix dulu...</span>
                            <input type="number" name="nomor_urut" id="nomor_urut" class="form-control" placeholder="Contoh: 1, 2, 10" min="1" required>
                        </div>
                        <small class="text-muted">*Kode akhir akan digabung otomatis. Contoh: <code>03.01.01.07.001</code></small>
                        @error('nomor_urut')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Penempatan Sementara</label>
                            <select name="lokasi_id" class="form-select" required>
                                <option value="">Pilih Lokasi Awal</option>
                                @foreach($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kursi Guru, Laptop Asus" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merek (Opsional)</label>
                            <input type="text" name="merek" class="form-control" placeholder="Contoh: Asus, Olympic, dll">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Perolehan (Rp)</label>
                            <input type="number" name="harga_perolehan" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kondisi Awal</label>
                            <select name="kondisi_terkini" class="form-select" required>
                                <option value="Baik" selected>Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan_waka" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script sederhana untuk preview kode secara langsung --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const prefixSelect = document.getElementById('master_kode_aset_id');
        const nomorInput = document.getElementById('nomor_urut');
        const preview = document.getElementById('kode-preview');

        function updatePreview() {
            const selectedOption = prefixSelect.options[prefixSelect.selectedIndex];
            const prefix = selectedOption ? selectedOption.getAttribute('data-prefix') : null;
            // PadStart 3 digit agar konsisten (1 jadi 001)
            const nomor = nomorInput.value ? nomorInput.value.padStart(3, '0') : '000';
            
            preview.textContent = prefix ? (prefix + '.' + nomor) : 'Pilih prefix dulu...';
        }

        prefixSelect.addEventListener('change', updatePreview);
        nomorInput.addEventListener('input', updatePreview);
    });
</script>
@endsection