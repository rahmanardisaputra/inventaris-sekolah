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
     * Bisa difilter berdasarkan Lokasi Ruangan
     */
    public function indexBarang(Request $request)
    {
        // Ambil semua lokasi untuk dropdown filter
        $lokasis = LokasiRuangan::all();
        
        // Query dasar
        $query = BarangInventaris::with('lokasi')
                    ->where('status_validasi', 'approved') // Hanya tampilkan barang yang sudah valid
                    ->orderBy('lokasi_id')
                    ->orderBy('kategori');

        // Jika ada filter lokasi
        if ($request->has('lokasi_id') && $request->lokasi_id != '') {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        $barangs = $query->get();
        $lokasiTerpilih = null;
        
        if ($request->has('lokasi_id') && $request->lokasi_id != '') {
            $lokasiTerpilih = LokasiRuangan::find($request->lokasi_id);
        }

        return view('laporan.barang', compact('barangs', 'lokasis', 'lokasiTerpilih'));
    }

    /**
     * Tampilkan Laporan Riwayat Pemeliharaan
     * Bisa difilter berdasarkan Tanggal atau Status
     */
    public function indexPemeliharaan(Request $request)
    {
        $query = LaporanPemeliharaan::with(['barang', 'pelapor'])
                    ->where('status_laporan', 'selesai') // Hanya tampilkan yang sudah selesai diperbaiki
                    ->orderBy('tanggal_selesai', 'desc');

        // Filter Tanggal Mulai & Akhir
        if ($request->has('tgl_mulai') && $request->tgl_mulai != '') {
            $query->whereDate('tanggal_selesai', '>=', $request->tgl_mulai);
        }
        if ($request->has('tgl_akhir') && $request->tgl_akhir != '') {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $laporans = $query->get();

        return view('laporan.pemeliharaan', compact('laporans'));
    }

    public function printQRMassal(Request $request)
    {
        // Ambil ID barang yang dipilih dari checkbox
        $ids = $request->input('barang_ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu barang untuk dicetak.');
        }

        // Ambil data barang berdasarkan ID
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