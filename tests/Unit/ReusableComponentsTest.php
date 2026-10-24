<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;

class ReusableComponentsTest extends TestCase
{
    /**
     * Uji komponen <x-page-header>
     */
    public function test_page_header_renders_title_subtitle_and_back_url(): void
    {
        $view = $this->blade(
            '<x-page-header title="Daftar Obat" subtitle="Kelola master obat" back-url="/dashboard">
                <button id="btn-tambah">Tambah</button>
            </x-page-header>'
        );

        $view->assertSee('Daftar Obat');
        $view->assertSee('Kelola master obat');
        $view->assertSee('/dashboard');
        $view->assertSee('btn-tambah');
    }

    /**
     * Uji komponen <x-pagination>
     */
    public function test_pagination_wrapper_renders_when_pages_exist(): void
    {
        $paginator = new LengthAwarePaginator([1, 2, 3], 30, 3, 1, ['path' => '/kategori']);
        $view = $this->blade('<x-pagination :paginator="$paginator" />', ['paginator' => $paginator]);

        $view->assertSee('pagination-container');
        $view->assertSee('Menampilkan');
    }

    public function test_pagination_wrapper_hides_when_single_page(): void
    {
        $paginator = new LengthAwarePaginator([1], 1, 15, 1, ['path' => '/kategori']);
        $view = $this->blade('<x-pagination :paginator="$paginator" />', ['paginator' => $paginator]);

        $view->assertDontSee('pagination-container');
    }

    /**
     * Uji komponen <x-status-badge>
     */
    public function test_status_badge_renders_correct_classes_and_labels(): void
    {
        // Badge Pembayaran
        $viewLunas = $this->blade('<x-status-badge type="pembayaran" status="lunas" />');
        $viewLunas->assertSee('Lunas');
        $viewLunas->assertSee('badge-success');

        $viewHutang = $this->blade('<x-status-badge type="pembayaran" status="belum_lunas" />');
        $viewHutang->assertSee('Belum Lunas');
        $viewHutang->assertSee('badge-warning');

        // Badge Stok
        $viewAman = $this->blade('<x-status-badge type="stok" status="aman" />');
        $viewAman->assertSee('Aman');
        $viewAman->assertSee('badge-success');

        $viewHabis = $this->blade('<x-status-badge type="stok" status="habis" />');
        $viewHabis->assertSee('Habis');
        $viewHabis->assertSee('badge-danger');
    }

    /**
     * Uji komponen <x-modal-confirm-delete>
     */
    public function test_modal_confirm_delete_renders_structure(): void
    {
        $view = $this->blade('<x-modal-confirm-delete />');

        $view->assertSee('modal-confirm-delete');
        $view->assertSee('Konfirmasi Hapus');
        $view->assertSee('confirmDelete');
        $view->assertSee('data-confirm-delete');
    }

    /**
     * Uji blade directives @rupiah dan @tanggalId
     */
    public function test_rupiah_and_tanggal_directives(): void
    {
        $viewRupiah = $this->blade('@rupiah(150000)');
        $viewRupiah->assertSee('Rp 150.000');

        $viewTanggal = $this->blade("@tanggalId('2026-10-24')");
        $viewTanggal->assertSee('24 Okt 2026');
    }

    /**
     * Uji komponen <x-print-actions>
     */
    public function test_print_actions_renders_excel_and_print_buttons(): void
    {
        $view = $this->blade('<x-print-actions excel-url="/laporan/export" print-label="Cetak Faktur" />');

        $view->assertSee('Ekspor Excel');
        $view->assertSee('/laporan/export');
        $view->assertSee('Cetak Faktur');
        $view->assertSee('print:hidden');
    }
}
