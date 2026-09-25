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
        $query = Penjualan::with(['user', 'pelanggan', 'detail']);

        // Pencarian nomor faktur
        if ($request->filled('cari')) {
            $query->where(
                'no_faktur',
                'like',
                '%' . $request->cari . '%'
            );
        }

        // Filter tanggal awal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $request->tanggal_awal
            );
        }

        // Filter tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $request->tanggal_akhir
            );
        }

        // Sorting No. Invoice Fitur Penjualan
        $sort = $request->input('sort', 'no_faktur');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = [
            'no_faktur',
            'tanggal',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'no_faktur';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        // Data lengkap untuk laporan cetak, mengikuti filter yang sama
        $laporanPenjualans = (clone $query)
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_faktur', 'desc')
            ->get();

        // Data tabel riwayat tetap memakai pagination
        $penjualans = $query
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $totalDiskonLaporan = $laporanPenjualans->sum(function ($penjualan) {
            return $penjualan->detail->sum('diskon');
        });

        $totalTransaksiLaporan = $laporanPenjualans->sum('total');

        return view('penjualan.index', compact(
            'penjualans',
            'laporanPenjualans',
            'totalDiskonLaporan',
            'totalTransaksiLaporan'
        ));
    }

    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama')
            ->get()
            ->map(function ($p) {
                $memberDiscount = $p->custom_discount_percentage !== null
                    ? (float) $p->custom_discount_percentage
                    : (float) config('pos.diskon_member', 10);

                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'telepon' => $p->telepon,
                    'is_member' => (bool) $p->is_member,
                    'member_aktif' => (bool) ($p->member_aktif ?? true),
                    'member_id' => $p->member_id,
                    'custom_discount_percentage' => $p->custom_discount_percentage !== null
                        ? (float) $p->custom_discount_percentage
                        : null,
                    'diskon_percent' => ($p->is_member && ($p->member_aktif ?? false))
                        ? min(50, $memberDiscount)
                        : 0,
                ];
            });

        // Cuma barang yang aktif & masih ada stok yang bisa dijual
        $barangs = Barang::where('aktif', true)
            ->get()
            ->filter(fn($b) => $b->stokTotal() > 0)
            ->map(fn($b) => [
                'id'                  => $b->id,
                'kode_apotek'         => $b->kode_apotek,
                'nama'                => $b->nama,
                'stok'                => $b->stokTotal(),
                'harga'               => $b->hargaJualTerkini(),
                'butuh_resep'         => $b->butuh_resep,
                'diskon_custom_percent' => \App\Models\CustomDiscount::getPercentForBarang($b),
            ])
            ->values();

        return view('penjualan.create', compact('pelanggans', 'barangs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'pelanggan_nama' => 'nullable|string|max:255',
            'pelanggan_telepon' => 'nullable|string|max:30',
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:100|unique:penjualans,no_faktur',
            'jenis_transaksi' => 'required|in:non_resep,resep',
            'nama_dokter' => 'nullable|string|max:255',
            'id_dokter' => 'nullable|string|max:255',
            'alamat_lembaga' => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|in:cash,qris,debit,piutang',
            'due_date' => 'nullable|date',
            'qris_lunas' => 'nullable|boolean',
            'debit_lunas' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($data['metode_pembayaran'] === 'qris' && !$request->boolean('qris_lunas')) {
            throw ValidationException::withMessages([
                'qris_lunas' => 'Konfirmasi pembayaran QRIS dengan mencentang Lunas.',
            ]);
        }

        if ($data['metode_pembayaran'] === 'debit' && !$request->boolean('debit_lunas')) {
            throw ValidationException::withMessages([
                'debit_lunas' => 'Konfirmasi pembayaran Debit dengan mencentang Lunas.',
            ]);
        }

        // Validasi Piutang & Member
        if ($data['metode_pembayaran'] === 'piutang') {
            if (empty($data['pelanggan_id'])) {
                throw ValidationException::withMessages([
                    'metode_pembayaran' => 'Metode pembayaran Piutang hanya dapat digunakan oleh Member.',
                ]);
            }

            $pelangganCheck = Pelanggan::find($data['pelanggan_id']);
            if (!$pelangganCheck || !$pelangganCheck->is_member || !($pelangganCheck->member_aktif ?? false)) {
                throw ValidationException::withMessages([
                    'metode_pembayaran' => 'Metode pembayaran Piutang hanya dapat digunakan oleh Member.',
                ]);
            }

            if (!empty($data['due_date'])) {
                $tglTransaksi = \Illuminate\Support\Carbon::parse($data['tanggal'])->startOfDay();
                $tglJatuhTempo = \Illuminate\Support\Carbon::parse($data['due_date'])->startOfDay();

                if ($tglJatuhTempo->lt($tglTransaksi)) {
                    throw ValidationException::withMessages([
                        'due_date' => 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal transaksi.',
                    ]);
                }

                $finalDueDate = $tglJatuhTempo->format('Y-m-d');
            } else {
                $finalDueDate = \Illuminate\Support\Carbon::parse($data['tanggal'])->addMonth()->format('Y-m-d');
            }
        } else {
            $finalDueDate = null;
        }

        try {
            $penjualan = DB::transaction(function () use ($data, $request, $finalDueDate) {

                        // Tentukan pelanggan yang digunakan dalam transaksi
                if (empty($data['pelanggan_id'])) {

                    // Jika nama pelanggan diisi
                    if (!empty($data['pelanggan_nama'])) {

                        $pelanggan = null;

                        // Cari berdasarkan nomor HP jika tersedia
                        if (!empty($data['pelanggan_telepon'])) {
                            $pelanggan = Pelanggan::where(
                                'telepon',
                                $data['pelanggan_telepon']
                            )->first();
                        }

                        // Jika belum ditemukan, cari berdasarkan nama
                        if (!$pelanggan) {
                            $pelanggan = Pelanggan::where(
                                'nama',
                                $data['pelanggan_nama']
                            )->first();
                        }

                        // Jika belum ada, buat pelanggan belum member
                        if (!$pelanggan) {
                            $pelanggan = Pelanggan::create([
                                'nama' => $data['pelanggan_nama'],
                                'telepon' => $data['pelanggan_telepon'] ?? null,
                                'alamat' => null,
                                'tanggal_lahir' => null,
                                'keterangan' => null,
                                'member_id' => null,
                                'is_member' => false,
                                'member_aktif' => false,
                                'member_since' => null,
                                'saldo_piutang' => 0,
                            ]);
                        }

                    } else {

                        // Jika tidak ada nama, gunakan Pelanggan Umum.
                        // Pelanggan Umum bukan member dan tidak memiliki member_id.
                        $pelanggan = Pelanggan::where('is_member', false)
                            ->where(function ($query) {
                                $query->where('keterangan', 'Pelanggan Umum')
                                    ->orWhere(function ($q) {
                                        $q->where('nama', 'Umum')
                                            ->whereNull('member_id');
                                    });
                            })
                            ->first();

                        if (!$pelanggan) {
                            $pelanggan = Pelanggan::create([
                                'nama' => 'Umum',
                                'telepon' => null,
                                'alamat' => null,
                                'tanggal_lahir' => null,
                                'keterangan' => 'Pelanggan Umum',
                                'member_id' => null,
                                'is_member' => false,
                                'member_aktif' => false,
                                'member_since' => null,
                                'saldo_piutang' => 0,
                                'status_member' => null,
                            ]);
                        }  
                    }

                    // Hubungkan transaksi dengan pelanggan tersebut
                    $data['pelanggan_id'] = $pelanggan->id;
                }

    // Tentukan diskon member dari backend
    $diskonMemberPercent = 0;
                if (!empty($data['pelanggan_id'])) {
                    $pelanggan = Pelanggan::find($data['pelanggan_id']);

                    if (!$pelanggan) {
                        throw ValidationException::withMessages([
                            'pelanggan_id' => 'Data pelanggan tidak ditemukan.',
                        ]);
                    }

                    if ($pelanggan->is_member && ($pelanggan->member_aktif ?? false)) {
                        $memberDiscount = $pelanggan->custom_discount_percentage !== null
                            ? (float) $pelanggan->custom_discount_percentage
                            : (float) config('pos.diskon_member', 10);
                        $diskonMemberPercent = min(50, $memberDiscount);
                    }

                    // Piutang hanya boleh untuk member yang aktif
                    if ($data['metode_pembayaran'] === 'piutang') {
                        if (!$pelanggan->is_member || !($pelanggan->member_aktif ?? false)) {
                            throw ValidationException::withMessages([
                                'metode_pembayaran' => 'Metode pembayaran Piutang hanya dapat digunakan oleh Member.',
                            ]);
                        }
                    }
                }
                
                $penjualan = Penjualan::create([
                    'user_id' => $request->user()->id,
                    'pelanggan_id' => $data['pelanggan_id'] ?? null,
                    'tanggal' => $data['tanggal'],
                    'no_faktur' => $data['no_faktur'],
                    'total' => 0,
                    'metode_pembayaran' => $data['metode_pembayaran'],
                    'due_date' => $finalDueDate,
                    'jenis_transaksi' => $data['jenis_transaksi'],
                    'nama_dokter' => $data['nama_dokter'] ?? null,
                    'id_dokter' => $data['id_dokter'] ?? null,
                    'alamat_lembaga' => $data['alamat_lembaga'] ?? null,
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
                if ($data['metode_pembayaran'] === 'piutang') {
                    $pelanggan->increment('saldo_piutang', $totalFaktur);
                }
                \App\Models\ActivityLog::log(
                    'Transaksi Penjualan',
                    "Invoice: {$penjualan->no_faktur}, Total: Rp " . number_format($totalFaktur, 2),
                    \App\Models\ActivityLog::CATEGORY_PENJUALAN
                );

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

    public function detail(Penjualan $penjualan)
    {
        $penjualan->load([
            'user',
            'pelanggan',
            'detail.detailPenerimaan.barang',
            'discountUsages',
        ]);

        // Hitung total persentase diskon member yang digunakan pada transaksi ini
        $memberDiscountPercent = null;
        $memberDiscountUsages = $penjualan->discountUsages
            ->where('jenis', 'member');

        if ($memberDiscountUsages->isNotEmpty()) {
            // Ambil persentase member diskon dari usage pertama (semua item seharusnya sama)
            $memberDiscountPercent = (float) $memberDiscountUsages->first()->persentase;
        }

        $penjualanData = $penjualan->toArray();
        $penjualanData['member_discount_percent_used'] = $memberDiscountPercent;
        $penjualanData['due_date_formatted'] = $penjualan->due_date ? $penjualan->due_date->format('d M Y') : null;

        return response()->json([
            'penjualan' => $penjualanData,
        ]);
    }

    public function cetakLaporan(Request $request)
    {
        $query = Penjualan::with([
            'user',
            'pelanggan',
            'detail',
        ]);

        // Filter tanggal awal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $request->tanggal_awal
            );
        }

        // Filter tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $request->tanggal_akhir
            );
        }

        // Filter nomor invoice jika diisi
        if ($request->filled('cari')) {
            $query->where(
                'no_faktur',
                'like',
                '%' . $request->cari . '%'
            );
        }

        // Ambil semua data sesuai filter, tanpa pagination
        $penjualans = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_faktur', 'desc')
            ->get();

        // Hitung total diskon seluruh transaksi
        $totalDiskon = $penjualans->sum(function ($penjualan) {
            return $penjualan->detail->sum('diskon');
        });

        // Hitung total transaksi
        $totalTransaksi = $penjualans->sum('total');

        return view('penjualan.cetak', compact(
            'penjualans',
            'totalDiskon',
            'totalTransaksi'
        ));
    }
}
