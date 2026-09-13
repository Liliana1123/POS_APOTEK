<?php

namespace App\Http\Controllers;

use App\Models\InfoApotek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $apotek = InfoApotek::singleton();
        return view('pengaturan.index', compact('apotek'));
    }

    public function update(Request $request)
    {
        $apotek = InfoApotek::singleton();

        $data = $request->validate([
            'nama_apotek' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'no_izin_sia' => 'nullable|string|max:100',
            'nama_apoteker_pj' => 'nullable|string|max:255',
            'no_sipa' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($apotek->logo) {
                Storage::disk('public')->delete($apotek->logo);
            }
            $data['logo'] = $request->file('logo')->store('info-apotek', 'public');
        }

        $apotek->update($data);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}