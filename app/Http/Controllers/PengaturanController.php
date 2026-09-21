<?php

namespace App\Http\Controllers;

use App\Models\InfoApotek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'nama_pemilik' => 'nullable|string|max:255',
            'alamat_jalan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
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
        Cache::forget('info_apotek');

        \App\Models\ActivityLog::log(
            'Update Pengaturan Apotek',
            "Nama: {$apotek->nama_apotek}",
            \App\Models\ActivityLog::CATEGORY_SISTEM
        );

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}