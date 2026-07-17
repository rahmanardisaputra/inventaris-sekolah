<?php

namespace App\Http\Controllers;

use App\Models\BarangInventaris;
use App\Models\LokasiRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBarangInventarisRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Pastikan package ini sudah diinstall


class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     * Bisa diakses oleh semua role yang login untuk melihat daftar barang.
     */
    // Tambahkan method ini di BarangController

/**
 * Filter berdasarkan ruangan
 */
public function index(Request $request)
{
    $query = BarangInventaris::with(['lokasi', 'masterKodeAset']);
    
    // Filter berdasarkan lokasi/ruangan
    if ($request->filled('lokasi_id')) {
        $query->where('lokasi_id', $request->lokasi_id);
    }
    
    // Hanya tampilkan yang approved
    $query->where('status_validasi', 'approved');
    
    $barangs = $query->orderBy('created_at', 'desc')->get();
    $lokasis = LokasiRuangan::all();
    
    return view('barang.index', compact('barangs', 'lokasis'));
}

/**
 * Export Excel - Simple dengan view
 */
/**
 * Export Excel - Simple dengan view
 */
public function exportExcel(Request $request)
{
    $query = BarangInventaris::with(['lokasi', 'masterKodeAset']);
    
    // Filter berdasarkan lokasi jika ada
    if ($request->filled('lokasi_id')) {
        $query->where('lokasi_id', $request->lokasi_id);
        $selectedLokasi = LokasiRuangan::find($request->lokasi_id);
    } else {
        $selectedLokasi = null;
    }
    
    $barangs = $query->where('status_validasi', 'approved')
                     ->orderBy('lokasi_id')
                     ->orderBy('kode_aset')
                     ->get();
    
    $filename = 'Laporan_Inventaris_' . date('Y-m-d') . '.xls';
    
    return response()->view('barang.export_excel', compact('barangs', 'selectedLokasi'), 200, [
        'Content-Type' => 'application/vnd.ms-excel',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}

    /**
     * Show the form for creating a new resource.
     * HANYA STAFF yang boleh input barang baru.
     */
    public function create()
    {
        // Ambil daftar lokasi untuk dropdown
        $lokasis = LokasiRuangan::all();
        $masterKodes = \App\Models\MasterKodeAset::all();
        
        // Hitung next sequence untuk masing-masing master kode
        foreach ($masterKodes as $mk) {
            $lastBarang = BarangInventaris::where('master_kode_aset_id', $mk->id)
                                          ->orderByRaw('CAST(SUBSTRING_INDEX(kode_aset, ".", -1) AS UNSIGNED) DESC')
                                          ->first();
            $nextUrut = 1;
            if ($lastBarang) {
                $lastKodeParts = explode('.', $lastBarang->kode_aset);
                $lastNumber = intval(end($lastKodeParts));
                $nextUrut = $lastNumber + 1;
            }
            $mk->next_sequence = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
        }

        return view('barang.create', compact('lokasis', 'masterKodes'));
    }

    /**
     * Store a newly created resource in storage.
     * HANYA STAFF yang boleh simpan data.
     */
    public function store(StoreBarangInventarisRequest $request)
    {
        $validated = $request->validated();
        $validated['status_validasi'] = 'pending';

        // 1. Ambil prefix dari master kode aset yang dipilih
        $masterKode = \App\Models\MasterKodeAset::findOrFail($validated['master_kode_aset_id']);

        // 2. Cari barang terakhir dengan master_kode_aset_id ini
        $lastBarang = BarangInventaris::where('master_kode_aset_id', $masterKode->id)
                                      ->orderByRaw('CAST(SUBSTRING_INDEX(kode_aset, ".", -1) AS UNSIGNED) DESC')
                                      ->first();
        
        $nextUrut = 1;
        if ($lastBarang) {
            $lastKodeParts = explode('.', $lastBarang->kode_aset);
            $lastNumber = intval(end($lastKodeParts));
            $nextUrut = $lastNumber + 1;
        }

        // 3. Format nomor urut jadi 3 digit (misal: 1 -> 001, 25 -> 025)
        $formattedSequence = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
        
        // 4. Gabungkan prefix dan nomor urut
        $kodeAset = $masterKode->kode_prefix . '.' . $formattedSequence;

        $validated['kode_aset'] = $kodeAset;

        // 5. Simpan data (tidak perlu DB transaction/lock lagi karena input manual)
        BarangInventaris::create($validated);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diinput! Menunggu validasi Waka.');
    }

    /**
     * Display the specified resource.
     * Untuk melihat detail barang & QR Code.
     */
    public function show(BarangInventaris $barang)
    {
        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     * (Opsional: Jika staff perlu edit data sebelum divalidasi)
     */
    public function edit(BarangInventaris $barang)
    {
        $lokasis = LokasiRuangan::all();
        $masterKodes = \App\Models\MasterKodeAset::all();
        
        // Hitung next sequence untuk masing-masing master kode
        foreach ($masterKodes as $mk) {
            $lastBarang = \App\Models\BarangInventaris::where('master_kode_aset_id', $mk->id)
                                          ->orderByRaw('CAST(SUBSTRING_INDEX(kode_aset, ".", -1) AS UNSIGNED) DESC')
                                          ->first();
            $nextUrut = 1;
            if ($lastBarang) {
                $lastKodeParts = explode('.', $lastBarang->kode_aset);
                $lastNumber = intval(end($lastKodeParts));
                $nextUrut = $lastNumber + 1;
            }
            $mk->next_sequence = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
        }

        return view('barang.edit', compact('barang', 'lokasis', 'masterKodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangInventaris $barang)
    {
        $validated = $request->validate([
            'master_kode_aset_id' => 'required|exists:master_kode_aset,id',
            'lokasi_id' => 'required|exists:lokasi_ruangan,id',
            'nama_barang' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'harga_perolehan' => 'required|numeric|min:0',
            'tanggal_perolehan' => 'required|date',
            'kondisi_terkini' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'catatan_waka' => 'nullable|string',
        ]);

        // Jika Kategori/Master Kode Aset diubah, maka generate ulang Kode Aset yang baru
        if ($barang->master_kode_aset_id != $validated['master_kode_aset_id']) {
            $masterKode = \App\Models\MasterKodeAset::findOrFail($validated['master_kode_aset_id']);
            $lastBarang = \App\Models\BarangInventaris::where('master_kode_aset_id', $masterKode->id)
                                          ->orderByRaw('CAST(SUBSTRING_INDEX(kode_aset, ".", -1) AS UNSIGNED) DESC')
                                          ->first();
            $nextUrut = 1;
            if ($lastBarang) {
                $lastKodeParts = explode('.', $lastBarang->kode_aset);
                $lastNumber = intval(end($lastKodeParts));
                $nextUrut = $lastNumber + 1;
            }
            $formattedSequence = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
            $validated['kode_aset'] = $masterKode->kode_prefix . '.' . $formattedSequence;
        } else {
            // Jika kategori tidak diubah, pertahankan kode aset lama
            $validated['kode_aset'] = $barang->kode_aset;
        }

        $barang->update($validated);
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangInventaris $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * KHUSUS WAKA: Halaman Daftar Barang Pending Validasi
     */
    public function pendingValidation()
    {
        // Ambil barang yang statusnya 'pending'
        $barangs = BarangInventaris::where('status_validasi', 'pending')
                                    ->with('lokasi')
                                    ->orderBy('created_at', 'asc')
                                    ->get();
        
        $lokasis = LokasiRuangan::all(); // Untuk dropdown pilihan lokasi final

        return view('barang.pending_validation', compact('barangs', 'lokasis'));
    }

    /**
     * KHUSUS WAKA: Approve & Tempatkan Barang
     */
    public function approve(Request $request, BarangInventaris $barang)
    {
        $validated = $request->validate([
            'lokasi_id_final' => 'required|exists:lokasi_ruangan,id',
            'catatan_waka' => 'nullable|string|max:500',
        ]);

        // Update status jadi approved dan set lokasi final
        $barang->update([
            'status_validasi' => 'approved',
            'lokasi_id' => $validated['lokasi_id_final'], // Update lokasi sesuai keputusan Waka
            'catatan_waka' => $validated['catatan_waka'],
        ]);

        return redirect()->route('barang.pending.validation')->with('success', 'Barang berhasil divalidasi dan ditempatkan.');
    }

    /**
     * Generate QR Code Image
     */
    public function generateQr(BarangInventaris $barang)
    {
        // Generate QR Code dalam format SVG (Vektor, tidak butuh Imagick)
        // SVG lebih ringan dan tajam di semua browser
        $qrCode = QrCode::format('svg')
                        ->size(300)
                        ->errorCorrection('H')
                        ->generate($barang->kode_aset);
        
        // Return sebagai response dengan header SVG
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}