<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with(['user', 'pelanggan']);

        if ($request->filled('cari')) {
            $query->where('no_faktur', 'like', '%' . $request->cari . '%');
        }

        $penjualans = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama')->get()->map(fn($p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'telepon' => $p->telepon,
            'is_member' => $p->is_member,
            'member_id' => $p->member_id,
            'diskon_percent' => $p->is_member ? min(50, config('pos.diskon_member', 10)) : 0,
        ]);

        // Cuma barang yang aktif & masih ada stok yang bisa dijual
        $barangs = Barang::where('aktif', true)
            ->get()
            ->filter(fn($b) => $b->stokTotal() > 0)
            ->map(fn($b) => [
                'id' => $b->id,
                'nama' => $b->nama,
                'stok' => $b->stokTotal(),
                'harga' => $b->hargaJualTerkini(),
                'butuh_resep' => $b->butuh_resep,
                'diskon_custom_percent' => \App\Models\CustomDiscount::getPercentForBarang($b),
            ])
            ->values();

        return view('penjualan.create', compact('pelanggans', 'barangs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:100|unique:penjualans,no_faktur',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            $penjualan = DB::transaction(function () use ($data, $request) {
                // Tentukan diskon member dari backend
                $isMember = false;
                $diskonMemberPercent = 0;
                if (!empty($data['pelanggan_id'])) {
                    $pelanggan = Pelanggan::find($data['pelanggan_id']);
                    if ($pelanggan && $pelanggan->is_member) {
                        $isMember = true;
                        $diskonMemberPercent = min(50, config('pos.diskon_member', 10));
                    }
                }

                $penjualan = Penjualan::create([
                    'user_id' => $request->user()->id,
                    'pelanggan_id' => $data['pelanggan_id'] ?? null,
                    'tanggal' => $data['tanggal'],
                    'no_faktur' => $data['no_faktur'],
                    'total' => 0,
                ]);

                $totalFaktur = 0;

                foreach ($data['items'] as $item) {
                    $barang = Barang::lockForUpdate()->findOrFail($item['barang_id']);
                    $jumlahDiminta = $item['jumlah'];
                    $sisaJumlah = $jumlahDiminta;

                    // Dapatkan diskon custom untuk barang ini dari database
                    $diskonCustomPercent = \App\Models\CustomDiscount::getPercentForBarang($barang);

                    // Diskon gabungan di-cap maksimal 50%
                    $totalDiskonPercent = min(50, $diskonMemberPercent + $diskonCustomPercent);

                    // Ambil batch aktif & masih ada stok, urut FEFO (expired paling dekat duluan)
                    $batches = $barang->detailPenerimaan()
                        ->lockForUpdate()
                        ->where('aktif', true)
                        ->where('stok', '>', 0)
                        ->orderBy('expired_date')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($sisaJumlah <= 0) {
                            break;
                        }

                        $ambil = min($sisaJumlah, $batch->stok);

                        // Diskon dihitung berdasarkan nominal rupiah (actual price * qty) * (totalDiskonPercent / 100)
                        $nominalDiskon = round(($batch->harga_jual * $ambil) * ($totalDiskonPercent / 100), 2);
                        $subtotal = ($batch->harga_jual * $ambil) - $nominalDiskon;

                        $detailPenjualan = DetailPenjualan::create([
                            'penjualan_id' => $penjualan->id,
                            'detail_penerimaan_id' => $batch->id,
                            'harga_jual' => $batch->harga_jual,
                            'jumlah' => $ambil,
                            'diskon' => $nominalDiskon,
                            'subtotal' => $subtotal,
                        ]);

                        // Audit penggunaan diskon
                        if ($totalDiskonPercent > 0) {
                            $sumPercent = $diskonMemberPercent + $diskonCustomPercent;
                            if ($sumPercent > 0) {
                                $ratioMember = $diskonMemberPercent / $sumPercent;
                                $ratioCustom = $diskonCustomPercent / $sumPercent;

                                $actualMemberPercent = (int) round($totalDiskonPercent * $ratioMember);
                                $actualCustomPercent = $totalDiskonPercent - $actualMemberPercent;

                                $nominalMember = round($nominalDiskon * $ratioMember, 2);
                                $nominalCustom = round($nominalDiskon - $nominalMember, 2);

                                if ($actualMemberPercent > 0 && $nominalMember > 0) {
                                    \App\Models\DiscountUsage::create([
                                        'penjualan_id' => $penjualan->id,
                                        'detail_penjualan_id' => $detailPenjualan->id,
                                        'barang_id' => $barang->id,
                                        'barang_nama' => $barang->nama,
                                        'jenis' => 'member',
                                        'custom_discount_id' => null,
                                        'custom_discount_nama' => null,
                                        'persentase' => $actualMemberPercent,
                                        'nominal' => $nominalMember,
                                    ]);
                                }

                                if ($actualCustomPercent > 0 && $nominalCustom > 0) {
                                    $activePromoObj = \App\Models\CustomDiscount::aktifHariIni()
                                        ->where(function ($query) use ($barang) {
                                            $query->where('cakupan', 'semua')
                                                ->orWhere(function ($q) use ($barang) {
                                                    $q->where('cakupan', 'kategori')
                                                      ->whereHas('kategoris', function ($qk) use ($barang) {
                                                          $qk->where('kategoris.id', $barang->kategori_id);
                                                      });
                                                })
                                                ->orWhere(function ($q) use ($barang) {
                                                    $q->where('cakupan', 'barang')
                                                      ->whereHas('barangs', function ($qb) use ($barang) {
                                                          $qb->where('barangs.id', $barang->id);
                                                      });
                                                })
                                                ->orWhere(function ($q) use ($barang) {
                                                    $q->where('cakupan', 'kombinasi')
                                                      ->where(function ($qc) use ($barang) {
                                                          $qc->whereHas('kategoris', function ($qk) use ($barang) {
                                                              $qk->where('kategoris.id', $barang->kategori_id);
                                                          })->orWhereHas('barangs', function ($qb) use ($barang) {
                                                              $qb->where('barangs.id', $barang->id);
                                                          });
                                                      });
                                                });
                                        })
                                        ->first();

                                    $promoId = $activePromoObj ? $activePromoObj->id : null;
                                    $promoNama = $activePromoObj ? $activePromoObj->nama : 'Promo Custom';

                                    \App\Models\DiscountUsage::create([
                                        'penjualan_id' => $penjualan->id,
                                        'detail_penjualan_id' => $detailPenjualan->id,
                                        'barang_id' => $barang->id,
                                        'barang_nama' => $barang->nama,
                                        'jenis' => 'custom',
                                        'custom_discount_id' => $promoId,
                                        'custom_discount_nama' => $promoNama,
                                        'persentase' => $actualCustomPercent,
                                        'nominal' => $nominalCustom,
                                    ]);
                                }
                            }
                        }

                        $batch->decrement('stok', $ambil);
                        if ($batch->stok - $ambil <= 0) {
                            $batch->update(['aktif' => false]);
                        }

                        $totalFaktur += $subtotal;
                        $sisaJumlah -= $ambil;
                    }

                    if ($sisaJumlah > 0) {
                        throw ValidationException::withMessages([
                            'items' => "Stok {$barang->nama} tidak mencukupi (kurang {$sisaJumlah}).",
                        ]);
                    }
                }

                $penjualan->update(['total' => $totalFaktur]);

                \App\Models\ActivityLog::log('Transaksi Penjualan', "Invoice: {$penjualan->no_faktur}, Total: Rp " . number_format($totalFaktur, 2));

                return $penjualan;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('penjualan.show', $penjualan)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'pelanggan', 'detail.detailPenerimaan.barang']);

        return view('penjualan.show', compact('penjualan'));
    }
}
