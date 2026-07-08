<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\LaporanController; // Kita akan buat ini nanti untuk PDF/Excel
use App\Http\Controllers\MasterKodeAsetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Welcome & Auth Bawaan Laravel
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| ROUTE TERPROTEKSI (HARUS LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // =========================================================
    // 1. FITUR MANAJEMEN BARANG INVENTARIS
    // =========================================================
    // C. Khusus WAKA (Validasi Penempatan Barang)
    Route::middleware(['role:waka'])->prefix('barang')->name('barang.')->group(function () {
        Route::get('/pending-validation', [BarangController::class, 'pendingValidation'])->name('pending.validation');
        Route::post('/{barang}/approve', [BarangController::class, 'approve'])->name('approve');
    });
    // B. Khusus STAFF (Input, Edit, Hapus Barang Baru)
    Route::middleware(['role:staff'])->prefix('barang')->name('barang.')->group(function () {
        Route::get('/create', [BarangController::class, 'create'])->name('create');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::get('/{barang}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{barang}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{barang}', [BarangController::class, 'destroy'])->name('destroy');
    });
    // A. Akses Umum (Semua Role Bisa Lihat Daftar & Detail)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    // Tambahkan di routes/web.php
Route::get('/barang/export-excel', [BarangController::class, 'exportExcel'])
     ->name('barang.export.excel');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('/barang/{barang}/qr', [BarangController::class, 'generateQr'])->name('barang.qr');


    

    Route::middleware(['auth'])->group(function () {
        Route::resource('master-kode-aset', MasterKodeAsetController::class);
        
        // CRUD Instruksi per Kode Aset
        Route::get('/master-kode-aset/{masterKodeAset}/instruksi', [App\Http\Controllers\InstruksiPemeliharaanController::class, 'index'])->name('instruksi.index');
        Route::post('/master-kode-aset/{masterKodeAset}/instruksi', [App\Http\Controllers\InstruksiPemeliharaanController::class, 'store'])->name('instruksi.store');
        Route::put('/instruksi/{instruksiPemeliharaan}', [App\Http\Controllers\InstruksiPemeliharaanController::class, 'update'])->name('instruksi.update');
        Route::delete('/instruksi/{instruksiPemeliharaan}', [App\Http\Controllers\InstruksiPemeliharaanController::class, 'destroy'])->name('instruksi.destroy');
    });

    // =========================================================
    // 2. FITUR PEMELIHARAAN & PERBAIKAN
    // =========================================================

    // A. Akses Umum (Lihat Daftar Laporan)
    // Controller akan memfilter data berdasarkan role user yang login
    Route::get('/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    
    // B. Khusus PENJAGA (Lapor, Revisi, Selesaikan Perbaikan)
    Route::middleware(['role:penjaga'])->prefix('pemeliharaan')->name('pemeliharaan.')->group(function () {
        Route::get('/create', [PemeliharaanController::class, 'create'])->name('create');
        Route::post('/', [PemeliharaanController::class, 'store'])->name('store');
        
        // Edit digunakan untuk 2 hal: 
        // 1. Penjaga merevisi laporan (Draft/Revisi)
        // 2. Penjaga melaporkan selesai (Approved Waka)
        Route::get('/{laporan}/edit', [PemeliharaanController::class, 'edit'])->name('edit');
        Route::put('/{laporan}', [PemeliharaanController::class, 'update'])->name('update');
        
        // Hapus hanya bisa jika status masih Draft
        Route::delete('/{laporan}', [PemeliharaanController::class, 'destroy'])->name('destroy');
    });

    // C. Khusus STAFF (Validasi Awal Laporan Kerusakan)
    Route::middleware(['role:staff'])->prefix('pemeliharaan')->name('pemeliharaan.')->group(function () {
        Route::post('/{laporan}/validate-staff', [PemeliharaanController::class, 'validateByStaff'])->name('validate.staff');
        Route::post('/{laporan}/request-revision', [PemeliharaanController::class, 'requestRevision'])->name('request.revision');
    });

    // D. Khusus WAKA (Persetujuan Tindak Lanjut)
    // Note: Logika approval Waka sebenarnya ada di method 'update' controller Pemeliharaan,
    // tapi kita arahkan via route edit/update yang sudah ada di block Penjaga di atas? 
    // TIDAK. Kita perlu route khusus atau pastikan Controller menangani role Waka di method update/edit.
    // Agar lebih jelas, kita tambahkan route spesifik untuk Waka melakukan "Approve" via form terpisah jika mau,
    // atau gunakan route edit yang sama tapi dibedakan di Controller.
    
    // Di kode Controller sebelumnya, saya menaruh logika Waka di method `update`.
    // Jadi Waka akan mengakses `GET /pemeliharaan/{id}/edit` untuk mengisi form instruksi,
    // dan `PUT /pemeliharaan/{id}` untuk menyimpannya.
    // Karena route edit/update di atas sudah dibungkus middleware `role:penjaga`, maka Waka TIDAK BISA AKSES.
    
    // PERBAIKAN PENTING DI SINI:
    // Kita harus membuka akses Edit & Update untuk Waka juga, ATAU buat route terpisah.
    // Cara termudah: Keluarkan route Edit/Update dari middleware penjaga, lalu cek role di Controller.
    // TAPI, agar aman sesuai permintaan "sesimpel mungkin", mari kita buat route khusus Waka di sini:
    
    Route::middleware(['role:waka'])->prefix('pemeliharaan')->name('pemeliharaan.waka.')->group(function () {
        // Waka butuh lihat form edit untuk isi instruksi
        Route::get('/{laporan}/instruksi', [PemeliharaanController::class, 'edit'])->name('instruksi.edit');
        // Waka submit instruksi
        Route::put('/{laporan}/instruksi', [PemeliharaanController::class, 'update'])->name('instruksi.update');
    });

    // E. Route Show Detail Pemeliharaan (Untuk Semua Role)
    Route::get('/pemeliharaan/{laporan}', [PemeliharaanController::class, 'show'])->name('pemeliharaan.show');


    // =========================================================
    // 3. FITUR LAPORAN (PDF / EXCEL)
    // =========================================================
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/barang', [App\Http\Controllers\LaporanController::class, 'indexBarang'])->name('barang');
        Route::get('/pemeliharaan', [App\Http\Controllers\LaporanController::class, 'indexPemeliharaan'])->name('pemeliharaan');
        Route::get('/print-qr', [App\Http\Controllers\LaporanController::class, 'printQRMassal'])->name('print.qr');
    });

    // =========================================================
    // 4. FITUR MANAJEMEN RUANGAN (LOKASI)
    // =========================================================
    
    // Index & Show: Bisa dilihat semua orang login

    // Create, Edit, Update, Destroy: Hanya Staff & Waka
    Route::middleware(['role:staff,waka'])->prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/create', [App\Http\Controllers\LokasiController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\LokasiController::class, 'store'])->name('store');
        Route::get('/{lokasi}/edit', [App\Http\Controllers\LokasiController::class, 'edit'])->name('edit');
        Route::put('/{lokasi}', [App\Http\Controllers\LokasiController::class, 'update'])->name('update');
        Route::delete('/{lokasi}', [App\Http\Controllers\LokasiController::class, 'destroy'])->name('destroy');
    });


    Route::get('/lokasi', [App\Http\Controllers\LokasiController::class, 'index'])->name('lokasi.index');
    Route::get('/lokasi/{lokasi}', [App\Http\Controllers\LokasiController::class, 'show'])->name('lokasi.show');
});