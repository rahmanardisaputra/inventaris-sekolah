<?php

namespace App\Http\Controllers;

use App\Models\InstruksiPemeliharaan;
use App\Models\MasterKodeAset;
use Illuminate\Http\Request;

class InstruksiPemeliharaanController extends Controller
{
    public function index(MasterKodeAset $masterKodeAset)
    {
        $instruksis = $masterKodeAset->instruksiPemeliharaans;
        return view('instruksi.index', compact('masterKodeAset', 'instruksis'));
    }

    public function store(Request $request, MasterKodeAset $masterKodeAset)
    {
        $request->validate([
            'instruksi' => 'required|string|max:255',
        ]);

        $masterKodeAset->instruksiPemeliharaans()->create([
            'instruksi' => $request->instruksi
        ]);

        return redirect()->back()->with('success', 'Instruksi berhasil ditambahkan.');
    }

    public function update(Request $request, InstruksiPemeliharaan $instruksiPemeliharaan)
    {
        $request->validate([
            'instruksi' => 'required|string|max:255',
        ]);

        $instruksiPemeliharaan->update([
            'instruksi' => $request->instruksi
        ]);

        return redirect()->back()->with('success', 'Instruksi berhasil diperbarui.');
    }

    public function destroy(InstruksiPemeliharaan $instruksiPemeliharaan)
    {
        $instruksiPemeliharaan->delete();
        return redirect()->back()->with('success', 'Instruksi berhasil dihapus.');
    }
}
