@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Dashboard Inventaris Sekolah</h2>
        <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Anda masuk sebagai <span class="badge bg-info">{{ ucfirst(Auth::user()->role) }}</span>.</p>

        <div class="row mt-4">
            @if(Auth::user()->role === 'penjaga')
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Lapor Kerusakan</div>
                        <div class="card-body">
                            <p class="card-text">Laporkan kondisi barang yang rusak atau perlu perawatan.</p>
                            <a href="{{ route('pemeliharaan.create') }}" class="btn btn-light">Buat Laporan</a>
                        </div>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'staff')
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Input Barang Baru</div>
                        <div class="card-body">
                            <p class="card-text">Tambahkan barang inventaris baru ke sistem.</p>
                            <a href="{{ route('barang.create') }}" class="btn btn-light">Input Barang</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-header">Validasi Laporan</div>
                        <div class="card-body">
                            <p class="card-text">Cek dan validasi laporan kerusakan dari penjaga.</p>
                            <a href="{{ route('pemeliharaan.index', ['status' => 'draft']) }}" class="btn btn-light">Lihat Laporan</a>
                        </div>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'waka')
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Validasi Penempatan</div>
                        <div class="card-body">
                            <p class="card-text">Setujui penempatan barang baru oleh staff.</p>
                            <a href="{{ route('barang.pending.validation') }}" class="btn btn-light">Barang Pending</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Persetujuan Perbaikan</div>
                        <div class="card-body">
                            <p class="card-text">Berikan tindak lanjut atas laporan yang sudah divalidasi staff.</p>
                            <a href="{{ route('pemeliharaan.index', ['status' => 'validated_staff']) }}" class="btn btn-light">Lihat Laporan</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-5">
            <h4>Akses Cepat</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="{{ route('barang.index') }}">Daftar Semua Barang</a></li>
                <li class="list-group-item"><a href="">Daftar Ruangan / Lokasi</a></li>
                <li class="list-group-item"><a href="">Download Laporan Pemeliharaan (PDF)</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection