@extends('layouts.app')
@section('title', 'Faktur Penerimaan Baru')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Faktur Penerimaan Barang Baru</h1>
        <p class="text-caption mt-1">Catat faktur masuk obat dari supplier beserta detail expired date batch.</p>
    </div>
    <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-2 px-4">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert-danger p-4 mb-6">
        <strong class="block text-xs font-bold mb-1.5">Perbaiki kesalahan berikut sebelum menyimpan faktur:</strong>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('penerimaan.store') }}" method="POST" id="form-penerimaan" class="space-y-6">
    @csrf

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
                class="form-input"
            >
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Terima <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Supplier <span class="text-red-500 font-bold">*</span></label>
            <select name="supplier_id" id="supplier_id" required class="form-input">
                <option value="">Pilih Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" data-telepon="{{ $supplier->telepon }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->nama }}</option>
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
            <button type="button" id="btn-tambah-item" class="btn-secondary py-1 px-3 text-xs font-semibold">
                + Tambah item barang
            </button>
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

        </div>
            <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        </div>

    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pembayaran Saat Penerimaan</label>
            <input type="number" name="pembayaran_pertama" id="pembayaran_pertama" value="{{ old('pembayaran_pertama', 0) }}" min="0" step="0.01" class="form-input text-right font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}" class="form-input">
        </div>
        <div class="text-xs text-gray-500">Pembayaran pertama dicatat sebagai histori dan tidak menimpa pembayaran sebelumnya.</div>
    </div>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan Faktur</button>
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary">Batal</a>
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
            <input type="date" name="items[__i__][expired_date]" required class="form-input py-1 px-2" style="min-width: 125px;">
        </td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_beli]" required class="form-input py-1 px-2 text-right font-mono harga-beli" placeholder="0" style="min-width: 145px;"></td>
        <td class="px-3 py-2">
    <input
        type="number"
        step="0.01"
        min="0"
        name="items[__i__][harga_jual]"
        required
        class="form-input py-1 px-2 text-right font-mono"
        placeholder="0"
        style="width: 100% !important; max-width: none !important;"
    >
</td>
        <td class="px-3 py-2"><input type="text" name="items[__i__][no_rak]" required class="form-input py-1 px-2 font-mono" placeholder="A-01"></td>
        <td class="px-3 py-2">
            <input type="number" min="1" name="items[__i__][jumlah_dipesan]" required class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field" placeholder="1">
        </td >
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
const tbody = document.getElementById('item-rows');
const template = document.getElementById('row-template');
const emptyHint = document.getElementById('empty-hint');
const totalFaktur = document.getElementById('total-faktur');
const ppn = document.getElementById('ppn');
const totalTagihan = document.getElementById('total-tagihan');

function formatRupiah(value) { return 'Rp ' + Math.round(value).toLocaleString('id-ID'); }

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
    totalTagihan.textContent = formatRupiah(total + nilaiPpn);
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
                newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 10);
    }

    return newRow;
}

const oldItems = @json(old('items', []));

// Tombol tambah baris manual (mouse)
document.getElementById('btn-tambah-item').addEventListener('click', function () {
    tambahBaris(true);
});

// Hapus baris
tbody.addEventListener('click', function (e) {
    if (e.target.closest('.btn-hapus-row')) {
        e.target.closest('tr').remove();
        if (tbody.children.length === 0) {
            emptyHint.style.display = 'block';
        }
        updateTotal();
    }
});

// Update barcode dan satuan saat pilih barang
tbody.addEventListener('change', function (e) {
    if (e.target.classList.contains('barang-select')) {
        updateRowBarangInfo(e.target);
    }
});

tbody.addEventListener('input', function (e) {
    if (e.target.classList.contains('barang-select')) {
        updateRowBarangInfo(e.target);
    }
    updateTotal();
});

// Navigasi Keyboard Tab: Auto-tambah baris baru saat Tab di field terakhir
tbody.addEventListener('keydown', function (e) {
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

document.getElementById('supplier_id').addEventListener('change', function () {
    document.getElementById('telepon_supplier').value = this.selectedOptions[0]?.dataset.telepon || '';
});

document.getElementById('form-penerimaan').addEventListener('submit', function (e) {
    if (tbody.children.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 baris barang.');
    }
});

// Mulai dengan baris awal
if (oldItems.length > 0) {
    oldItems.forEach(() => {
        tambahBaris(false);
    });
} else {
    tambahBaris(false);
}
</script>
@endsection
