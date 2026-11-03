<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenerimaanSyncPembayaranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Apotek',
            'email' => 'admin@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);
    }

    public function test_penerimaan_index_modal_renders_sync_to_payment_button(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penerimaan.index'));

        $response->assertOk();
        $content = $response->getContent();

        // Cek keberadaan tombol dan teksnya
        $this->assertStringContainsString('id="btn-sinkron-pembayaran-tambah"', $content);
        $this->assertStringContainsString('Sinkronkan ke Pembayaran', $content);

        // Pastikan styling outline biru dan rounded
        $this->assertStringContainsString('border-blue-500', $content);
        $this->assertStringContainsString('text-blue-600', $content);

        // Pastikan posisi tombol berada di bawah Total Tagihan dan sebelum Pembayaran Saat Penerimaan
        $posTotalTagihan = strpos($content, 'id="total-tagihan-tambah"');
        $posBtnSinkron = strpos($content, 'id="btn-sinkron-pembayaran-tambah"');
        $posPembayaran = strpos($content, 'id="pembayaran_pertama_tambah"');

        $this->assertNotFalse($posTotalTagihan);
        $this->assertNotFalse($posBtnSinkron);
        $this->assertNotFalse($posPembayaran);

        $this->assertTrue($posBtnSinkron > $posTotalTagihan, 'Tombol harus berada di bawah informasi Total Tagihan');
        $this->assertTrue($posBtnSinkron < $posPembayaran, 'Tombol harus berada sebelum kotak Pembayaran Saat Penerimaan');
    }

    public function test_penerimaan_create_page_renders_sync_to_payment_button(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penerimaan.create'));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('id="btn-sinkron-pembayaran"', $content);
        $this->assertStringContainsString('Sinkronkan ke Pembayaran', $content);

        $posTotalTagihan = strpos($content, 'id="total-tagihan"');
        $posBtnSinkron = strpos($content, 'id="btn-sinkron-pembayaran"');
        $posPembayaran = strpos($content, 'id="pembayaran_pertama"');

        $this->assertNotFalse($posTotalTagihan);
        $this->assertNotFalse($posBtnSinkron);
        $this->assertNotFalse($posPembayaran);

        $this->assertTrue($posBtnSinkron > $posTotalTagihan, 'Tombol harus berada di bawah informasi Total Tagihan');
        $this->assertTrue($posBtnSinkron < $posPembayaran, 'Tombol harus berada sebelum kotak Pembayaran Saat Penerimaan');
    }
}

