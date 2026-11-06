@extends('layouts.app')
@section('title', 'Faktur Penerimaan Baru')

@section('content')
<x-page-header
    title="Faktur Penerimaan Barang Baru"
    subtitle="Catat faktur masuk obat dari supplier beserta detail expired date batch."
    :back-url="route('penerimaan.index')" />

<form action="{{ route('penerimaan.store') }}" method="POST" id="form-penerimaan" class="space-y-6">
    @csrf
    <input type="hidden" name="items_json" id="items_json">

    <!-- Form Header Card -->
    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-start">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Faktur <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="no_faktur" value="{{ old('no_faktur') }}" required
                class="form-input font-mono font-semibold" placeholder="Nomor faktur masuk...">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Tanggal Faktur <span class="text-red-500 font-bold">*</span>
            </label>
            <input
                type="date"
                name="tanggal_faktur"
                value="{{ old('tanggal_faktur') }}"
                required
                class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Terima <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Supplier <span class="text-red-500 font-bold">*</span></label>
            <select name="supplier_id" id="supplier_id" required class="form-input">
                <option value="">Pilih Supplier</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" data-telepon="{{ $supplier->telepon }}" @selected(old('supplier_id')==$supplier->id)>{{ $supplier->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Telepon Supplier</label>
            <input type="text" id="telepon_supplier" class="form-input bg-gray-50" readonly placeholder="Otomatis dari master supplier">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Keterangan</label>
            <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-input" placeholder="Keterangan penerimaan (opsional)">
        </div>
    </div>

    <!-- Details Card -->
    <div class="card-base p-6">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Detail Barang Diterima</h3>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-import-excel" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-700 bg-white border border-emerald-500 hover:bg-emerald-50 hover:border-emerald-600 active:bg-emerald-100 rounded-lg shadow-2xs transition-colors cursor-pointer" title="Import Data Obat dari Excel atau CSV">
                    <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span>Import Excel</span>
                </button>
                <button type="button" id="btn-tambah-item" class="btn-secondary py-1 px-3 text-xs font-semibold cursor-pointer">
                    + Tambah item barang
                </button>
            </div>
        </div>

        <div class="table-custom-container">
            <div class="overflow-x-auto overflow-y-auto max-h-[430px]">
                <table class="penerimaan-detail-table mb-2">
                    <thead class="table-custom-header">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left">
                                Barang <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Barcode
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                No. Batch <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Expired Date <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Harga Beli <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Harga Jual <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                No. Rak <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Jumlah Dipesan <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Jumlah Diterima <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Satuan
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Subtotal
                            </th>

                            <th scope="col" class="px-3 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150"></tbody>
                </table>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center py-4" id="empty-hint">Belum ada baris. Klik "+ Tambah Baris" untuk mulai input.</p>
        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Belanja:</span>
            <span id="total-faktur" class="text-blue-700 font-mono">Rp 0</span>
        </div>
        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold items-center">
            <label for="ppn">PPN (11%)</label>
            <input type="text" id="ppn" value="Rp 0" readonly class="form-input w-full sm:w-40 text-right font-mono bg-gray-50">
        </div>

        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Tagihan:</span>
            <span id="total-tagihan" class="text-blue-700 font-mono">Rp 0</span>
        </div>

        <div class="mt-3.5 flex justify-end">
            <button type="button"
                id="btn-sinkron-pembayaran"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-white border border-blue-500 hover:bg-blue-50 hover:border-blue-600 active:bg-blue-100 rounded-lg shadow-2xs transition-colors cursor-pointer"
                title="Sinkronkan ke Pembayaran">
                <x-heroicon-o-arrow-path class="w-4 h-4 text-blue-600 shrink-0" />
                <span>Sinkronkan ke Pembayaran</span>
            </button>
        </div>
    </div>

    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pembayaran Saat Penerimaan</label>
            <input type="number" name="pembayaran_pertama" id="pembayaran_pertama" value="{{ old('pembayaran_pertama', 0) }}" min="0" step="0.01" class="form-input text-right font-mono" data-terbilang="true">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}" class="form-input">
        </div>
        <div class="text-xs text-gray-500 italic border-l-2 border-red-500 pl-2">Pembayaran pertama dicatat sebagai histori dan tidak menimpa pembayaran sebelumnya.</div>
    </div>

    <div class="flex justify-end items-center gap-2.5 pt-4">
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Faktur</button>
    </div>
</form>

<template id="row-template">
    <tr class="item-row hover:bg-gray-50 transition-colors">
        <td class="px-3 py-2">
            <select name="items[__i__][barang_id]" required class="form-input py-1 px-2 barang-select" style="width: 100% !important; max-width: none !important;">
                <option value="">Pilih barang</option>
                @foreach ($barangs as $barang)
                <option value="{{ $barang->id }}" data-pabrik="{{ $barang->pabrik->nama ?? '' }}" data-satuan="{{ $barang->satuan->nama ?? '' }}" data-barcode="{{ $barang->barcode }}">{{ $barang->nama }}{{ $barang->barcode ? ' — ' . $barang->barcode : '' }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" class="form-input py-1 px-2 barcode-field bg-gray-50" readonly tabindex="-1" style="width: 100% !important; max-width: none !important;">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="items[__i__][no_batch]" required class="form-input py-1 px-2 font-mono" placeholder="Batch..." style="min-width: 130px;">
        </td>
        <td class="px-3 py-2">
            <input type="date" name="items[__i__][expired_date]" required class="form-input py-1 px-2 expired-field" style="min-width: 130px;">
        </td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_beli]" required class="form-input py-1 px-2 text-right font-mono harga-beli" placeholder="0" style="min-width: 120px;"></td>
        <td class="px-3 py-2">
            <input
                type="number"
                step="0.01"
                min="0"
                name="items[__i__][harga_jual]"
                required
                class="form-input py-1 px-2 text-right font-mono harga-jual"
                placeholder="0"
                style="min-width: 120px;">
        </td>
        <td class="px-3 py-2"><input type="text" name="items[__i__][no_rak]" required class="form-input py-1 px-2 font-mono" placeholder="A-01"></td>
        <td class="px-3 py-2">
            <input type="number" min="1" name="items[__i__][jumlah_dipesan]" required class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field" placeholder="1">
        </td>
        <td class="px-3 py-2">
            <input type="number" min="0" name="items[__i__][jumlah_diterima]" required class="form-input py-1 px-2 text-right font-mono jumlah-diterima-field" placeholder="0">
        </td>
        <td class="px-3 py-2"><input type="text" class="form-input py-1 px-2 satuan-field bg-gray-50" readonly tabindex="-1" style="min-width: 90px;"></td>
        <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">Rp 0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" tabindex="-1" class="text-red-500 hover:text-red-700 p-1 btn-hapus-row" aria-label="Hapus baris" title="Hapus baris"><x-heroicon-o-trash class="w-4 h-4" /></button>
        </td>
    </tr>
</template>

<script>
    let rowIndex = 0;
    let currentTotalTagihan = 0;
    const tbody = document.getElementById('item-rows');
    const template = document.getElementById('row-template');
    const emptyHint = document.getElementById('empty-hint');
    const totalFaktur = document.getElementById('total-faktur');
    const ppn = document.getElementById('ppn');
    const totalTagihan = document.getElementById('total-tagihan');
    const btnSinkronPembayaran = document.getElementById('btn-sinkron-pembayaran');
    const pembayaranPertamaInput = document.getElementById('pembayaran_pertama');

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function updateTotal() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
            const jumlah = parseInt(row.querySelector('.jumlah-diterima-field')?.value, 10) || 0;
            const subtotal = harga * jumlah;
            total += subtotal;
            row.querySelector('.subtotal-field').textContent = formatRupiah(subtotal);
        });
        totalFaktur.textContent = formatRupiah(total);
        const nilaiPpn = total * 0.11;
        ppn.value = formatRupiah(nilaiPpn);
        currentTotalTagihan = total + nilaiPpn;
        totalTagihan.textContent = formatRupiah(currentTotalTagihan);
    }

    function getFocusableInputs(row) {
        if (!row) return [];
        return Array.from(row.querySelectorAll(
            'select:not([disabled]):not([tabindex="-1"]), input:not([type="hidden"]):not([disabled]):not([readonly]):not([tabindex="-1"])'
        ));
    }

    function updateRowBarangInfo(selectEl) {
        const option = selectEl.selectedOptions[0];
        const row = selectEl.closest('tr');
        if (row && option) {
            row.querySelector('.barcode-field').value = option.dataset.barcode || '';
            row.querySelector('.satuan-field').value = option.dataset.satuan || '';
        }
    }

    function tambahBaris(focusFirst = false) {
        const html = template.innerHTML.replaceAll('__i__', rowIndex);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;
        const newRow = tempTr.firstElementChild;
        tbody.appendChild(newRow);

        if (oldItems[rowIndex]) {
            const item = oldItems[rowIndex];
            const row = tbody.lastElementChild;

            row.querySelector('[name$="[barang_id]"]').value = item.barang_id || '';
            row.querySelector('[name$="[no_batch]"]').value = item.no_batch || '';
            row.querySelector('[name$="[expired_date]"]').value = item.expired_date || '';
            row.querySelector('[name$="[harga_beli]"]').value = item.harga_beli || '';
            row.querySelector('[name$="[harga_jual]"]').value = item.harga_jual || '';
            row.querySelector('[name$="[no_rak]"]').value = item.no_rak || '';
            row.querySelector('[name$="[jumlah_dipesan]"]').value = item.jumlah_dipesan || '';
            row.querySelector('[name$="[jumlah_diterima]"]').value = item.jumlah_diterima || '';

            const sel = row.querySelector('.barang-select');
            if (sel) {
                updateRowBarangInfo(sel);
            }
        }
        rowIndex++;
        emptyHint.style.display = 'none';
        updateTotal();

        if (focusFirst) {
            setTimeout(() => {
                const inputs = getFocusableInputs(newRow);
                if (inputs.length > 0) {
                    inputs[0].focus();
                    newRow.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }, 10);
        }

        return newRow;
    }

    const oldItems = @json(old('items', []));

    // Tombol tambah baris manual (mouse)
    document.getElementById('btn-tambah-item').addEventListener('click', function() {
        tambahBaris(true);
    });

    // Tombol sinkronisasi nominal total tagihan ke pembayaran saat penerimaan
    if (btnSinkronPembayaran) {
        btnSinkronPembayaran.addEventListener('click', function() {
            updateTotal();
            const nominal = Number.isInteger(currentTotalTagihan) ?
                currentTotalTagihan :
                Math.round(currentTotalTagihan * 100) / 100;

            if (pembayaranPertamaInput) {
                pembayaranPertamaInput.value = nominal;
                pembayaranPertamaInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                pembayaranPertamaInput.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            }
        });
    }

    // Hapus baris
    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-hapus-row')) {
            e.target.closest('tr').remove();
            if (tbody.children.length === 0) {
                emptyHint.style.display = 'block';
            }
            updateTotal();
        }
    });

    // Update barcode dan satuan saat pilih barang
    tbody.addEventListener('change', function(e) {
        if (e.target.classList.contains('barang-select')) {
            updateRowBarangInfo(e.target);
        }
    });

    tbody.addEventListener('input', function(e) {
        if (e.target.classList.contains('barang-select')) {
            updateRowBarangInfo(e.target);
        }
        updateTotal();
    });

    // Navigasi Keyboard Tab: Auto-tambah baris baru saat Tab di field terakhir
    tbody.addEventListener('keydown', function(e) {
        if (e.key === 'Tab' && !e.shiftKey) {
            const target = e.target;
            const row = target.closest('tr.item-row');
            if (!row) return;

            const focusableInputs = getFocusableInputs(row);
            if (focusableInputs.length === 0) return;

            const lastInput = focusableInputs[focusableInputs.length - 1];

            // Jika bukan field terakhir pada baris, biarkan tab alami browser berjalan rapi ke field berikutnya
            if (target !== lastInput) {
                return;
            }

            // Jika berada di field terakhir pada baris:
            const isLastRow = (row === tbody.lastElementChild);

            if (!isLastRow) {
                // Jika bukan baris terakhir (misal user mengedit baris atas), fokus ke field pertama baris berikutnya
                const nextRow = row.nextElementSibling;
                if (nextRow) {
                    const nextInputs = getFocusableInputs(nextRow);
                    if (nextInputs.length > 0) {
                        e.preventDefault();
                        nextInputs[0].focus();
                    }
                }
                return;
            }

            // Baris terakhir: Cek apakah seluruh field wajib (required) pada baris ini sudah terisi
            const requiredInputs = Array.from(row.querySelectorAll('select[required], input[required]'));
            const invalidInput = requiredInputs.find(input => {
                if (!input.value || input.value.trim() === '') return true;
                if (typeof input.checkValidity === 'function' && !input.checkValidity()) return true;
                return false;
            });

            if (invalidInput) {
                // Masih ada field wajib yang belum terisi di baris tersebut, jangan buat baris baru
                e.preventDefault();
                invalidInput.focus();
                if (typeof invalidInput.reportValidity === 'function') {
                    invalidInput.reportValidity();
                }
                return;
            }

            // Semua field wajib sudah terisi, buat baris baru dan otomatis pindah fokus ke field Barang baris baru
            e.preventDefault();
            tambahBaris(true);
        } else if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
            // Cegah submit form tiba-tiba saat menekan Enter di input tabel
            e.preventDefault();
        }
    });

    ppn.addEventListener('input', updateTotal);

    document.getElementById('supplier_id').addEventListener('change', function() {
        document.getElementById('telepon_supplier').value = this.selectedOptions[0]?.dataset.telepon || '';
    });

    tbody.addEventListener('input', function(e) {
        updateTotal();
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.name && target.name.includes('[expired_date]')) {
            const tgl = document.getElementById('tanggal')?.value;
            validateRowExpiredCreate(row, tgl);
        } else if (target.name && (target.name.includes('[harga_beli]') || target.name.includes('[harga_jual]'))) {
            validateRowHargaCreate(row);
        }
    });

    tbody.addEventListener('change', function(e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.name && target.name.includes('[expired_date]')) {
            const tgl = document.getElementById('tanggal')?.value;
            validateRowExpiredCreate(row, tgl);
        } else if (target.name && (target.name.includes('[harga_beli]') || target.name.includes('[harga_jual]'))) {
            validateRowHargaCreate(row);
        }
    });

    const tglInputCreate = document.getElementById('tanggal');
    if (tglInputCreate) {
        tglInputCreate.addEventListener('change', function() {
            const tgl = this.value;
            tbody.querySelectorAll('tr').forEach(row => {
                validateRowExpiredCreate(row, tgl);
            });
        });
    }

    function setFieldErrorCreate(input, message) {
        if (!input) return;
        input.classList.remove('border-gray-300');
        input.classList.add('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');

        const container = input.closest('td') || input.parentElement;
        if (!container) return;

        let errEl = container.querySelector('.field-error-msg');
        if (!errEl) {
            errEl = document.createElement('span');
            errEl.className = 'field-error-msg text-[11px] text-red-600 font-semibold block mt-1 leading-tight whitespace-normal';
            container.appendChild(errEl);
        }
        errEl.textContent = message;
    }

    function clearFieldErrorCreate(input) {
        if (!input) return;
        input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
        input.classList.add('border-gray-300');

        const container = input.closest('td') || input.parentElement;
        if (!container) return;

        const errEl = container.querySelector('.field-error-msg');
        if (errEl) {
            errEl.remove();
        }
    }

    function clearAllErrorsCreate() {
        const form = document.getElementById('form-penerimaan');
        if (!form) return;
        form.querySelectorAll('.field-error-msg').forEach(el => el.remove());
        form.querySelectorAll('.\\!border-red-500').forEach(input => {
            input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
            input.classList.add('border-gray-300');
        });
        const generalBox = document.getElementById('create-general-error');
        if (generalBox) {
            generalBox.classList.add('hidden');
            generalBox.textContent = '';
        }
    }

    function validateRowExpiredCreate(row, tanggalPenerimaan) {
        if (!row) return true;
        const input = row.querySelector('[name$="[expired_date]"]');
        if (!input) return true;

        if (input.value && tanggalPenerimaan) {
            if (input.value < tanggalPenerimaan) {
                setFieldErrorCreate(input, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                return false;
            }
        }
        clearFieldErrorCreate(input);
        return true;
    }

    function validateRowHargaCreate(row) {
        if (!row) return true;
        const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
        const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
        if (!hbInput || !hjInput) return true;

        const hb = parseFloat(hbInput.value);
        const hj = parseFloat(hjInput.value);

        if (!isNaN(hb) && !isNaN(hj)) {
            if (hj < hb) {
                setFieldErrorCreate(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                return false;
            }
        }
        clearFieldErrorCreate(hjInput);
        return true;
    }

    function validateFormCreate() {
        clearAllErrorsCreate();
        let isValid = true;
        let firstErrorField = null;

        const tglPenerimaan = document.getElementById('tanggal')?.value || '';

        tbody.querySelectorAll('tr').forEach(row => {
            // Validasi Expired
            const expInput = row.querySelector('[name$="[expired_date]"]');
            if (expInput) {
                if (!expInput.value) {
                    setFieldErrorCreate(expInput, 'Tanggal expired wajib diisi.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = expInput;
                } else if (tglPenerimaan && expInput.value < tglPenerimaan) {
                    setFieldErrorCreate(expInput, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = expInput;
                }
            }

            // Validasi Harga
            const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
            const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
            if (hbInput && hjInput) {
                const hb = parseFloat(hbInput.value);
                const hj = parseFloat(hjInput.value);

                if (isNaN(hj) || hj < 0) {
                    setFieldErrorCreate(hjInput, 'Harga jual wajib diisi.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hjInput;
                } else if (!isNaN(hb) && hj < hb) {
                    setFieldErrorCreate(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hjInput;
                }
            }
        });

        if (!isValid && firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        return isValid;
    }

    function applyServerErrorsCreate(errors) {
        clearAllErrorsCreate();
        const form = document.getElementById('form-penerimaan');
        if (!form) return;
        let firstErrorField = null;
        let unmappedMessages = [];

        for (const key in errors) {
            const msgs = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
            const msg = msgs[0];

            const match = key.match(/^items\.(\d+)\.([a-z_]+)$/);
            if (match) {
                const rowIndex = parseInt(match[1], 10);
                const fieldName = match[2];
                const rows = tbody.querySelectorAll('tr');
                const row = rows[rowIndex];
                if (row) {
                    const input = row.querySelector(`[name$="[${fieldName}]"]`);
                    if (input) {
                        setFieldErrorCreate(input, msg);
                        if (!firstErrorField) firstErrorField = input;
                        continue;
                    }
                }
            }

            const topInput = form.querySelector(`[name="${key}"]`);
            if (topInput) {
                setFieldErrorCreate(topInput, msg);
                if (!firstErrorField) firstErrorField = topInput;
                continue;
            }

            unmappedMessages.push(msg);
        }

        const generalBox = document.getElementById('create-general-error');
        if (generalBox && unmappedMessages.length > 0) {
            generalBox.textContent = unmappedMessages.join(', ');
            generalBox.classList.remove('hidden');
        }

        if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    const formCreate = document.getElementById('form-penerimaan');
    const btnSimpanCreate = formCreate.querySelector('button[type="submit"]');

    if (formCreate) {
        formCreate.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                e.preventDefault();
            }
        });

        formCreate.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (tbody.children.length === 0) {
                alert('Tambahkan minimal 1 baris barang.');
                return;
            }

            if (!validateFormCreate()) {
                return;
            }

            if (btnSimpanCreate && btnSimpanCreate.disabled) return;

            const items = [];
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const bId = row.querySelector('[name$="[barang_id]"]')?.value;
                const nb = row.querySelector('[name$="[no_batch]"]')?.value;
                const ed = row.querySelector('[name$="[expired_date]"]')?.value;
                const hb = row.querySelector('.harga-beli')?.value ?? row.querySelector('[name$="[harga_beli]"]')?.value;
                const hj = row.querySelector('.harga-jual')?.value ?? row.querySelector('[name$="[harga_jual]"]')?.value;
                const nr = row.querySelector('[name$="[no_rak]"]')?.value;
                const jd = row.querySelector('.jumlah-dipesan-field')?.value ?? row.querySelector('[name$="[jumlah_dipesan]"]')?.value;
                const jt = row.querySelector('.jumlah-diterima-field')?.value ?? row.querySelector('[name$="[jumlah_diterima]"]')?.value;

                if (bId || nb) {
                    items.push({
                        barang_id: bId ? parseInt(bId, 10) : '',
                        no_batch: nb ? String(nb).trim() : '',
                        expired_date: ed ? String(ed).trim() : '',
                        harga_beli: hb !== '' && hb !== null && hb !== undefined ? parseFloat(hb) : 0,
                        harga_jual: hj !== '' && hj !== null && hj !== undefined ? parseFloat(hj) : 0,
                        no_rak: nr ? String(nr).trim() : 'A-01',
                        jumlah_dipesan: jd !== '' && jd !== null && jd !== undefined ? parseInt(jd, 10) : 1,
                        jumlah_diterima: jt !== '' && jt !== null && jt !== undefined ? parseInt(jt, 10) : 0,
                    });
                }
            });

            if (items.length === 0) {
                alert('Tambahkan minimal 1 baris barang.');
                return;
            }

            const itemsJsonInput = document.getElementById('items_json');
            if (itemsJsonInput) {
                itemsJsonInput.value = JSON.stringify(items);
            }

            const payload = {
                supplier_id: document.getElementById('supplier_id')?.value,
                tanggal: document.getElementById('tanggal')?.value,
                tanggal_faktur: formCreate.querySelector('[name="tanggal_faktur"]')?.value,
                no_faktur: formCreate.querySelector('[name="no_faktur"]')?.value,
                keterangan: formCreate.querySelector('[name="keterangan"]')?.value || '',
                jatuh_tempo: formCreate.querySelector('[name="jatuh_tempo"]')?.value || null,
                pembayaran_pertama: document.getElementById('pembayaran_pertama')?.value || 0,
                items: items,
            };

            const originalBtnHtml = btnSimpanCreate ? btnSimpanCreate.innerHTML : 'Simpan Faktur';
            if (btnSimpanCreate) {
                btnSimpanCreate.disabled = true;
                btnSimpanCreate.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Menyimpan Faktur...
            `;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                formCreate.querySelector('input[name="_token"]')?.value || '';

            try {
                const response = await fetch(formCreate.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        applyServerErrorsCreate(data.errors);
                        const firstErrKey = Object.keys(data.errors)[0];
                        const firstErrMsg = Array.isArray(data.errors[firstErrKey]) ? data.errors[firstErrKey][0] : data.errors[firstErrKey];
                        if (typeof window.showToast === 'function') {
                            window.showToast(firstErrMsg || 'Terdapat kesalahan validasi formulir.', 'error');
                        } else {
                            alert(firstErrMsg || 'Terdapat kesalahan validasi formulir.');
                        }
                    } else {
                        const errorMsg = data.message || 'Terjadi kesalahan saat menyimpan faktur.';
                        if (typeof window.showToast === 'function') {
                            window.showToast(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                    }
                    if (btnSimpanCreate) {
                        btnSimpanCreate.disabled = false;
                        btnSimpanCreate.innerHTML = originalBtnHtml;
                    }
                    return;
                }

                if (typeof window.showToast === 'function') {
                    window.showToast(data.message || 'Penerimaan barang berhasil disimpan.', 'success');
                }
                setTimeout(() => {
                    window.location.href = data.redirect || '/penerimaan';
                }, 400);

            } catch (err) {
                console.error('Error saat menyimpan faktur:', err);
                if (btnSimpanCreate) {
                    btnSimpanCreate.disabled = false;
                    btnSimpanCreate.innerHTML = originalBtnHtml;
                }
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal menghubungi server. Silakan coba lagi.', 'error');
                } else {
                    alert('Gagal menghubungi server. Silakan coba lagi.');
                }
            }
        });
    }

    // Mulai dengan baris awal
    if (oldItems.length > 0) {
        oldItems.forEach(() => {
            tambahBaris(false);
        });
    } else {
        tambahBaris(false);
    }

    @if($errors->any())
    setTimeout(() => {
        applyServerErrorsCreate(@json($errors->toArray()));
    }, 50);
    @endif

    // Hook untuk import Excel pada create page
    window.insertImportedItemCreate = function(item) {
        const html = template.innerHTML.replaceAll('__i__', rowIndex);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;
        const row = tempTr.firstElementChild;
        tbody.appendChild(row);

        const bId = row.querySelector('[name$="[barang_id]"]');
        if (bId) {
            bId.value = item.barang_id;
            const opt = bId.selectedOptions[0];
            const bcField = row.querySelector('.barcode-field');
            const satField = row.querySelector('.satuan-field');
            if (bcField) bcField.value = opt?.dataset.barcode || item.barcode || '';
            if (satField) satField.value = opt?.dataset.satuan || item.satuan_nama || '';
        }

        const nb = row.querySelector('[name$="[no_batch]"]');
        if (nb) nb.value = item.no_batch || '';

        const ed = row.querySelector('[name$="[expired_date]"]');
        if (ed) ed.value = item.expired_date || '';

        const hb = row.querySelector('[name$="[harga_beli]"]');
        if (hb) hb.value = (item.harga_beli !== null && item.harga_beli !== undefined) ? item.harga_beli : '';

        const hj = row.querySelector('[name$="[harga_jual]"]');
        if (hj) hj.value = (item.harga_jual !== null && item.harga_jual !== undefined) ? item.harga_jual : '';

        const nr = row.querySelector('[name$="[no_rak]"]');
        if (nr) nr.value = item.no_rak || 'A-01';

        const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
        if (jd) jd.value = item.jumlah_dipesan || 1;

        const jt = row.querySelector('[name$="[jumlah_diterima]"]');
        if (jt) jt.value = item.jumlah_diterima || 1;

        rowIndex++;
    };

    window.insertImportedItemsBatchCreate = function(items) {
        if (!template || !tbody || !items || items.length === 0) return;
        const fragment = document.createDocumentFragment();
        const baseHtml = template.innerHTML;

        items.forEach(item => {
            const html = baseHtml.replaceAll('__i__', rowIndex);
            const tempTr = document.createElement('tbody');
            tempTr.innerHTML = html;
            const row = tempTr.firstElementChild;

            const bId = row.querySelector('[name$="[barang_id]"]');
            if (bId) {
                bId.value = item.barang_id;
                const opt = bId.selectedOptions[0];
                const bcField = row.querySelector('.barcode-field');
                const satField = row.querySelector('.satuan-field');
                if (bcField) bcField.value = opt?.dataset.barcode || item.barcode || '';
                if (satField) satField.value = opt?.dataset.satuan || item.satuan_nama || '';
            }

            const nb = row.querySelector('[name$="[no_batch]"]');
            if (nb) nb.value = item.no_batch || '';

            const ed = row.querySelector('[name$="[expired_date]"]');
            if (ed) ed.value = item.expired_date || '';

            const hb = row.querySelector('[name$="[harga_beli]"]');
            if (hb) hb.value = (item.harga_beli !== null && item.harga_beli !== undefined) ? item.harga_beli : '';

            const hj = row.querySelector('[name$="[harga_jual]"]');
            if (hj) hj.value = (item.harga_jual !== null && item.harga_jual !== undefined) ? item.harga_jual : '';

            const nr = row.querySelector('[name$="[no_rak]"]');
            if (nr) nr.value = item.no_rak || 'A-01';

            const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
            if (jd) jd.value = item.jumlah_dipesan || 1;

            const jt = row.querySelector('[name$="[jumlah_diterima]"]');
            if (jt) jt.value = item.jumlah_diterima || 1;

            fragment.appendChild(row);
            rowIndex++;
        });

        tbody.appendChild(fragment);
        const emptyHintEl = document.getElementById('empty-hint');
        if (emptyHintEl) emptyHintEl.style.display = 'none';
        updateTotal();
    };
    window.updateTotalCreate = updateTotal;
</script>

@vite('resources/js/pages/penerimaan-index.js')
<!-- Modal Import Data Obat Excel/CSV -->
@include('penerimaan.partials.modal-import-obat')

@endsection