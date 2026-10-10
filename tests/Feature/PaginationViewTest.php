<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    public function test_empty_pagination_keeps_the_shared_typography_and_indonesian_range(): void
    {
        $paginator = new LengthAwarePaginator([], 0, 15, 1);
        $html = $paginator->links('vendor.pagination.apotek')->render();

        $this->assertStringContainsString('Menampilkan', $html);
        $this->assertStringContainsString('<span class="font-medium">0 – 0</span>', $html);
        $this->assertStringContainsString('<span class="font-medium">0</span>', $html);
        $this->assertStringContainsString('text-sm leading-5 text-slate-500', $html);
        $this->assertStringNotContainsString('Showing', $html);
        $this->assertStringNotContainsString('results', $html);
    }

    public function test_available_results_show_the_dynamic_range_and_preserve_filters_in_page_links(): void
    {
        $paginator = new LengthAwarePaginator(
            range(1, 15),
            16,
            15,
            1,
            ['path' => '/barang', 'query' => ['cari' => 'obat']]
        );
        $html = $paginator->links('vendor.pagination.apotek')->render();

        $this->assertStringContainsString('<span class="font-medium">1 – 15</span>', $html);
        $this->assertStringContainsString('<span class="font-medium">16</span>', $html);
        $this->assertStringContainsString('cari=obat&amp;page=2', $html);
        $this->assertStringContainsString('aria-label="Berikutnya"', $html);
        $this->assertStringContainsString('aria-disabled="true" aria-label="Sebelumnya"', $html);
    }

    public function test_last_page_shows_its_actual_range_and_previous_navigation(): void
    {
        $paginator = new LengthAwarePaginator(
            [16],
            16,
            15,
            2,
            ['path' => '/barang', 'query' => ['cari' => 'obat']]
        );
        $html = $paginator->links('vendor.pagination.apotek')->render();

        $this->assertStringContainsString('<span class="font-medium">16 – 16</span>', $html);
        $this->assertStringContainsString('aria-label="Sebelumnya"', $html);
        $this->assertStringContainsString('aria-disabled="true" aria-label="Berikutnya"', $html);
        $this->assertStringContainsString('cari=obat&amp;page=1', $html);
    }

    public function test_single_result_uses_the_same_indonesian_range_and_disabled_navigation(): void
    {
        $paginator = new LengthAwarePaginator([1], 1, 15, 1);
        $html = $paginator->links('vendor.pagination.apotek')->render();

        $this->assertStringContainsString('<span class="font-medium">1 – 1</span>', $html);
        $this->assertStringContainsString('aria-disabled="true" aria-label="Sebelumnya"', $html);
        $this->assertStringContainsString('aria-disabled="true" aria-label="Berikutnya"', $html);
    }
}
