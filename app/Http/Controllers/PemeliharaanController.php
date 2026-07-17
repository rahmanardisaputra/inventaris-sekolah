<?php

namespace App\Http\Controllers;

use App\Models\LaporanPemeliharaan;
use App\Models\BarangInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemeliharaanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar laporan berdasarkan role user.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LaporanPemeliharaan::with(['barang', 'pelapor']);

        // Filter berdasarkan status jika ada parameter di URL
        if ($request->has('status')) {
            $query->where('status_laporan', $request->status);
        }

        // Logika tampilan berdasarkan Role
        if ($user->role === 'penjaga') {
            // Penjaga hanya melihat laporannya sendiri
            $query->where('pelapor_id', $user->id);
        } elseif ($user->role === 'staff') {
            // Staff melihat laporan yang butuh validasi awal (draft/revisi)
            // Atau semua laporan untuk monitoring
        } elseif ($user->role === 'waka') {
            // Waka melihat laporan yang sudah divalidasi staff
             // $query->where('status_laporan', 'validated_staff'); 
             // Kita biarkan Waka lihat semua tapi filter default nanti di view
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pemeliharaan.index', compact('laporans'));
    }

    /**
     * Show the form for creating a new resource.
     * HANYA PENJAGA yang boleh membuat laporan baru.
     */
    public function create()
    {
        // Ambil barang yang statusnya 'approved' saja
        $barangs = BarangInventaris::where('status_validasi', 'approved')->get();
        return view('pemeliharaan.create', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     * HANYA PENJAGA.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang_inventaris,id',
            'deskripsi_kerusakan' => 'required|string',
            'foto_bukti_awal' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan foto
        $path = $request->file('foto_bukti_awal')->store('bukti_awal', 'public');
        $validated['foto_bukti_awal'] = $path;
        $validated['pelapor_id'] = auth()->id();
        $validated['status_laporan'] = 'draft'; // Status awal

        LaporanPemeliharaan::create($validated);

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan kerusakan berhasil dikirim. Menunggu validasi Staff.');
    }

    /**
     * Show the form for editing the specified resource.
     * Untuk revisi oleh Penjaga atau Update Tindak Lanjut oleh Waka.
     */
    public function edit(LaporanPemeliharaan $laporan)
    {
        // Cek hak akses sederhana
        if (auth()->user()->role === 'penjaga' && $laporan->pelapor_id !== auth()->id()) {
            abort(403);
        }
        
        $barangs = BarangInventaris::where('status_validasi', 'approved')->get();
        return view('pemeliharaan.edit', compact('laporan', 'barangs'));
    }

    /**
     * Update the specified resource in storage.
     * Digunakan untuk:
     * 1. Penjaga merevisi laporan (Status: draft/revisi)
     * 2. Waka memberikan tindak lanjut (Status: validated_staff)
     * 3. Penjaga menyelesaikan perbaikan (Status: approved_waka)
     */
    public function update(Request $request, LaporanPemeliharaan $laporan)
    {
        $user = auth()->user();

        // KASUS 1: Penjaga Revisi Laporan (Draft/Revisi)
        if ($user->role === 'penjaga' && in_array($laporan->status_laporan, ['draft', 'revisi'])) {
            $validated = $request->validate([
                'deskripsi_kerusakan' => 'required|string',
                'foto_bukti_awal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($request->hasFile('foto_bukti_awal')) {
                // Hapus foto lama jika ada
                if ($laporan->foto_bukti_awal) Storage::disk('public')->delete($laporan->foto_bukti_awal);
                $path = $request->file('foto_bukti_awal')->store('bukti_awal', 'public');
                $validated['foto_bukti_awal'] = $path;
            }

            $laporan->update($validated);
            // Status tetap draft/revisi, menunggu staff validasi ulang
            return redirect()->route('pemeliharaan.index')->with('success', 'Laporan berhasil direvisi.');
        }

        // KASUS 2: Waka Berikan Tindak Lanjut (Validated Staff -> Approved Waka)
        if ($user->role === 'waka' && $laporan->status_laporan === 'validated_staff') {
            $validated = $request->validate([
                'instruksi_cepat' => 'nullable|string',
                'tindakan_waka' => 'required_without:instruksi_cepat|nullable|string',
                'biaya_estimasi' => 'nullable|numeric|min:0',
            ], [
                'tindakan_waka.required_without' => 'Harap pilih Instruksi Cepat atau isi Catatan Tambahan.'
            ]);

            $finalInstruksi = [];
            if (!empty($validated['instruksi_cepat'])) {
                $finalInstruksi[] = $validated['instruksi_cepat'];
            }
            if (!empty($validated['tindakan_waka'])) {
                $finalInstruksi[] = trim($validated['tindakan_waka']);
            }
            
            $gabunganInstruksi = implode(' - ', $finalInstruksi);

            $laporan->update([
                'tindakan_waka' => $gabunganInstruksi,
                'biaya_estimasi' => $validated['biaya_estimasi'],
                'status_laporan' => 'approved_waka',
            ]);

            return redirect()->route('pemeliharaan.index')->with('success', 'Tindak lanjut disetujui. Silakan lakukan perbaikan.');
        }

        // KASUS 3: Penjaga Selesaikan Perbaikan (Approved Waka -> Selesai)
        if ($user->role === 'penjaga' && $laporan->status_laporan === 'approved_waka') {
            $validated = $request->validate([
                'foto_bukti_akhir' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $path = $request->file('foto_bukti_akhir')->store('bukti_akhir', 'public');
            
            $laporan->update([
                'foto_bukti_akhir' => $path,
                'status_laporan' => 'selesai',
                'tanggal_selesai' => now(),
            ]);

            // Opsional: Update kondisi barang jadi 'Baik' otomatis
            $laporan->barang->update(['kondisi_terkini' => 'Baik']);

            return redirect()->route('pemeliharaan.index')->with('success', 'Perbaikan selesai dilaporkan.');
        }

        return abort(403, 'Aksi tidak diizinkan.');
    }

    /**
     * KHUSUS STAFF: Validasi Laporan Awal
     */
    public function validateByStaff(LaporanPemeliharaan $laporan)
    {
        if (auth()->user()->role !== 'staff') abort(403);
        
        // Cek status harus draft atau revisi
        if (!in_array($laporan->status_laporan, ['draft', 'revisi'])) {
            return back()->with('error', 'Status laporan tidak valid untuk divalidasi.');
        }

        $laporan->update(['status_laporan' => 'validated_staff']);

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan divalidasi. Menunggu persetujuan Waka.');
    }

    /**
     * KHUSUS STAFF: Minta Revisi ke Penjaga
     */
    public function requestRevision(LaporanPemeliharaan $laporan)
    {
        if (auth()->user()->role !== 'staff') abort(403);

        if ($laporan->status_laporan !== 'draft') {
             return back()->with('error', 'Hanya laporan draft yang bisa direvisi.');
        }

        $laporan->update(['status_laporan' => 'revisi']);

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan dikembalikan ke penjaga untuk revisi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanPemeliharaan $laporan)
    {
        // Hanya bisa hapus jika masih draft dan milik sendiri
        if ($laporan->pelapor_id !== auth()->id() || $laporan->status_laporan !== 'draft') {
            abort(403);
        }

        // Hapus foto
        if ($laporan->foto_bukti_awal) Storage::disk('public')->delete($laporan->foto_bukti_awal);
        if ($laporan->foto_bukti_akhir) Storage::disk('public')->delete($laporan->foto_bukti_akhir);

        $laporan->delete();

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan dibatalkan.');
    }
}