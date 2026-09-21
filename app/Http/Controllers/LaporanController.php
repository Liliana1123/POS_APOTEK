<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenjualan;
use App\Models\Rusak;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Laporan stok: total stok per barang + alert stok menipis & mendekati expired
    public function stok(Request $request)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $barangs = Barang::with('kategori')
            ->withSum(['detailPenerimaan' => function ($query) {
                $query->where('aktif', true);
            }], 'stok')
            ->where('aktif', true)
            ->when($request->filled('nama'), function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            })
            ->when($request->filled('kategori_id'), function ($query) use ($request) {
                $query->where('kategori_id', $request->kategori_id);
            })

            ->when($request->filled('status_stok'), function ($query) use ($request) {
                if ($request->status_stok === 'habis') {
                    $query->whereDoesntHave('detailPenerimaan', function ($q) {
                        $q->where('aktif', true)
                        ->where('stok', '>', 0);
                    });
                }

                if ($request->status_stok === 'menipis') {
                    $query->whereHas('detailPenerimaan', function ($q) {
                        $q->where('aktif', true);
                    })
                    ->whereRaw(
                        '(SELECT COALESCE(SUM(dp.stok), 0)
                        FROM detail_penerimaans dp
                        WHERE dp.barang_id = barangs.id
                        AND dp.aktif = 1
                        AND dp.deleted_at IS NULL) > 0'
                    )
                    ->whereRaw(
                        '(SELECT COALESCE(SUM(dp.stok), 0)
                        FROM detail_penerimaans dp
                        WHERE dp.barang_id = barangs.id
                        AND dp.aktif = 1
                        AND dp.deleted_at IS NULL) <= stok_minimum'
                    );
                }

                if ($request->status_stok === 'aman') {
                    $query->whereRaw(
                        '(SELECT COALESCE(SUM(dp.stok), 0)
                        FROM detail_penerimaans dp
                        WHERE dp.barang_id = barangs.id
                        AND dp.aktif = 1
                        AND dp.deleted_at IS NULL) > stok_minimum'
                    );
                }
            })

            ->orderBy('nama')
            ->get();

        $stokPerBatch = DetailPenerimaan::with(['barang.kategori'])
            ->withSum('detailPenjualan as stok_terjual', 'jumlah')
            ->withSum('rusak as stok_rusak', 'jumlah')
            ->when($request->status_stok !== 'habis', function ($query) {
                $query->where('aktif', true)
                    ->where('stok', '>', 0);
            })
            ->when($request->filled('nama'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama . '%');
                });
            })

            ->when($request->filled('kategori_id'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('kategori_id', $request->kategori_id);
                });
            })

            ->when($request->filled('status_stok'), function ($query) use ($request) {

                if ($request->status_stok === 'habis') {
                    $query->where('stok', 0);
                }

                if ($request->status_stok === 'menipis') {
                    $query->where('stok', '>', 0)
                        ->whereHas('barang', function ($q) {
                            $q->whereColumn('detail_penerimaans.stok', '<=', 'stok_minimum');
                        });
                }

                if ($request->status_stok === 'aman') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereColumn('detail_penerimaans.stok', '>', 'stok_minimum');
                    });
                }
            })

            ->when($request->filled('status_expired'), function ($query) use ($request) {
                $today = now()->startOfDay();

                if ($request->status_expired === 'kadaluarsa') {
                    $query->whereDate('expired_date', '<=', $today);
                }

                if ($request->status_expired === '1_bulan') {
                    $query->whereDate('expired_date', '>', $today)
                        ->whereDate('expired_date', '<=', $today->copy()->addMonth());
                }

                if ($request->status_expired === '3_bulan') {
                    $query->whereDate('expired_date', '>', $today->copy()->addMonth())
                        ->whereDate('expired_date', '<=', $today->copy()->addMonths(3));
                }

                if ($request->status_expired === 'normal') {
                    $query->whereDate('expired_date', '>', $today->copy()->addMonths(3));
                }

                if ($request->status_expired === 'tidak_ada') {
                    $query->whereNull('expired_date');
                }
            })

            ->orderBy(
            Barang::select('nama')
                ->whereColumn('barangs.id', 'detail_penerimaans.barang_id')
            )
            ->orderBy('expired_date')
            ->get();

        // Ringkasan metrik stok sinkron dari data transaksi riil
        $totalStokAwal = $stokPerBatch->sum('jumlah');
        $totalStokTerjual = $stokPerBatch->sum(fn ($i) => (int) ($i->stok_terjual ?? 0));
        $totalStokRusak = $stokPerBatch->sum(fn ($i) => (int) ($i->stok_rusak ?? 0));
        $totalSisaStok = $totalStokAwal - $totalStokTerjual - $totalStokRusak;

        $mendekatiExpired = DetailPenerimaan::with(['barang.kategori'])
            ->withSum('detailPenjualan as stok_terjual', 'jumlah')
            ->withSum('rusak as stok_rusak', 'jumlah')
            ->when($request->filled('nama'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama . '%');
                });
            })
            ->when($request->filled('kategori_id'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('kategori_id', $request->kategori_id);
                });
            })
            ->mendekatiExpired(90)
            ->orderBy('expired_date')
            ->get();

        if ($request->query('export') === 'csv') {
            $headers = ['Nama Barang', 'Kategori', 'Stok Saat Ini', 'Stok Minimum', 'Status'];
            $data = [];
            foreach ($barangs as $b) {
                $stok = $b->stokTotal();
                $data[] = [
                    $b->nama,
                    $b->kategori->nama,
                    $stok,
                    $b->stok_minimum,
                    $stok <= $b->stok_minimum ? 'Menipis' : 'Aman'
                ];
            }
            return $this->exportCsv('laporan-stok-' . now()->format('Ymd') . '.csv', $headers, $data);
        }

        return view('laporan.stok', compact(
            'barangs',
            'stokPerBatch',
            'mendekatiExpired',
            'kategoris',
            'totalStokAwal',
            'totalStokTerjual',
            'totalStokRusak',
            'totalSisaStok'
        ));
    }

    // Laporan penerimaan barang dalam rentang tanggal
    public function penerimaan(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = DetailPenerimaan::with(['barang', 'penerimaan.supplier'])
            ->whereHas('penerimaan', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            });

        if ($request->filled('no_faktur')) {
            $query->whereHas('penerimaan', function ($q) use ($request) {
                $q->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
            });
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('penerimaan', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->filled('nama_barang')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama_barang . '%');
            });
        }

        $items = $query->orderByDesc('created_at')->get();

        $totalNilai = $items->sum(fn ($i) => $i->harga_beli * $i->jumlah);

        if ($request->query('export') === 'csv') {
            $headers = ['Tanggal', 'No. Faktur', 'Supplier', 'Barang', 'Jumlah', 'Harga Beli', 'Total'];
            $data = [];
            foreach ($items as $item) {
                $data[] = [
                    $item->penerimaan->tanggal->format('d M Y'),
                    $item->penerimaan->no_faktur,
                    $item->penerimaan->supplier->nama ?? '—',
                    $item->barang->nama ?? '—',
                    $item->jumlah,
                    $item->harga_beli,
                    $item->harga_beli * $item->jumlah
                ];
            }
            return $this->exportCsv('laporan-penerimaan-' . $dari . '-' . $sampai . '.csv', $headers, $data);
        }

        $suppliers = Supplier::orderBy('nama')->get();

        return view('laporan.penerimaan', compact('items', 'totalNilai', 'dari', 'sampai', 'suppliers'));
    }

    // Laporan penjualan barang dalam rentang tanggal
    public function penjualan(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = \App\Models\Penjualan::with(['pelanggan', 'detail'])
            ->whereBetween('tanggal', [$dari, $sampai]);

        // Filter: member / non-member
        if ($request->filled('status_pelanggan')) {
            $status = $request->status_pelanggan;
            if ($status === 'member') {
                $query->whereHas('pelanggan', function ($q) {
                    $q->where('is_member', true);
                });
            } elseif ($status === 'non-member') {
                $query->where(function ($q) {
                    $q->whereHas('pelanggan', function ($q2) {
                        $q2->where('is_member', false);
                    })->orWhereNull('pelanggan_id');
                });
            }
        }

        $penjualans = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        // Calculate stats
        $jumlahTransaksi = $penjualans->count();
        
        $totalDiskon = $penjualans->sum(function ($p) {
            return $p->detail->sum('diskon');
        });
        
        $totalPenjualanBersih = $penjualans->sum('total');
        $omzet = $totalPenjualanBersih + $totalDiskon; // total kotor

        $transaksiMember = $penjualans->filter(function ($p) {
            return $p->pelanggan && $p->pelanggan->is_member;
        })->count();
        
        $transaksiNonMember = $jumlahTransaksi - $transaksiMember;

        if ($request->query('export') === 'csv') {
            $headers = ['Tanggal', 'No. Faktur', 'Pelanggan', 'Status Pelanggan', 'Kasir', 'Subtotal Kotor', 'Diskon', 'Total'];
            $data = [];
            foreach ($penjualans as $p) {
                $subtotalKotor = $p->total + $p->detail->sum('diskon');
                $data[] = [
                    $p->tanggal->format('d M Y'),
                    $p->no_faktur,
                    $p->pelanggan->nama ?? 'Umum',
                    $p->pelanggan ? ($p->pelanggan->is_member ? 'Member' : 'Umum') : 'Umum',
                    $p->user->name,
                    $subtotalKotor,
                    $p->detail->sum('diskon'),
                    $p->total
                ];
            }
            return $this->exportCsv('laporan-penjualan-' . $dari . '-' . $sampai . '.csv', $headers, $data);
        }

        return view('laporan.penjualan', compact(
            'penjualans',
            'jumlahTransaksi',
            'omzet',
            'totalDiskon',
            'totalPenjualanBersih',
            'transaksiMember',
            'transaksiNonMember',
            'dari',
            'sampai'
        ));
    }

    // Laporan barang rusak dalam rentang tanggal
    public function rusak(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = Rusak::with('detailPenerimaan.barang')
            ->whereBetween('tanggal', [$dari, $sampai]);

        if ($request->filled('nama_barang')) {
            $query->whereHas('detailPenerimaan.barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama_barang . '%');
            });
        }

        if ($request->filled('no_batch')) {
            $query->whereHas('detailPenerimaan', function ($q) use ($request) {
                $q->where('no_batch', 'like', '%' . $request->no_batch . '%');
            });
        }

        $items = $query->orderByDesc('tanggal')->get();

        $totalKerugian = $items->sum(fn ($r) => $r->jumlah * ($r->detailPenerimaan->harga_beli ?? 0));

        if ($request->query('export') === 'csv') {
            $headers = ['Tanggal', 'Barang', 'No. Batch', 'Jumlah', 'Kerugian (Harga Beli)', 'Keterangan'];
            $data = [];
            foreach ($items as $item) {
                $data[] = [
                    $item->tanggal->format('d M Y'),
                    $item->detailPenerimaan->barang->nama ?? '—',
                    $item->detailPenerimaan->no_batch ?? '—',
                    $item->jumlah,
                    $item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0),
                    $item->keterangan ?? '-'
                ];
            }
            return $this->exportCsv('laporan-barang-rusak-' . $dari . '-' . $sampai . '.csv', $headers, $data);
        }

        return view('laporan.rusak', compact('items', 'totalKerugian', 'dari', 'sampai'));
    }

    // Laporan laba-rugi: pendapatan penjualan dikurangi harga pokok (harga beli) barang terjual
    public function labaRugi(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $items = DetailPenjualan::with(['detailPenerimaan.barang', 'penjualan'])
            ->whereHas('penjualan', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->get();

        $pendapatan = $items->sum('subtotal');
        $hpp = $items->sum(fn ($i) => $i->detailPenerimaan->harga_beli * $i->jumlah);
        $labaKotor = $pendapatan - $hpp;

        return view('laporan.laba_rugi', compact('pendapatan', 'hpp', 'labaKotor', 'dari', 'sampai'));
    }

    // Laporan penggunaan diskon (Fase 3)
    public function diskon(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = \App\Models\DiscountUsage::with(['penjualan.pelanggan', 'customDiscount'])
            ->whereHas('penjualan', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            });

        // Filter: jenis (member / custom)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter: promo_id
        if ($request->filled('promo_id')) {
            $query->where('custom_discount_id', $request->promo_id);
        }

        // Filter: status pelanggan (member / umum)
        if ($request->filled('status_pelanggan')) {
            if ($request->status_pelanggan === 'member') {
                $query->whereHas('penjualan.pelanggan', function ($q) {
                    $q->where('is_member', true);
                });
            } else if ($request->status_pelanggan === 'umum') {
                $query->where(function ($q) {
                    $q->whereHas('penjualan.pelanggan', function ($q2) {
                        $q2->where('is_member', false);
                    })->orWhereNull('penjualan.pelanggan_id');
                });
            }
        }

        $usages = $query->orderByDesc('created_at')->get();
        $totalNominal = $usages->sum('nominal');

        // Untuk dropdown filter promo
        $promos = \App\Models\CustomDiscount::orderBy('nama')->get();

        if ($request->query('export') === 'csv') {
            $headers = ['Tanggal', 'No. Faktur', 'Barang', 'Pelanggan', 'Jenis Diskon', 'Nama Promo', 'Persentase', 'Nominal'];
            $data = [];
            foreach ($usages as $usage) {
                $data[] = [
                    $usage->created_at->format('d M Y H:i'),
                    $usage->penjualan->no_faktur ?? '',
                    $usage->barang_nama,
                    $usage->penjualan->pelanggan->nama ?? 'Umum',
                    $usage->jenis,
                    $usage->custom_discount_nama ?? '-',
                    $usage->persentase,
                    $usage->nominal
                ];
            }
            return $this->exportCsv('laporan-diskon-' . $dari . '-' . $sampai . '.csv', $headers, $data);
        }

        return view('laporan.diskon', compact('usages', 'totalNominal', 'promos', 'dari', 'sampai'));
    }

    // Helper: export data ke format CSV native PHP stream
    private function exportCsv(string $filename, array $headers, array $data)
    {
        $callback = function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    // Helper: ambil rentang tanggal dari query string, default bulan berjalan
    private function rentangTanggal(Request $request): array
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->format('Y-m-d');

        return [$dari, $sampai];
    }
}
