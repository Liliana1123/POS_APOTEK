<?php

namespace App\Services;

use App\Models\Barang;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\CSV\Options as CSVOptions;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class PenerimaanImportService
{
    private string $tempDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/temp_imports');
        if (!File::isDirectory($this->tempDir)) {
            File::makeDirectory($this->tempDir, 0755, true);
        }
    }

    /**
     * Unduh template resmi Excel (.xlsx) untuk import penerimaan obat.
     * File bersih/kosong hanya memuat header kolom tanpa data contoh/dummy.
     */
    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $tempFile = tempnam(sys_get_temp_dir(), 'tpl_import_') . '.xlsx';

            $options = new Options();
            $options->setColumnWidth(28, 1); // 1: Nama Barang
            $options->setColumnWidth(18, 2); // 2: Barcode
            $options->setColumnWidth(18, 3); // 3: Nomor Batch
            $options->setColumnWidth(16, 4); // 4: Expired Date
            $options->setColumnWidth(16, 5); // 5: Harga Beli
            $options->setColumnWidth(16, 6); // 6: Harga Jual
            $options->setColumnWidth(16, 7); // 7: Jumlah Dipesan
            $options->setColumnWidth(16, 8); // 8: Jumlah Diterima
            $options->setColumnWidth(14, 9); // 9: Nomor Rak
            $options->DEFAULT_ROW_HEIGHT = 24;

            $writer = new XLSXWriter($options);
            $writer->openToFile($tempFile);

            $border = new Border(
                new BorderPart(Border::TOP, Color::rgb(191, 219, 254), Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::BOTTOM, Color::rgb(147, 197, 253), Border::WIDTH_MEDIUM, Border::STYLE_SOLID),
                new BorderPart(Border::LEFT, Color::rgb(191, 219, 254), Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::RIGHT, Color::rgb(191, 219, 254), Border::WIDTH_THIN, Border::STYLE_SOLID),
            );

            $headerBaseStyle = (new Style())
                ->setFontBold()
                ->setFontSize(11)
                ->setFontName('Calibri')
                ->setFontColor(Color::rgb(30, 58, 138))
                ->setBackgroundColor(Color::rgb(219, 234, 254))
                ->setBorder($border)
                ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

            // 9 Kolom Header Sesuai Ketentuan Format Import
            $headerCells = [
                Cell::fromValue('Nama Barang', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::LEFT)),
                Cell::fromValue('Barcode', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::CENTER)),
                Cell::fromValue('Nomor Batch', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::CENTER)),
                Cell::fromValue('Expired Date', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::CENTER)),
                Cell::fromValue('Harga Beli', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::RIGHT)),
                Cell::fromValue('Harga Jual', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::RIGHT)),
                Cell::fromValue('Jumlah Dipesan', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::RIGHT)),
                Cell::fromValue('Jumlah Diterima', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::RIGHT)),
                Cell::fromValue('Nomor Rak', (clone $headerBaseStyle)->setCellAlignment(CellAlignment::CENTER)),
            ];

            // Tulis hanya baris 1 (Header), tanpa data obat/dummy sama sekali
            $writer->addRow(new Row($headerCells));
            $writer->close();

            // Format tanggal dan angka pada level metadata worksheet
            $zip = new ZipArchive();
            if ($zip->open($tempFile) === true) {
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $stylesXml = $zip->getFromName('xl/styles.xml');

                if ($sheetXml && $stylesXml) {
                    $numFmtsXml = '<numFmts count="1"><numFmt numFmtId="164" formatCode="yyyy-mm-dd"/></numFmts>';
                    $stylesXml = preg_replace('/<numFmts count="0"><\/numFmts>/', $numFmtsXml, $stylesXml);

                    // xf 4: Date (164, center)
                    // xf 5: Harga / Currency (3, right)
                    // xf 6: Jumlah / Integer (1, right)
                    // xf 7: Text (49, left)
                    $extraXfs = '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' .
                                '<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' .
                                '<xf numFmtId="1" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' .
                                '<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>';

                    $stylesXml = preg_replace('/<cellXfs count="(\d+)">/', '<cellXfs count="8">', $stylesXml);
                    $stylesXml = str_replace('</cellXfs>', $extraXfs . '</cellXfs>', $stylesXml);

                    $zip->addFromString('xl/styles.xml', $stylesXml);

                    // Hubungkan style format ke masing-masing kolom
                    // Col 1, 2, 3, 9 -> Text (style 7)
                    // Col 4 -> Date (style 4)
                    // Col 5, 6 -> Number (style 5)
                    // Col 7, 8 -> Integer (style 6)
                    $colStyles = [
                        1 => 7,
                        2 => 7,
                        3 => 7,
                        4 => 4,
                        5 => 5,
                        6 => 5,
                        7 => 6,
                        8 => 6,
                        9 => 7,
                    ];

                    foreach ($colStyles as $colIdx => $sId) {
                        $sheetXml = preg_replace(
                            '/<col min="' . $colIdx . '" max="' . $colIdx . '" width="([^"]+)" customWidth="true"\/>/',
                            '<col min="' . $colIdx . '" max="' . $colIdx . '" width="$1" customWidth="true" style="' . $sId . '"/>',
                            $sheetXml
                        );
                    }

                    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
                }
                $zip->close();
            }

            readfile($tempFile);
            @unlink($tempFile);
        }, 'template-import-penerimaan-obat.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Membaca file Excel/CSV, mengekstrak header kolom, baris sampel, dan menyimpan ke cache sementara.
     */
    public function parseUploadedFile(UploadedFile $file): array
    {
        $this->cleanupOldTempFiles();

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            throw new Exception('Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).');
        }

        $realPath = $file->getRealPath();
        $rawRows = $this->readRowsFromFile($realPath, $ext);

        if (empty($rawRows)) {
            throw new Exception('File kosong atau tidak memiliki data yang dapat dibaca.');
        }

        // Cari baris pertama yang tidak kosong sebagai Header
        $headerIndex = -1;
        $headers = [];
        foreach ($rawRows as $idx => $r) {
            $filtered = array_filter($r, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (!empty($filtered)) {
                $headerIndex = $idx;
                $headers = array_values($r);
                break;
            }
        }

        if ($headerIndex === -1 || empty($headers)) {
            throw new Exception('Tidak dapat menemukan baris header pada file.');
        }

        // Hapus UTF-8 BOM pada kolom pertama jika ada & bersihkan spasi
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
        $headers = array_map(function ($h, $i) {
            $cleaned = trim((string) $h);
            return $cleaned !== '' ? $cleaned : 'Kolom ' . ($i + 1);
        }, $headers, array_keys($headers));

        // Kumpulkan data baris setelah header
        $dataRows = [];
        for ($i = $headerIndex + 1; $i < count($rawRows); $i++) {
            $row = $rawRows[$i];
            // Lewati baris yang benar-benar kosong
            $nonEmpty = array_filter($row, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            // Normalisasi panjang kolom agar sama dengan header
            $normalizedRow = [];
            foreach ($headers as $hIdx => $hName) {
                $normalizedRow[$hName] = $row[$hIdx] ?? null;
            }
            $dataRows[] = $normalizedRow;
        }

        if (empty($dataRows)) {
            throw new Exception('File hanya berisi baris header dan tidak memiliki data obat.');
        }

        // Rekomendasi auto-mapping
        $suggestedMapping = $this->detectSuggestedMapping($headers);

        // Simpan data rows ke file JSON sementara
        $cacheId = Str::random(32);
        $cachePath = $this->getCacheFilePath($cacheId);
        File::put($cachePath, json_encode([
            'headers' => $headers,
            'rows' => $dataRows,
            'file_name' => $file->getClientOriginalName(),
            'created_at' => time(),
        ]));

        return [
            'success' => true,
            'file_cache_id' => $cacheId,
            'file_name' => $file->getClientOriginalName(),
            'total_rows' => count($dataRows),
            'headers' => $headers,
            'suggested_mapping' => $suggestedMapping,
            'sample_rows' => array_slice($dataRows, 0, 3),
        ];
    }

    /**
     * Memvalidasi dan mencocokkan data baris dengan Data Master Barang.
     */
    public function validateAndMatch(string $cacheId, array $mapping, ?string $tanggalPenerimaan = null): array
    {
        $cachePath = $this->getCacheFilePath($cacheId);
        if (!File::exists($cachePath)) {
            throw new Exception('Sesi import telah kedaluwarsa. Silakan unggah kembali file Anda.');
        }

        $cachedData = json_decode(File::get($cachePath), true);
        if (!$cachedData || empty($cachedData['rows'])) {
            throw new Exception('Data file tidak valid atau rusak.');
        }

        $headers = $cachedData['headers'] ?? [];
        $rows = $cachedData['rows'] ?? [];

        // Validasi pemetaan wajib: minimal salah satu antara nama_barang atau barcode harus terpetakan
        $hasBarangMapping = !empty($mapping['nama_barang']) || !empty($mapping['barcode']);
        if (!$hasBarangMapping) {
            throw new Exception('Kolom "Nama Barang" atau "Barcode" wajib dipetakan untuk mengidentifikasi obat.');
        }
        if (empty($mapping['no_batch'])) {
            throw new Exception('Kolom "No. Batch" wajib dipetakan.');
        }
        if (empty($mapping['expired_date'])) {
            throw new Exception('Kolom "Expired Date" wajib dipetakan.');
        }
        if (empty($mapping['harga_beli'])) {
            throw new Exception('Kolom "Harga Beli" wajib dipetakan.');
        }
        if (empty($mapping['jumlah_diterima']) && empty($mapping['jumlah_dipesan'])) {
            throw new Exception('Kolom "Jumlah Diterima" atau "Jumlah Dipesan" wajib dipetakan.');
        }

        // Muat Data Master Barang aktif untuk pencocokan cepat
        $masterBarangs = Barang::with(['satuan', 'pabrik'])
            ->where('aktif', true)
            ->get();

        $barcodeMap = [];
        $nameMap = [];

        foreach ($masterBarangs as $b) {
            if (!empty($b->barcode)) {
                $cleanBc = strtolower(trim((string) $b->barcode));
                $barcodeMap[$cleanBc] = $b;
            }
            $cleanName = $this->normalizeString($b->nama);
            if ($cleanName !== '') {
                // Simpan jika belum ada (utamakan kecocokan pertama)
                if (!isset($nameMap[$cleanName])) {
                    $nameMap[$cleanName] = $b;
                }
            }
        }

        $validatedRows = [];
        $seenDuplicates = [];

        $totalValid = 0;
        $totalWarning = 0;
        $totalInvalid = 0;

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $rowErrors = [];
            $rowWarnings = [];

            // Ekstraksi nilai berdasarkan pemetaan kolom
            $rawNama = !empty($mapping['nama_barang']) ? ($row[$mapping['nama_barang']] ?? null) : null;
            $rawBarcode = !empty($mapping['barcode']) ? ($row[$mapping['barcode']] ?? null) : null;
            $rawBatch = !empty($mapping['no_batch']) ? ($row[$mapping['no_batch']] ?? null) : null;
            $rawExpired = !empty($mapping['expired_date']) ? ($row[$mapping['expired_date']] ?? null) : null;
            $rawHargaBeli = !empty($mapping['harga_beli']) ? ($row[$mapping['harga_beli']] ?? null) : null;
            $rawHargaJual = !empty($mapping['harga_jual']) ? ($row[$mapping['harga_jual']] ?? null) : null;
            $rawJumlahDiterima = !empty($mapping['jumlah_diterima']) ? ($row[$mapping['jumlah_diterima']] ?? null) : null;
            $rawJumlahDipesan = !empty($mapping['jumlah_dipesan']) ? ($row[$mapping['jumlah_dipesan']] ?? null) : null;
            $rawNoRak = !empty($mapping['no_rak']) ? ($row[$mapping['no_rak']] ?? null) : null;

            // 1. Pencocokan Obat ke Master
            $matchedBarang = null;

            // Coba lewat Barcode terlebih dahulu
            if (!empty($rawBarcode)) {
                $cleanBc = strtolower(trim((string) $rawBarcode));
                if (isset($barcodeMap[$cleanBc])) {
                    $matchedBarang = $barcodeMap[$cleanBc];
                }
            }

            // Jika belum cocok, coba lewat Nama Barang
            if (!$matchedBarang && !empty($rawNama)) {
                $cleanNama = $this->normalizeString((string) $rawNama);
                if (isset($nameMap[$cleanNama])) {
                    $matchedBarang = $nameMap[$cleanNama];
                }
            }

            if (!$matchedBarang) {
                $identitas = trim(($rawNama ? "Nama: {$rawNama}" : '') . ($rawBarcode ? " Barcode: {$rawBarcode}" : ''));
                $rowErrors[] = "Obat ({$identitas}) tidak ditemukan di Data Master Barang.";
            }

            // 2. Validasi Nomor Batch
            $batchVal = trim((string) $rawBatch);
            if ($batchVal === '') {
                $rowErrors[] = 'Nomor batch tidak boleh kosong.';
            }

            // 3. Validasi & Parsing Expired Date
            $parsedDate = $this->parseDate($rawExpired);
            if (!$parsedDate) {
                $rowErrors[] = 'Format tanggal expired tidak valid (' . ($rawExpired ?? 'kosong') . '). Gunakan format YYYY-MM-DD atau DD/MM/YYYY.';
            } else {
                if ($tanggalPenerimaan && $parsedDate < $tanggalPenerimaan) {
                    $rowWarnings[] = 'Tanggal expired (' . $parsedDate . ') sebelum tanggal penerimaan (' . $tanggalPenerimaan . ').';
                }
            }

            // 4. Validasi Harga Beli
            $hargaBeliVal = $this->parseNumber($rawHargaBeli);
            if ($hargaBeliVal === null || $hargaBeliVal < 0) {
                $rowErrors[] = 'Harga beli harus berupa angka positif.';
            }

            // 5. Validasi Jumlah Diterima & Jumlah Dipesan
            $jumlahDiterimaVal = $this->parseInteger($rawJumlahDiterima);
            $jumlahDipesanVal = $this->parseInteger($rawJumlahDipesan);

            // Jika jumlah dipesan kosong tapi diterima ada, samakan
            if ($jumlahDipesanVal === null && $jumlahDiterimaVal !== null) {
                $jumlahDipesanVal = $jumlahDiterimaVal;
            } elseif ($jumlahDiterimaVal === null && $jumlahDipesanVal !== null) {
                $jumlahDiterimaVal = $jumlahDipesanVal;
            }

            if ($jumlahDiterimaVal === null || $jumlahDiterimaVal < 0) {
                $rowErrors[] = 'Jumlah diterima harus berupa angka bulat (>= 0).';
            }
            if ($jumlahDipesanVal === null || $jumlahDipesanVal < 0) {
                $rowErrors[] = 'Jumlah dipesan harus berupa angka bulat (>= 0).';
            }
            if ($jumlahDiterimaVal !== null && $jumlahDipesanVal !== null && $jumlahDiterimaVal > $jumlahDipesanVal) {
                $rowWarnings[] = 'Jumlah diterima (' . $jumlahDiterimaVal . ') melebihi jumlah dipesan (' . $jumlahDipesanVal . ').';
            }

            // 6. Validasi Harga Jual (Opsional dalam file)
            $hargaJualVal = $this->parseNumber($rawHargaJual);
            if ($hargaJualVal === null && $matchedBarang && !empty($matchedBarang->harga_jual)) {
                $hargaJualVal = (float) $matchedBarang->harga_jual;
                $rowWarnings[] = 'Harga jual otomatis diambil dari Data Master (Rp ' . number_format($hargaJualVal, 0, ',', '.') . ').';
            }

            if ($hargaJualVal !== null) {
                if ($hargaBeliVal !== null && $hargaJualVal < $hargaBeliVal) {
                    $rowErrors[] = 'Harga jual tidak boleh lebih kecil dari harga beli.';
                }
            } else {
                $rowWarnings[] = 'Harga jual belum diisi (dapat dilengkapi di form).';
            }

            // 7. Nomor Rak
            $noRakVal = trim((string) $rawNoRak);
            if ($noRakVal === '') {
                $noRakVal = 'A-01'; // Nilai default rak yang wajar
            }

            // 8. Cek Duplikasi Kombinasi (Barang + Batch) dalam file
            if ($matchedBarang && $batchVal !== '') {
                $dupKey = $matchedBarang->id . '||' . strtolower($batchVal);
                if (isset($seenDuplicates[$dupKey])) {
                    $rowWarnings[] = "Duplikat batch: Obat dan nomor batch '{$batchVal}' muncul lebih dari 1 kali dalam file.";
                } else {
                    $seenDuplicates[$dupKey] = true;
                }
            }

            // Tentukan status baris
            if (!empty($rowErrors)) {
                $status = 'invalid';
                $statusLabel = 'Tidak Valid';
                $totalInvalid++;
            } elseif (!empty($rowWarnings)) {
                $status = 'warning';
                $statusLabel = 'Perlu Dilengkapi';
                $totalWarning++;
            } else {
                $status = 'valid';
                $statusLabel = 'Valid';
                $totalValid++;
            }

            $validatedRows[] = [
                'row_num' => $rowNum,
                'status' => $status,
                'status_label' => $statusLabel,
                'can_import' => in_array($status, ['valid', 'warning']),
                'barang_id' => $matchedBarang->id ?? null,
                'nama_barang' => $matchedBarang->nama ?? (trim((string) $rawNama) ?: '—'),
                'barcode' => $matchedBarang->barcode ?? (trim((string) $rawBarcode) ?: '—'),
                'satuan_nama' => $matchedBarang->satuan->nama ?? '—',
                'pabrik_nama' => $matchedBarang->pabrik->nama ?? '',
                'no_batch' => $batchVal,
                'expired_date' => $parsedDate ?? (string) $rawExpired,
                'harga_beli' => $hargaBeliVal !== null ? $hargaBeliVal : 0,
                'harga_jual' => $hargaJualVal,
                'jumlah_dipesan' => $jumlahDipesanVal ?? 1,
                'jumlah_diterima' => $jumlahDiterimaVal ?? 1,
                'no_rak' => $noRakVal,
                'errors' => $rowErrors,
                'warnings' => $rowWarnings,
            ];
        }

        return [
            'success' => true,
            'summary' => [
                'total_rows' => count($validatedRows),
                'valid_count' => $totalValid,
                'warning_count' => $totalWarning,
                'invalid_count' => $totalInvalid,
                'ready_to_import_count' => ($totalValid + $totalWarning),
            ],
            'rows' => $validatedRows,
        ];
    }

    /**
     * Membaca array baris dari file XLSX/XLS/CSV menggunakan OpenSpout atau fallback.
     */
    private function readRowsFromFile(string $filePath, string $ext): array
    {
        $rows = [];

        if ($ext === 'csv') {
            $firstLine = fgets(fopen($filePath, 'r'));
            $options = new CSVOptions();
            if ($firstLine && strpos($firstLine, ';') !== false && substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $options->FIELD_DELIMITER = ';';
            }
            $reader = new CSVReader($options);
            $reader->open($filePath);
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $this->extractRowCells($row);
                }
                break;
            }
            $reader->close();
            return $rows;
        }

        // Coba sebagai XLSX terlebih dahulu
        try {
            $reader = new XLSXReader();
            $reader->open($filePath);
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $this->extractRowCells($row);
                }
                break;
            }
            $reader->close();
            return $rows;
        } catch (Exception $e) {
            // Jika format .xls bukan XLSX valid (misal CSV atau HTML table yang disimpan dengan ekstensi .xls)
            if ($ext === 'xls') {
                $contentHead = file_get_contents($filePath, false, null, 0, 1024);
                if (stripos($contentHead, '<table') !== false || stripos($contentHead, '<html') !== false) {
                    return $this->parseHtmlTableRows($filePath);
                }

                // Coba fallback pembaca CSV
                try {
                    $options = new CSVOptions();
                    $reader = new CSVReader($options);
                    $reader->open($filePath);
                    foreach ($reader->getSheetIterator() as $sheet) {
                        foreach ($sheet->getRowIterator() as $row) {
                            $rows[] = $this->extractRowCells($row);
                        }
                        break;
                    }
                    $reader->close();
                    if (!empty($rows)) {
                        return $rows;
                    }
                } catch (Exception) {
                    // Abaikan dan lempar pesan ramah
                }

                throw new Exception('File Excel dengan format biner lama (.xls) tidak dapat dibaca langsung. Silakan buka dan simpan ulang file sebagai format .xlsx atau .csv di Excel/LibreOffice, lalu coba lagi.');
            }

            throw new Exception('Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    private function extractRowCells(Row $row): array
    {
        $cells = $row->getCells();
        $res = [];
        foreach ($cells as $c) {
            $val = $c->getValue();
            if ($val instanceof DateTimeInterface) {
                $res[] = $val->format('Y-m-d');
            } else {
                $res[] = is_string($val) ? trim($val) : $val;
            }
        }
        return $res;
    }

    private function parseHtmlTableRows(string $filePath): array
    {
        $html = file_get_contents($filePath);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $rows = [];
        $trList = $dom->getElementsByTagName('tr');
        foreach ($trList as $tr) {
            $cells = [];
            foreach ($tr->childNodes as $child) {
                if (in_array(strtolower($child->nodeName), ['td', 'th'])) {
                    $cells[] = trim($child->textContent);
                }
            }
            if (!empty($cells)) {
                $rows[] = $cells;
            }
        }
        return $rows;
    }

    /**
     * Deteksi otomatis pemetaan kolom berdasarkan kemiripan nama header.
     */
    private function detectSuggestedMapping(array $headers): array
    {
        $mapping = [
            'nama_barang' => '',
            'barcode' => '',
            'no_batch' => '',
            'expired_date' => '',
            'harga_beli' => '',
            'harga_jual' => '',
            'jumlah_diterima' => '',
            'jumlah_dipesan' => '',
            'no_rak' => '',
        ];

        $synonyms = [
            'nama_barang' => ['nama_barang', 'namabarang', 'nama_produk', 'namaproduk', 'produk', 'item', 'item_name', 'product_name', 'nama', 'nama_obat', 'obat', 'deskripsi', 'description'],
            'barcode' => ['barcode', 'kode_barcode', 'kodebarcode', 'kode_barang', 'kodebarang', 'kode', 'sku', 'ean', 'upc'],
            'no_batch' => ['no_batch', 'nobatch', 'batch', 'nomor_batch', 'nomorbatch', 'batch_no', 'batchno', 'lot', 'no_lot', 'lot_no', 'lotno', 'batchnumber'],
            'expired_date' => ['expired_date', 'expireddate', 'expired', 'exp_date', 'expdate', 'ed', 'tgl_expired', 'tglexpired', 'kadaluarsa', 'tgl_kadaluarsa', 'tglkadaluarsa', 'expiry', 'exp', 'expiry_date'],
            'harga_beli' => ['harga_beli', 'hargabeli', 'h_beli', 'hbeli', 'beli', 'harga', 'price', 'cost', 'hna', 'hpp', 'harga_pokok', 'buy_price', 'unit_price', 'hargapokok'],
            'harga_jual' => ['harga_jual', 'hargajual', 'h_jual', 'hjual', 'jual', 'selling_price', 'het', 'hja', 'harga_satuan', 'hargasatuan'],
            'jumlah_diterima' => ['jumlah_diterima', 'jumlahditerima', 'qty_terima', 'qtyterima', 'terima', 'diterima', 'received_qty', 'receivedqty', 'jml_terima', 'jmlterima', 'qty', 'jumlah', 'realisasi', 'masuk'],
            'jumlah_dipesan' => ['jumlah_dipesan', 'jumlahdipesan', 'qty_pesan', 'qtypesan', 'pesan', 'dipesan', 'order_qty', 'orderqty', 'jml_pesan', 'jmlpesan', 'order', 'po_qty'],
            'no_rak' => ['nomor_rak', 'nomorrak', 'no_rak', 'norak', 'rak', 'lokasi', 'bin', 'shelf', 'rack', 'tempat'],
        ];

        foreach ($synonyms as $targetField => $aliases) {
            foreach ($headers as $h) {
                $clean = $this->normalizeString($h);
                if (in_array($clean, $aliases, true)) {
                    $mapping[$targetField] = $h;
                    break;
                }
            }
        }

        return $mapping;
    }

    private function normalizeString(?string $str): string
    {
        if ($str === null) return '';
        $clean = strtolower(trim($str));
        return preg_replace('/[^a-z0-9]/', '', $clean);
    }

    private function parseNumber($val): ?float
    {
        if ($val === null || $val === '') return null;
        if (is_numeric($val)) return (float) $val;
        if (!is_string($val)) return null;

        $clean = trim(preg_replace('/[^\d.,]/', '', $val));
        if ($clean === '') return null;

        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif (strpos($clean, '.') !== false) {
            $parts = explode('.', $clean);
            if (count($parts) > 1 && strlen(end($parts)) === 3) {
                $clean = str_replace('.', '', $clean);
            }
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return is_numeric($clean) ? (float) $clean : null;
    }

    private function parseInteger($val): ?int
    {
        $num = $this->parseNumber($val);
        return $num !== null ? (int) round($num) : null;
    }

    private function parseDate($val): ?string
    {
        if ($val === null || $val === '') return null;
        if ($val instanceof DateTimeInterface) {
            return $val->format('Y-m-d');
        }

        $str = trim((string) $val);

        // Jika angka Excel serial date (misal 45678)
        if (is_numeric($str) && (float) $str > 30000 && (float) $str < 70000) {
            try {
                $timestamp = ((float) $str - 25569) * 86400;
                return gmdate('Y-m-d', (int) $timestamp);
            } catch (Exception) {}
        }

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d',
        ];

        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $str);
                if ($d && $d->year >= 2000 && $d->year <= 2100) {
                    return $d->format('Y-m-d');
                }
            } catch (Exception) {}
        }

        // Coba parser fleksibel Carbon
        try {
            $d = Carbon::parse($str);
            if ($d && $d->year >= 2000 && $d->year <= 2100) {
                return $d->format('Y-m-d');
            }
        } catch (Exception) {}

        return null;
    }

    private function getCacheFilePath(string $cacheId): string
    {
        return $this->tempDir . '/' . preg_replace('/[^a-zA-Z0-9_]/', '', $cacheId) . '.json';
    }

    private function cleanupOldTempFiles(): void
    {
        try {
            $files = File::files($this->tempDir);
            $now = time();
            foreach ($files as $file) {
                // Hapus file yang lebih tua dari 1 jam
                if ($now - $file->getMTime() > 3600) {
                    @File::delete($file->getRealPath());
                }
            }
        } catch (Exception) {}
    }
}
