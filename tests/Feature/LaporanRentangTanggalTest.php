<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenjualan;
use App\Models\DiscountUsage;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Rusak;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanRentangTanggalTest extends TestCase
{
    use RefreshDatabase;

    private function createReportData(int $currentMonthRows = 1): array
    {
        $user = User::create([
            'name' => 'Admin Laporan',
            'email' => 'admin-laporan@test.local',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);
        $supplier = Supplier::create([
            'nama' => 'Supplier Laporan',
            'telepon' => '021',
            'alamat' => 'Jakarta',
        ]);
        $kategori = Kategori::create(['nama' => 'Kategori Laporan']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create([
            'nama' => 'Pabrik Laporan',
            'telepon' => '021',
            'alamat' => 'Jakarta',
        ]);
        $barang = Barang::create([
            'nama' => 'Barang Laporan',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'butuh_resep' => false,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        $today = now();
        $previousMonth = $today->copy()->startOfMonth()->subDay();
        $previousMonthInvoice = null;

        for ($index = 0; $index <= $currentMonthRows; $index++) {
            $date = $index === $currentMonthRows
                ? $previousMonth
                : $today->copy()->startOfMonth()->addDays($index);
            $suffix = str_pad((string) $index, 3, '0', STR_PAD_LEFT);
            $invoice = 'REPORT-' . $suffix;

            $penerimaan = Penerimaan::create([
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
                'tanggal' => $date->toDateString(),
                'tanggal_faktur' => $date->toDateString(),
                'no_faktur' => $invoice,
                'lunas' => true,
            ]);
            $batch = DetailPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'barang_id' => $barang->id,
                'no_batch' => 'BATCH-' . $suffix,
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'expired_date' => $today->copy()->addYear()->toDateString(),
                'no_rak' => 'A-01',
                'jumlah' => 5,
                'stok' => 5,
                'aktif' => true,
            ]);
            Rusak::create([
                'detail_penerimaan_id' => $batch->id,
                'tanggal' => $date->toDateString(),
                'jumlah' => 1,
                'keterangan' => 'Rusak ' . $suffix,
            ]);
            $penjualan = Penjualan::create([
                'user_id' => $user->id,
                'tanggal' => $date->toDateString(),
                'no_faktur' => $invoice,
                'total' => 15000,
                'metode_pembayaran' => 'cash',
                'jenis_transaksi' => 'non_resep',
            ]);
            $detailPenjualan = DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'detail_penerimaan_id' => $batch->id,
                'harga_jual' => 15000,
                'jumlah' => 1,
                'diskon' => 1000,
                'subtotal' => 14000,
            ]);
            $usage = DiscountUsage::create([
                'penjualan_id' => $penjualan->id,
                'detail_penjualan_id' => $detailPenjualan->id,
                'barang_id' => $barang->id,
                'barang_nama' => 'Barang Laporan ' . $suffix,
                'jenis' => 'member',
                'persentase' => 5,
                'nominal' => 1000,
            ]);
            $usage->forceFill([
                'created_at' => $date->copy()->setTime(12, 0),
                'updated_at' => $date->copy()->setTime(12, 0),
            ])->save();

            if ($index === $currentMonthRows) {
                $previousMonthInvoice = $invoice;
            }
        }

        return [
            'user' => $user,
            'start' => $today->copy()->startOfMonth()->toDateString(),
            'end' => $today->copy()->endOfMonth()->toDateString(),
            'previousMonthInvoice' => $previousMonthInvoice,
        ];
    }

    public function test_all_five_reports_default_to_current_month_and_filter_by_date(): void
    {
        $data = $this->createReportData();
        $reportRoutes = [
            'laporan.stok',
            'laporan.penerimaan',
            'laporan.penjualan',
            'laporan.rusak',
            'laporan.diskon',
        ];

        foreach ($reportRoutes as $routeName) {
            $response = $this->actingAs($data['user'])->get(route($routeName));

            $response->assertOk()
                ->assertSee($data['start'])
                ->assertSee($data['end'])
                ->assertDontSee($data['previousMonthInvoice']);
        }

        foreach ($reportRoutes as $routeName) {
            $response = $this->actingAs($data['user'])->get(route($routeName, [
                'dari' => $data['start'],
                'sampai' => $data['end'],
            ]));

            $response->assertOk()
                ->assertDontSee($data['previousMonthInvoice']);
        }
    }

    public function test_all_five_reports_show_error_for_reversed_dates(): void
    {
        $data = $this->createReportData();
        $later = Carbon::parse($data['end'])->toDateString();
        $earlier = Carbon::parse($data['start'])->toDateString();

        foreach ([
            'laporan.stok',
            'laporan.penerimaan',
            'laporan.penjualan',
            'laporan.rusak',
            'laporan.diskon',
        ] as $routeName) {
            $this->actingAs($data['user'])
                ->get(route($routeName, ['dari' => $later, 'sampai' => $earlier]))
                ->assertOk()
                ->assertSee('Tanggal awal tidak boleh lebih besar daripada tanggal akhir.');
        }
    }

    public function test_all_five_reports_paginate_in_database_and_keep_date_filters(): void
    {
        $data = $this->createReportData(16);

        foreach ([
            'laporan.stok',
            'laporan.penerimaan',
            'laporan.penjualan',
            'laporan.rusak',
            'laporan.diskon',
        ] as $routeName) {
            $filteredUrl = route($routeName, [
                'dari' => $data['start'],
                'sampai' => $data['end'],
            ]);
            $firstPage = $this->actingAs($data['user'])->get($filteredUrl);

            $firstPage->assertOk()
                ->assertSee('Menampilkan 1–15')
                ->assertSee('page=2');

            $secondPage = $this->actingAs($data['user'])->get($filteredUrl . '&page=2');
            $secondPage->assertOk()
                ->assertSee('Menampilkan 16–16')
                ->assertSee('dari 16');
        }
    }
}
