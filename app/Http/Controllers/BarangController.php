<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'satuan', 'pabrik']);

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_apotek', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_kfa', 'like', '%' . $request->cari . '%')
                  ->orWhere('merk', 'like', '%' . $request->cari . '%')
                  ->orWhere('barcode', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('butuh_resep')) {
            $query->where('butuh_resep', $request->butuh_resep === '1');
        }

        if ($request->filled('stok')) {
            $stok = $request->stok;
            if ($stok === 'habis') {
                $query->whereRaw('NOT EXISTS (
                    SELECT 1 FROM detail_penerimaans dp
                    WHERE dp.barang_id = barangs.id
                    AND dp.aktif = 1
                    AND dp.stok > 0
                )');
            } elseif ($stok === 'menipis') {
                $query->whereRaw('EXISTS (
                    SELECT 1 FROM detail_penerimaans dp
                    WHERE dp.barang_id = barangs.id
                    AND dp.aktif = 1
                    AND dp.stok > 0
                )')->whereRaw('(
                    SELECT COALESCE(SUM(dp.stok), 0) FROM detail_penerimaans dp
                    WHERE dp.barang_id = barangs.id
                    AND dp.aktif = 1
                    AND dp.stok > 0
                ) <= barangs.stok_minimum');
            } elseif ($stok === 'aman') {
                $query->whereRaw('(
                    SELECT COALESCE(SUM(dp.stok), 0) FROM detail_penerimaans dp
                    WHERE dp.barang_id = barangs.id
                    AND dp.aktif = 1
                    AND dp.stok > 0
                ) > barangs.stok_minimum');
            }
        }

        $barangs = $query->orderBy('kode_apotek')->paginate(15)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();
        $satuans = Satuan::orderBy('nama')->get();
        $pabriks = Pabrik::orderBy('nama')->get();

        return view('barang.index', compact('barangs', 'kategoris', 'satuans', 'pabriks'));
    }

    public function create()
    {
        return redirect()->route('barang.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode_kfa' => 'nullable|string|max:100',
            'merk' => 'nullable|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'pabrik_id' => 'required|exists:pabriks,id',
            'barcode' => 'nullable|string|max:100|unique:barangs,barcode',
            'butuh_resep' => 'nullable|boolean',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $data['butuh_resep'] = $request->boolean('butuh_resep');

        // Generate Kode Apotek otomatis dari sistem (contoh: P-0001)
        $data['kode_apotek'] = Barang::generateKodeApotek($data['nama']);

        Barang::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Barang berhasil ditambahkan.'], 201);
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return redirect()->route('barang.index');
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode_kfa' => 'nullable|string|max:100',
            'merk' => 'nullable|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'pabrik_id' => 'required|exists:pabriks,id',
            'barcode' => 'nullable|string|max:100|unique:barangs,barcode,' . $barang->id,
            'butuh_resep' => 'nullable|boolean',
            'stok_minimum' => 'required|integer|min:0',
            'aktif' => 'nullable|boolean',
        ]);

        $data['butuh_resep'] = $request->boolean('butuh_resep');
        $data['aktif'] = $request->boolean('aktif');

        // Kode apotek tidak boleh diubah setelah dibuat
        unset($data['kode_apotek']);

        $barang->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Barang berhasil diperbarui.']);
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function show(Request $request, Barang $barang)
    {
        $barang->load(['kategori', 'satuan', 'pabrik', 'detailPenerimaan.penerimaan.supplier']);

        $stokTotal = $barang->stokTotal();
        $statusStok = 'Aman';
        $statusStokBadge = 'badge-success';
        if ($stokTotal <= 0) {
            $statusStok = 'Habis';
            $statusStokBadge = 'badge-danger';
        } elseif ($stokTotal <= $barang->stok_minimum) {
            $statusStok = 'Menipis';
            $statusStokBadge = 'badge-warning';
        }

        $supplierNama = $barang->supplierNama();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $barang->id,
                'kode_apotek' => $barang->kode_apotek ?? '—',
                'kode_kfa' => $barang->kode_kfa ?? '—',
                'nama' => $barang->nama,
                'merk' => $barang->merk ?? '—',
                'barcode' => $barang->barcode ?? '—',
                'kategori' => $barang->kategori->nama ?? '—',
                'satuan' => $barang->satuan->nama ?? '—',
                'pabrik' => $barang->pabrik->nama ?? '—',
                'supplier' => $supplierNama,
                'stok' => $stokTotal,
                'stok_minimum' => $barang->stok_minimum,
                'status_stok' => $statusStok,
                'status_stok_badge' => $statusStokBadge,
                'butuh_resep' => $barang->butuh_resep ? 'Wajib Resep' : 'Bebas (Tanpa Resep)',
                'aktif' => $barang->aktif ? 'Aktif' : 'Nonaktif',
                'edit_url' => route('barang.edit', $barang),
            ]);
        }

        return view('barang.show', compact('barang', 'stokTotal', 'statusStok', 'statusStokBadge', 'supplierNama'));
    }

    public function destroy(Barang $barang)
    {
        if ($barang->detailPenerimaan()->exists()) {
            return back()->with('error', 'Barang tidak bisa dihapus karena punya riwayat penerimaan. Nonaktifkan saja lewat form edit.');
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = Barang::with(['kategori', 'satuan', 'pabrik']);

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_apotek', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_kfa', 'like', '%' . $request->cari . '%')
                  ->orWhere('merk', 'like', '%' . $request->cari . '%')
                  ->orWhere('barcode', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('butuh_resep')) {
            $query->where('butuh_resep', $request->butuh_resep === '1');
        }

        $barangs = $query->orderBy('nama')->get();

        $headers = [
            'No',
            'Kode Apotek',
            'Kode KFA',
            'Nama Barang / Produk',
            'Merk',
            'Kategori',
            'Satuan',
            'Pabrik',
            'Barcode',
            'Stok Saat Ini',
            'Stok Minimum',
            'Wajib Resep',
            'Status'
        ];

        $data = [];
        foreach ($barangs as $index => $barang) {
            $data[] = [
                $index + 1,
                $barang->kode_apotek ?? '-',
                $barang->kode_kfa ?? '-',
                $barang->nama,
                $barang->merk ?? '-',
                $barang->kategori->nama ?? '-',
                $barang->satuan->nama ?? '-',
                $barang->pabrik->nama ?? '-',
                $barang->barcode ?? '-',
                $barang->stokTotal(),
                $barang->stok_minimum,
                $barang->butuh_resep ? 'Ya' : 'Tidak',
                $barang->aktif ? 'Aktif' : 'Nonaktif',
            ];
        }

        return $this->exportCsv('data-master-barang-' . now()->format('Ymd_His') . '.csv', $headers, $data);
    }

    public function importTemplate()
    {
        $headers = ['nama', 'kode_kfa', 'merk', 'kategori', 'satuan', 'pabrik', 'barcode', 'stok_minimum', 'butuh_resep'];
        $data = [
            ['Paracetamol 500mg', 'KFA-00123', 'Kimia Farma', 'Obat Bebas', 'Strip', 'Kimia Farma', '8991234567890', '10', '0'],
            ['Amoxicillin 500mg', 'KFA-00456', 'Kalbe', 'Antibiotik', 'Kapsul', 'Kalbe Farma', '8990987654321', '15', '1'],
            ['Vitamin C 500mg', '', 'Sanbe', 'Suplemen', 'Botol', 'Sanbe Farma', '', '20', '0'],
        ];

        return $this->exportCsv('template-import-barang.csv', $headers, $data);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'auto_create_master' => 'nullable|boolean',
        ], [
            'file.required' => 'File CSV wajib dipilih.',
            'file.mimes' => 'Format file harus berupa CSV (.csv atau .txt).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');
        $autoCreateMaster = $request->boolean('auto_create_master', true);

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        // Deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false && substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        // Baca baris header
        $rawHeader = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeader) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong atau format tidak valid.');
        }

        // Hapus UTF-8 BOM jika ada & normalisasi nama kolom
        $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeader[0]);
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', (string) $h))));
        }, $rawHeader);

        // Petakan indeks kolom
        $colMap = [];
        foreach ($headers as $idx => $name) {
            if (in_array($name, ['nama', 'nama_barang', 'nama_produk', 'produk'])) $colMap['nama'] = $idx;
            elseif (in_array($name, ['kode_kfa', 'kfa', 'kodekfa'])) $colMap['kode_kfa'] = $idx;
            elseif (in_array($name, ['merk', 'brand', 'merek'])) $colMap['merk'] = $idx;
            elseif (in_array($name, ['kategori', 'kategori_id', 'nama_kategori'])) $colMap['kategori'] = $idx;
            elseif (in_array($name, ['satuan', 'satuan_id', 'nama_satuan'])) $colMap['satuan'] = $idx;
            elseif (in_array($name, ['pabrik', 'pabrik_id', 'nama_pabrik', 'produsen'])) $colMap['pabrik'] = $idx;
            elseif (in_array($name, ['barcode', 'kode_barcode', 'kode_barang'])) $colMap['barcode'] = $idx;
            elseif (in_array($name, ['stok_minimum', 'min_stok', 'stok_min', 'minimum_stok'])) $colMap['stok_minimum'] = $idx;
            elseif (in_array($name, ['butuh_resep', 'resep', 'wajib_resep'])) $colMap['butuh_resep'] = $idx;
        }

        if (!isset($colMap['nama'])) {
            fclose($handle);
            return back()->with('error', 'Format CSV tidak sesuai. Kolom "nama" wajib ada.');
        }

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $rowNum = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNum++;
                // Lewati baris kosong
                if (empty(array_filter($row, fn ($v) => trim((string) $v) !== ''))) {
                    continue;
                }

                $nama = isset($colMap['nama'], $row[$colMap['nama']]) ? trim($row[$colMap['nama']]) : '';
                if ($nama === '') {
                    $skippedCount++;
                    $errors[] = "Baris {$rowNum}: Nama barang kosong.";
                    continue;
                }

                $kodeKfa = isset($colMap['kode_kfa'], $row[$colMap['kode_kfa']]) ? trim($row[$colMap['kode_kfa']]) : null;
                if ($kodeKfa === '' || $kodeKfa === '-') $kodeKfa = null;

                $merk = isset($colMap['merk'], $row[$colMap['merk']]) ? trim($row[$colMap['merk']]) : null;
                if ($merk === '' || $merk === '-') $merk = null;

                $kategoriName = isset($colMap['kategori'], $row[$colMap['kategori']]) ? trim($row[$colMap['kategori']]) : '';
                $satuanName = isset($colMap['satuan'], $row[$colMap['satuan']]) ? trim($row[$colMap['satuan']]) : '';
                $pabrikName = isset($colMap['pabrik'], $row[$colMap['pabrik']]) ? trim($row[$colMap['pabrik']]) : '';
                $barcode = isset($colMap['barcode'], $row[$colMap['barcode']]) ? trim($row[$colMap['barcode']]) : null;
                if ($barcode === '' || $barcode === '-') $barcode = null;

                $stokMinRaw = isset($colMap['stok_minimum'], $row[$colMap['stok_minimum']]) ? trim($row[$colMap['stok_minimum']]) : '0';
                $stokMinimum = is_numeric($stokMinRaw) ? max(0, (int) $stokMinRaw) : 0;

                $butuhResepRaw = isset($colMap['butuh_resep'], $row[$colMap['butuh_resep']]) ? strtolower(trim($row[$colMap['butuh_resep']])) : '0';
                $butuhResep = in_array($butuhResepRaw, ['1', 'true', 'ya', 'wajib', 'yes', 'y'], true);

                if ($kategoriName === '') $kategoriName = 'Umum';
                if ($satuanName === '') $satuanName = 'Pcs';
                if ($pabrikName === '') $pabrikName = 'Umum';

                if ($autoCreateMaster) {
                    $kategori = Kategori::firstOrCreate(['nama' => $kategoriName]);
                    $satuan = Satuan::firstOrCreate(['nama' => $satuanName]);
                    $pabrik = Pabrik::firstOrCreate(['nama' => $pabrikName]);
                } else {
                    $kategori = Kategori::where('nama', $kategoriName)->first();
                    $satuan = Satuan::where('nama', $satuanName)->first();
                    $pabrik = Pabrik::where('nama', $pabrikName)->first();

                    if (!$kategori || !$satuan || !$pabrik) {
                        $skippedCount++;
                        $errors[] = "Baris {$rowNum}: Master data ('{$kategoriName}', '{$satuanName}', '{$pabrikName}') belum terdaftar di sistem.";
                        continue;
                    }
                }

                // Cek duplikasi berdasarkan barcode, kode kfa, atau nama
                $existingBarang = null;
                if ($barcode) {
                    $existingBarang = Barang::where('barcode', $barcode)->first();
                }
                if (!$existingBarang && $kodeKfa) {
                    $existingBarang = Barang::where('kode_kfa', $kodeKfa)->first();
                }
                if (!$existingBarang) {
                    $existingBarang = Barang::where('nama', $nama)->first();
                }

                if ($existingBarang) {
                    $existingBarang->update([
                        'kode_kfa' => $kodeKfa ?? $existingBarang->kode_kfa,
                        'merk' => $merk ?? $existingBarang->merk,
                        'kategori_id' => $kategori->id,
                        'satuan_id' => $satuan->id,
                        'pabrik_id' => $pabrik->id,
                        'barcode' => $barcode ?? $existingBarang->barcode,
                        'stok_minimum' => $stokMinimum,
                        'butuh_resep' => $butuhResep,
                        'aktif' => true,
                    ]);
                    $updatedCount++;
                } else {
                    // Generate Kode Apotek baru otomatis
                    $kodeApotek = Barang::generateKodeApotek($nama);

                    Barang::create([
                        'kode_apotek' => $kodeApotek,
                        'kode_kfa' => $kodeKfa,
                        'nama' => $nama,
                        'merk' => $merk,
                        'kategori_id' => $kategori->id,
                        'satuan_id' => $satuan->id,
                        'pabrik_id' => $pabrik->id,
                        'barcode' => $barcode,
                        'stok_minimum' => $stokMinimum,
                        'butuh_resep' => $butuhResep,
                        'aktif' => true,
                    ]);
                    $importedCount++;
                }
            }

            DB::commit();
            fclose($handle);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan saat memproses data import: ' . $e->getMessage());
        }

        $msg = "Import data selesai: {$importedCount} barang baru ditambahkan, {$updatedCount} barang diperbarui.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris dilewati).";
        }

        return redirect()->route('barang.index')->with('success', $msg);
    }

    private function exportCsv(string $filename, array $headers, array $data)
    {
        $callback = function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM untuk kompatibilitas Excel
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
