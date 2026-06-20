<?php

namespace App\Http\Controllers;

use App\Models\BarangInventaris;
use App\Models\LaporanPemeliharaan;
use App\Models\LokasiRuangan;
use Illuminate\Http\Request;


class LaporanController extends Controller
{
    /**
     * Tampilkan Laporan Daftar Barang
     * Bisa difilter berdasarkan Lokasi Ruangan dan Tanggal
     */
    public function indexBarang(Request $request)
    {
        // Ambil semua lokasi untuk dropdown filter
        $lokasis = LokasiRuangan::all();
        
        // Query dasar dengan relasi
        $query = BarangInventaris::with(['lokasi', 'masterKodeAset'])
                    ->where('status_validasi', 'approved') // Hanya tampilkan barang yang sudah valid
                    ->orderBy('lokasi_id');

        // Filter berdasarkan lokasi/ruangan
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_perolehan', $request->tahun);
        }

        // Filter berdasarkan range tanggal
        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tanggal_perolehan', '>=', $request->tgl_mulai);
        }
        
        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_perolehan', '<=', $request->tgl_akhir);
        }

        $barangs = $query->get();
        
        $lokasiTerpilih = null;
        if ($request->filled('lokasi_id')) {
            $lokasiTerpilih = LokasiRuangan::find($request->lokasi_id);
        }

        // Generate tahun untuk dropdown
        $tahunSekarang = date('Y');
        $tahunMulai = 2020;
        $tahuns = range($tahunSekarang, $tahunMulai);

        return view('laporan.barang', compact('barangs', 'lokasis', 'lokasiTerpilih', 'tahuns'));
    }

    /**
     * Tampilkan Laporan Riwayat Pemeliharaan
     */
    public function indexPemeliharaan(Request $request)
    {
        $query = LaporanPemeliharaan::with(['barang', 'pelapor'])
                    ->where('status_laporan', 'selesai')
                    ->orderBy('tanggal_selesai', 'desc');

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tanggal_selesai', '>=', $request->tgl_mulai);
        }
        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $laporans = $query->get();

        return view('laporan.pemeliharaan', compact('laporans'));
    }

    public function printQRMassal(Request $request)
    {
        $ids = $request->input('barang_ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu barang untuk dicetak.');
        }

        $barangs = BarangInventaris::whereIn('id', $ids)
                    ->where('status_validasi', 'approved')
                    ->with('lokasi')
                    ->get();

        if ($barangs->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barang valid yang dipilih.');
        }

        return view('laporan.print_qr', compact('barangs'));
    }
}