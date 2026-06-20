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

        // 2. Format nomor urut manual jadi 3 digit (misal: input 1 -> 001, input 25 -> 025)
        $formattedSequence = str_pad($validated['nomor_urut'], 3, '0', STR_PAD_LEFT);
        
        // 3. Gabungkan prefix dan nomor urut
        $kodeAset = $masterKode->kode_prefix . '.' . $formattedSequence;

        // 4. Cek duplikasi kode aset (mencegah user input nomor yang sudah dipakai)
        if (BarangInventaris::where('kode_aset', $kodeAset)->exists()) {
            return back()->withErrors(['nomor_urut' => 'Nomor urut ini sudah terpakai untuk prefix tersebut!'])->withInput();
        }

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
        // Cek apakah barang masih pending, jika sudah approved tidak boleh diedit sembarangan
        if ($barang->status_validasi === 'approved') {
            return abort(403, 'Barang sudah divalidasi, tidak dapat diedit melalui form ini.');
        }

        $lokasis = LokasiRuangan::all();
        return view('barang.edit', compact('barang', 'lokasis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangInventaris $barang)
    {
        if ($barang->status_validasi === 'approved') {
            return abort(403, 'Barang sudah divalidasi.');
        }

        $validated = $request->validate([
            'kode_aset' => 'required|unique:barang_inventaris,kode_aset,' . $barang->id,
            'lokasi_id' => 'required|exists:lokasi_ruangan,id',
            'kategori' => 'required|string',
            'merek' => 'required|string',
            'jenis' => 'required|string',
            'harga_perolehan' => 'required|numeric|min:0',
            'tanggal_perolehan' => 'required|date',
        ]);

        $barang->update($validated);
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangInventaris $barang)
    {
        // Hanya hapus jika belum approved
        if ($barang->status_validasi === 'approved') {
            return abort(403, 'Barang yang sudah divalidasi tidak boleh dihapus.');
        }

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