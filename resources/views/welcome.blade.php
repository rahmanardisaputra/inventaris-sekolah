@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold">Sistem Inventaris Barang Sekolah</h1>
    <p class="lead">SD Muhammadiyah Metro Pusat</p>
    <hr class="my-4">
    <p>Kelola aset sekolah secara digital dengan QR Code, pelacakan lokasi, dan laporan pemeliharaan terintegrasi.</p>
    <div class="mt-4">
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Login</a>
        @else
            <a href="{{ route('home') }}" class="btn btn-success btn-lg">Masuk ke Dashboard</a>
        @endguest
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Manajemen Aset</h5>
                <p class="card-text">Catat, lacak, dan kelola seluruh barang inventaris sekolah dalam satu sistem.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-qrcode fa-3x text-success mb-3"></i>
                <h5 class="card-title">QR Code Tracking</h5>
                <p class="card-text">Setiap barang dilengkapi QR Code unik untuk identifikasi cepat dan akurat.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-tools fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Pelaporan Kerusakan</h5>
                <p class="card-text">Laporkan kerusakan barang dengan foto bukti, lalu pantau proses perbaikannya.</p>
            </div>
        </div>
    </div>
</div>
@endsection