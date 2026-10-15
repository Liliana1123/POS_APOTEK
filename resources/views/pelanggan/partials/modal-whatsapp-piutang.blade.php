<!-- Modal Pratinjau Pengingat WhatsApp -->
<div id="whatsappPiutangModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[10000] items-center justify-center bg-black/50 p-4">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <x-heroicon-o-chat-bubble-left-right class="h-5 w-5" />
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Pengingat Piutang WhatsApp
                    </h3>
                    <p class="text-xs text-gray-500">
                        Pratinjau tagihan member sebelum konfirmasi pengiriman
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeWhatsAppPiutangPreview()"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi modal -->
        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">

            <div id="whatsappPiutangLoading"
                class="hidden rounded-lg bg-gray-50 p-4 text-center text-sm text-gray-500">
                <div class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat tagihan...</span>
                </div>
            </div>

            <div id="whatsappPiutangSuccess"
                class="hidden rounded-lg border border-green-200 bg-green-50 p-3 text-sm font-medium text-green-800"
                role="alert"></div>

            <div id="whatsappPiutangError"
                class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"
                role="alert"></div>

            <div id="whatsappPiutangContent" class="hidden space-y-4">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 p-3 bg-gray-50/50">
                        <p class="text-xs text-gray-500">Nama Member</p>
                        <p id="whatsappPiutangNama" class="mt-1 break-words font-semibold text-gray-800">-</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-3 bg-gray-50/50">
                        <p class="text-xs text-gray-500">Nomor WhatsApp</p>
                        <p id="whatsappPiutangTelepon" class="mt-1 break-words font-semibold text-gray-800 font-mono">-</p>
                    </div>
                </div>

                <div>
                    <label for="whatsappPiutangInvoice"
                        class="mb-1 block text-sm font-semibold text-gray-700">
                        Pilih Faktur Tagihan <span class="text-red-500">*</span>
                    </label>
                    <select id="whatsappPiutangInvoice" class="form-input w-full">
                        <option value="">Pilih faktur yang akan diingatkan</option>
                    </select>
                </div>

                <div id="whatsappPiutangDetail"
                    class="hidden space-y-2 rounded-xl border border-green-200 bg-green-50/50 p-4">

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-sm text-gray-500">Nomor Faktur</span>
                        <span id="whatsappPiutangNoFaktur"
                            class="break-words text-right text-sm font-semibold text-gray-800 font-mono">-</span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-sm text-gray-500">Jatuh Tempo</span>
                        <span id="whatsappPiutangJatuhTempo"
                            class="text-right text-sm font-semibold text-gray-800">-</span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-sm text-gray-500">Sisa Piutang</span>
                        <span id="whatsappPiutangSisa"
                            class="text-right text-base font-bold text-red-600">-</span>
                    </div>
                </div>

                <div>
                    <label for="whatsappPiutangPesan"
                        class="mb-1 block text-sm font-semibold text-gray-700">
                        Pratinjau Pesan WhatsApp
                    </label>
                    <textarea id="whatsappPiutangPesan"
                        class="form-input w-full font-sans text-xs sm:text-sm leading-relaxed"
                        rows="6"
                        readonly
                        placeholder="Pesan akan tampil setelah faktur dipilih."></textarea>
                    <p class="mt-1 text-xs text-gray-500">
                        Pesan ini disusun otomatis mohon konfirmasi sebelum pesan benar-benar dikirim.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex shrink-0 justify-end gap-2 border-t border-gray-200 px-5 py-4">
            <button type="button"
                onclick="bukaKonfirmasiWhatsApp()"
                id="btnKirimWhatsAppPiutang"
                disabled
                title="Pilih faktur terlebih dahulu untuk mengaktifkan pengiriman"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white opacity-50 cursor-not-allowed transition">
                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                <span>Kirim Pesan WhatsApp</span>
            </button>

            <button type="button"
                onclick="closeWhatsAppPiutangPreview()"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pengiriman WhatsApp -->
<div id="whatsappPiutangConfirmModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[10010] items-center justify-center bg-black/60 p-4">

    <div class="modal-container-custom flex w-full max-w-lg max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Konfirmasi -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-5 py-4 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Konfirmasi Pengiriman WhatsApp
                    </h3>
                    <p class="text-xs text-gray-500">
                        Periksa kembali data sebelum pesan dikirim ke gateway
                    </p>
                </div>
            </div>

            <button type="button"
                id="btnBatalKirimWhatsAppX"
                onclick="closeWhatsAppPiutangConfirm()"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-200 hover:text-gray-600"
                title="Batal">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi Konfirmasi -->
        <div class="min-h-0 flex-1 space-y-3 overflow-y-auto p-5 text-sm">

            <div id="waConfirmError"
                class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-xs sm:text-sm text-red-700 font-medium"
                role="alert"></div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3.5 space-y-2">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Nama Member:</span>
                    <span id="waConfirmNama" class="font-semibold text-gray-800 text-right">-</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">WhatsApp Tujuan:</span>
                    <span id="waConfirmTelepon" class="font-mono font-bold text-green-700 text-right">-</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Nomor Faktur:</span>
                    <span id="waConfirmNoFaktur" class="font-mono font-semibold text-gray-800 text-right">-</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Sisa Piutang:</span>
                    <span id="waConfirmSisa" class="font-bold text-red-600 text-right">-</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Jatuh Tempo:</span>
                    <span id="waConfirmJatuhTempo" class="font-medium text-gray-700 text-right">-</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Isi Pesan yang Akan Dikirim:
                </label>
                <div id="waConfirmPesan"
                    class="rounded-lg border border-gray-200 bg-white p-3 text-xs text-gray-700 whitespace-pre-line max-h-36 overflow-y-auto leading-relaxed font-sans">
                </div>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 p-2.5 text-xs text-blue-800">
                Pesan akan dikirim langsung ke nomor WhatsApp member melalui WhatsApp.
            </div>
        </div>

        <!-- Footer Konfirmasi -->
        <div class="flex shrink-0 justify-end gap-2 border-t border-gray-200 px-5 py-4 bg-gray-50">
            <button type="button"
                id="btnBatalKirimWhatsApp"
                onclick="closeWhatsAppPiutangConfirm()"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-white">
                Batal
            </button>

            <button type="button"
                id="btnEksekusiKirimWhatsApp"
                onclick="eksekusiKirimWhatsApp()"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                <span id="btnEksekusiKirimWhatsAppText">Ya, Kirim Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- 1. MODAL KONFIRMASI PENGIRIMAN MASSAL (Langkah 1)       -->
<!-- ======================================================== -->
<div id="bulkWhatsAppConfirmModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[10010] items-center justify-center bg-black/60 p-4">

    <div class="modal-container-custom flex w-full max-w-xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Konfirmasi -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-5 py-4 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Konfirmasi Pengiriman WhatsApp
                    </h3>
                    <p class="text-xs text-gray-500">
                        Anda akan mengirim pesan pengingat piutang ke <strong id="bulkConfirmCountText" class="text-emerald-700">0</strong> member yang dipilih.
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeBulkWhatsAppConfirmModal()"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-200 hover:text-gray-600"
                title="Batal">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi Konfirmasi -->
        <div class="min-h-0 flex-1 space-y-3.5 overflow-y-auto p-5 text-sm">

            <div id="bulkConfirmNotice"
                class="hidden rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-xs text-amber-800"
                role="alert"></div>

            <!-- Tabel Daftar Penerima -->
            <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="max-h-44 overflow-y-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-100 text-gray-600 font-semibold sticky top-0 border-b border-gray-200">
                            <tr>
                                <th class="py-2 px-3">Nama Member</th>
                                <th class="py-2 px-3">No. WhatsApp</th>
                                <th class="py-2 px-3 text-right">Sisa Piutang</th>
                                <th class="py-2 px-3 text-center">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody id="bulkConfirmTableBody" class="divide-y divide-gray-150">
                            <!-- Diisi via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pratinjau Pesan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Pratinjau Pesan:
                </label>
                <div id="bulkConfirmPreviewText"
                    class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 whitespace-pre-line leading-relaxed font-sans max-h-36 overflow-y-auto">
                </div>
                <p class="text-[11px] text-gray-400 mt-1">
                    * Nama member, nominal piutang, dan tanggal jatuh tempo akan terisi otomatis untuk masing-masing penerima.
                </p>
            </div>
        </div>

        <!-- Footer Konfirmasi -->
        <div class="flex shrink-0 justify-end gap-2 border-t border-gray-200 px-5 py-4 bg-gray-50">
            <button type="button"
                onclick="closeBulkWhatsAppConfirmModal()"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-white">
                Batal
            </button>

            <button type="button"
                id="btnStartBulkSend"
                onclick="startBulkWhatsAppSending()"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                <x-heroicon-o-check class="h-4 w-4" />
                <span>Konfirmasi dan Kirim</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- 2. MODAL PROSES PENGIRIMAN MASSAL (Langkah 2)          -->
<!-- ======================================================== -->
<div id="bulkWhatsAppProgressModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[10020] items-center justify-center bg-black/60 p-4">

    <div class="modal-container-custom flex w-full max-w-lg max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Proses -->
        <div class="border-b border-gray-200 px-5 py-4 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <x-heroicon-o-paper-airplane class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Mengirim Pesan WhatsApp
                    </h3>
                    <p class="text-xs text-gray-500">
                        Mohon tunggu, pesan sedang dikirim ke <span id="bulkProgressHeaderTotal">0</span> member...
                    </p>
                </div>
            </div>
        </div>

        <!-- Isi Proses List -->
        <div class="min-h-0 flex-1 space-y-3 overflow-y-auto p-5">
            <div id="bulkProgressList" class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                <!-- Render item penerima dan statusnya -->
            </div>

            <!-- Progress Bar -->
            <div class="pt-3 border-t border-gray-150 space-y-1.5">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="bulkProgressBarFill" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span id="bulkProgressDoneText">0 dari 0 selesai</span>
                    <span id="bulkProgressPercentText" class="font-semibold text-gray-700">0%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- 3. MODAL HASIL PENGIRIMAN MASSAL (Langkah 3)           -->
<!-- ======================================================== -->
<div id="bulkWhatsAppResultModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[10030] items-center justify-center bg-black/60 p-4">

    <div class="modal-container-custom flex w-full max-w-md max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl p-6 text-center">

        <!-- Centered Hero Icon -->
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mx-auto mb-3">
            <x-heroicon-o-check class="h-9 w-9 stroke-[2.5]" />
        </div>

        <h3 class="text-lg font-bold text-gray-800">
            Pengiriman Selesai
        </h3>
        <p class="text-xs text-gray-500 mt-1 mb-5">
            Pesan pengingat piutang telah berhasil diproses.
        </p>

        <!-- Kartu Ringkasan -->
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-2.5 text-left text-xs sm:text-sm mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Member Dipilih</span>
                <strong id="bulkResultTotal" class="font-bold text-gray-800">0</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Berhasil Terkirim</span>
                <strong id="bulkResultSuccess" class="font-bold text-emerald-600">0</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Gagal</span>
                <strong id="bulkResultFailed" class="font-bold text-red-600">0</strong>
            </div>
        </div>

        <!-- Tombol Tutup -->
        <button type="button"
            onclick="closeBulkWhatsAppResultModal()"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow transition">
            Tutup
        </button>
    </div>
</div>

<script>
let currentPelangganId = null;
let whatsappPiutangTagihan = [];
const defaultNamaApotek = @json(\App\Models\InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek'));
let currentNamaApotek = defaultNamaApotek;

function closeWhatsAppPiutangPreview() {
    const modal = document.getElementById('whatsappPiutangModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function closeWhatsAppPiutangConfirm() {
    const modal = document.getElementById('whatsappPiutangConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function formatRupiahWhatsApp(nilai) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(nilai) || 0);
}

function updateBtnKirimState(isEnabled) {
    const btn = document.getElementById('btnKirimWhatsAppPiutang');
    if (!btn) return;

    if (isEnabled) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.classList.add('hover:bg-green-700', 'cursor-pointer');
        btn.title = 'Kirim Pesan WhatsApp Pengingat Piutang';
    } else {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-green-700', 'cursor-pointer');
        btn.title = 'Pilih faktur terlebih dahulu untuk mengaktifkan pengiriman';
    }
}

function openWhatsAppPiutangPreview(pelangganId) {
    currentPelangganId = pelangganId;

    const modal = document.getElementById('whatsappPiutangModal');
    const loading = document.getElementById('whatsappPiutangLoading');
    const errorBox = document.getElementById('whatsappPiutangError');
    const successBox = document.getElementById('whatsappPiutangSuccess');
    const content = document.getElementById('whatsappPiutangContent');
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');

    if (!modal || !loading || !errorBox || !content || !invoiceSelect) {
        alert('Komponen modal WhatsApp belum siap.');
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    loading.classList.remove('hidden');
    errorBox.classList.add('hidden');
    if (successBox) successBox.classList.add('hidden');
    content.classList.add('hidden');

    whatsappPiutangTagihan = [];
    invoiceSelect.innerHTML = '<option value="">Pilih faktur yang akan diingatkan</option>';

    document.getElementById('whatsappPiutangDetail').classList.add('hidden');
    document.getElementById('whatsappPiutangPesan').value = '';
    updateBtnKirimState(false);

    const urlTemplate = @json(route('pelanggan.whatsapp-piutang.preview', ['pelanggan' => '__ID__']));
    const url = urlTemplate.replace('__ID__', encodeURIComponent(pelangganId));

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'Gagal mengambil pratinjau tagihan.');
        }

        return data;
    })
    .then(data => {
        if (!data.pelanggan || !Array.isArray(data.tagihan) || data.tagihan.length === 0) {
            throw new Error(data.message || 'Tidak ada tagihan yang memenuhi syarat pengingat.');
        }

        if (!data.pelanggan.telepon || !String(data.pelanggan.telepon).trim()) {
            throw new Error('Nomor telepon member belum diisi. Lengkapi data pelanggan terlebih dahulu.');
        }

        whatsappPiutangTagihan = data.tagihan;
        currentNamaApotek = data.nama_apotek || defaultNamaApotek;

        document.getElementById('whatsappPiutangNama').textContent = data.pelanggan.nama || '-';
        document.getElementById('whatsappPiutangTelepon').textContent = data.pelanggan.telepon;

        data.tagihan.forEach(tagihan => {
            const option = document.createElement('option');
            option.value = String(tagihan.penjualan_id);
            option.textContent =
                `${tagihan.no_faktur || 'Faktur #' + tagihan.penjualan_id} — ` +
                formatRupiahWhatsApp(tagihan.sisa_piutang) +
                ` — jatuh tempo ${tagihan.jatuh_tempo}`;

            invoiceSelect.appendChild(option);
        });

        content.classList.remove('hidden');
    })
    .catch(error => {
        errorBox.textContent = error.message || 'Terjadi kesalahan saat mengambil tagihan.';
        errorBox.classList.remove('hidden');
    })
    .finally(() => {
        loading.classList.add('hidden');
    });
}

document.getElementById('whatsappPiutangInvoice')?.addEventListener('change', function () {
    const detail = document.getElementById('whatsappPiutangDetail');
    const pesan = document.getElementById('whatsappPiutangPesan');

    const tagihan = whatsappPiutangTagihan.find(
        item => String(item.penjualan_id) === this.value
    );

    if (!tagihan) {
        detail.classList.add('hidden');
        pesan.value = '';
        updateBtnKirimState(false);
        return;
    }

    document.getElementById('whatsappPiutangNoFaktur').textContent = tagihan.no_faktur || '-';
    document.getElementById('whatsappPiutangJatuhTempo').textContent = tagihan.jatuh_tempo || '-';
    document.getElementById('whatsappPiutangSisa').textContent = formatRupiahWhatsApp(tagihan.sisa_piutang);

    const nama = document.getElementById('whatsappPiutangNama').textContent;
    const sisa = formatRupiahWhatsApp(tagihan.sisa_piutang);
    const jatuhTempo = tagihan.jatuh_tempo || '-';
    const apotek = currentNamaApotek || 'Apotek';

    pesan.value =
        `Yth. Bapak/Ibu ${nama},\n\n` +
        `Kami dari ${apotek} ingin mengingatkan bahwa masih terdapat piutang sebesar ${sisa} dengan tanggal jatuh tempo ${jatuhTempo}.\n\n` +
        `Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n` +
        `Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n` +
        `Terima kasih atas perhatian dan kerja samanya.\n\n` +
        `Salam,\n` +
        `${apotek}`;

    detail.classList.remove('hidden');
    updateBtnKirimState(true);
});

function bukaKonfirmasiWhatsApp() {
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');
    const penjualanId = invoiceSelect ? invoiceSelect.value : '';

    const tagihan = whatsappPiutangTagihan.find(
        item => String(item.penjualan_id) === penjualanId
    );

    if (!tagihan) {
        alert('Silakan pilih faktur tagihan terlebih dahulu.');
        return;
    }

    const nama = document.getElementById('whatsappPiutangNama').textContent;
    const telepon = document.getElementById('whatsappPiutangTelepon').textContent;
    const pesan = document.getElementById('whatsappPiutangPesan').value;

    document.getElementById('waConfirmNama').textContent = nama;
    document.getElementById('waConfirmTelepon').textContent = telepon;
    document.getElementById('waConfirmNoFaktur').textContent = tagihan.no_faktur || ('#' + tagihan.penjualan_id);
    document.getElementById('waConfirmSisa').textContent = formatRupiahWhatsApp(tagihan.sisa_piutang);
    document.getElementById('waConfirmJatuhTempo').textContent = tagihan.jatuh_tempo || '-';
    document.getElementById('waConfirmPesan').textContent = pesan;

    const confirmError = document.getElementById('waConfirmError');
    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const modalConfirm = document.getElementById('whatsappPiutangConfirmModal');
    if (modalConfirm) {
        modalConfirm.classList.remove('hidden');
        modalConfirm.classList.add('flex');
    }
}

function eksekusiKirimWhatsApp() {
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');
    const penjualanId = invoiceSelect ? invoiceSelect.value : '';

    if (!currentPelangganId || !penjualanId) {
        alert('Data pengiriman tidak lengkap. Silakan pilih faktur terlebih dahulu.');
        return;
    }

    const btnKirim = document.getElementById('btnEksekusiKirimWhatsApp');
    const btnBatal = document.getElementById('btnBatalKirimWhatsApp');
    const btnBatalX = document.getElementById('btnBatalKirimWhatsAppX');
    const btnText = document.getElementById('btnEksekusiKirimWhatsAppText');
    const confirmError = document.getElementById('waConfirmError');
    const successBox = document.getElementById('whatsappPiutangSuccess');

    // Dapatkan CSRF token dari meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        if (confirmError) {
            confirmError.textContent = 'Token keamanan (CSRF) tidak ditemukan. Silakan muat ulang halaman.';
            confirmError.classList.remove('hidden');
        }
        return;
    }

    // Nonaktifkan tombol untuk mencegah klik ganda
    btnKirim.disabled = true;
    btnBatal.disabled = true;
    if (btnBatalX) btnBatalX.disabled = true;
    btnKirim.classList.add('opacity-60', 'cursor-not-allowed');
    btnText.textContent = 'Sedang mengirim...';

    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const sendUrlTemplate = @json(route('pelanggan.whatsapp-piutang.send', ['pelanggan' => '__ID__']));
    const sendUrl = sendUrlTemplate.replace('__ID__', encodeURIComponent(currentPelangganId));

    fetch(sendUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            penjualan_id: parseInt(penjualanId, 10)
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.status) {
            const pesanError = data.message || 'Gagal mengirim pesan WhatsApp.';
            throw new Error(pesanError);
        }

        return data;
    })
    .then(data => {
        // Pengiriman berhasil diterima gateway
        closeWhatsAppPiutangConfirm();

        if (successBox) {
            successBox.textContent = data.message || 'Pesan pengingat WhatsApp berhasil dikirim ke nomor member.';
            successBox.classList.remove('hidden');
        }

        // Segarkan pratinjau tagihan agar sisa dan status tetap sinkron
        setTimeout(() => {
            openWhatsAppPiutangPreview(currentPelangganId);
        }, 1200);
    })
    .catch(error => {
        // Pengiriman gagal
        if (confirmError) {
            confirmError.textContent = error.message || 'Terjadi kesalahan saat memproses pengiriman WhatsApp.';
            confirmError.classList.remove('hidden');
        }
    })
    .finally(() => {
        // Pulihkan tombol agar dapat digunakan kembali jika gagal
        btnKirim.disabled = false;
        btnBatal.disabled = false;
        if (btnBatalX) btnBatalX.disabled = false;
        btnKirim.classList.remove('opacity-60', 'cursor-not-allowed');
        btnText.textContent = 'Ya, Kirim Sekarang';
    });
}

/* ==========================================================
 * FITUR PENGIRIMAN PENGINGAT PIUTANG WHATSAPP MASSAL
 * ========================================================== */

let currentBulkRecipients = [];

function escapeHtmlWhatsApp(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getCheckedMemberBoxes() {
    return Array.from(document.querySelectorAll('.member-checkbox:checked'));
}

function getAllMemberBoxes() {
    return Array.from(document.querySelectorAll('.member-checkbox'));
}

function updateBulkActionBar() {
    const checkedBoxes = getCheckedMemberBoxes();
    const allBoxes = getAllMemberBoxes();
    const count = checkedBoxes.length;

    const bar = document.getElementById('bulkWhatsAppActionBar');
    const countText = document.getElementById('bulkSelectedCount');
    const totalPiutangText = document.getElementById('bulkSelectedTotalPiutang');
    const btnCountText = document.getElementById('bulkBtnCountText');
    const masterCheckbox = document.getElementById('checkAllMember');

    if (countText) countText.textContent = count;
    if (btnCountText) btnCountText.textContent = count;

    if (count > 0) {
        let total = 0;
        checkedBoxes.forEach(cb => {
            total += parseFloat(cb.getAttribute('data-sisa') || 0);
        });
        if (totalPiutangText) {
            totalPiutangText.textContent = formatRupiahWhatsApp(total);
        }

        if (bar) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        }
    } else {
        if (bar) {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    // Sinkronisasi status master checkbox di thead
    if (masterCheckbox && allBoxes.length > 0) {
        if (count === allBoxes.length) {
            masterCheckbox.checked = true;
            masterCheckbox.indeterminate = false;
        } else if (count > 0) {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = true;
        } else {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = false;
        }
    } else if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
}

function handleMemberCheckboxChange() {
    updateBulkActionBar();
}

function toggleSelectAllMember(master) {
    const allBoxes = getAllMemberBoxes();
    allBoxes.forEach(cb => {
        cb.checked = master.checked;
    });
    updateBulkActionBar();
}

function deselectAllMember() {
    const allBoxes = getAllMemberBoxes();
    allBoxes.forEach(cb => {
        cb.checked = false;
    });
    const masterCheckbox = document.getElementById('checkAllMember');
    if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
    updateBulkActionBar();
}

function closeBulkWhatsAppConfirmModal() {
    const modal = document.getElementById('bulkWhatsAppConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openBulkWhatsAppConfirmModal() {
    const checkedBoxes = getCheckedMemberBoxes();
    const ids = checkedBoxes.map(cb => parseInt(cb.getAttribute('data-id'), 10)).filter(Boolean);

    if (ids.length === 0) {
        alert('Silakan pilih minimal satu member terlebih dahulu.');
        return;
    }

    const modal = document.getElementById('bulkWhatsAppConfirmModal');
    const tableBody = document.getElementById('bulkConfirmTableBody');
    const previewText = document.getElementById('bulkConfirmPreviewText');
    const countText = document.getElementById('bulkConfirmCountText');
    const noticeBox = document.getElementById('bulkConfirmNotice');
    const btnStart = document.getElementById('btnStartBulkSend');

    if (!modal || !tableBody || !previewText || !countText) {
        alert('Komponen modal konfirmasi belum siap.');
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-gray-500">Memuat data penerima...</td></tr>`;
    previewText.textContent = 'Memuat pratinjau pesan...';
    if (noticeBox) noticeBox.classList.add('hidden');
    if (btnStart) btnStart.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(@json(route('pelanggan.whatsapp-piutang.bulk-preview')), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            pelanggan_ids: ids
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.status) {
            throw new Error(data.message || 'Gagal memuat pratinjau pengiriman massal.');
        }
        return data;
    })
    .then(data => {
        currentBulkRecipients = data.penerima || [];
        countText.textContent = currentBulkRecipients.length;

        tableBody.innerHTML = '';
        currentBulkRecipients.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';
            tr.innerHTML = `
                <td class="py-2 px-3 font-medium text-gray-800">${escapeHtmlWhatsApp(item.nama)}</td>
                <td class="py-2 px-3 font-mono text-green-700 font-semibold">${escapeHtmlWhatsApp(item.telepon || '-')}</td>
                <td class="py-2 px-3 text-right font-bold text-red-600">${escapeHtmlWhatsApp(item.sisa_piutang_formatted)}</td>
                <td class="py-2 px-3 text-center text-gray-600">${escapeHtmlWhatsApp(item.jatuh_tempo)}</td>
            `;
            tableBody.appendChild(tr);
        });

        if (currentBulkRecipients.length > 0) {
            previewText.textContent = currentBulkRecipients[0].pesan;
        }

        if (data.dikeluarkan && data.dikeluarkan.length > 0 && noticeBox) {
            noticeBox.innerHTML = `<strong>Perhatian:</strong> Sebanyak ${data.dikeluarkan.length} member tidak diikutsertakan karena data piutang sudah tidak valid atau lunas.`;
            noticeBox.classList.remove('hidden');
        }

        if (btnStart) btnStart.disabled = false;
    })
    .catch(error => {
        tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-red-600 font-semibold">${escapeHtmlWhatsApp(error.message)}</td></tr>`;
        previewText.textContent = '-';
        if (btnStart) btnStart.disabled = true;
    });
}

function closeBulkWhatsAppProgressModal() {
    const modal = document.getElementById('bulkWhatsAppProgressModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function closeBulkWhatsAppResultModal() {
    const modal = document.getElementById('bulkWhatsAppResultModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    // Muat ulang halaman agar sisa piutang, status, dan checkbox ter-refresh
    window.location.reload();
}

async function startBulkWhatsAppSending() {
    if (!currentBulkRecipients || currentBulkRecipients.length === 0) {
        alert('Tidak ada penerima yang siap diproses.');
        return;
    }

    closeBulkWhatsAppConfirmModal();

    const progressModal = document.getElementById('bulkWhatsAppProgressModal');
    const progressList = document.getElementById('bulkProgressList');
    const headerTotal = document.getElementById('bulkProgressHeaderTotal');
    const barFill = document.getElementById('bulkProgressBarFill');
    const doneText = document.getElementById('bulkProgressDoneText');
    const percentText = document.getElementById('bulkProgressPercentText');

    if (!progressModal || !progressList) return;

    const total = currentBulkRecipients.length;
    if (headerTotal) headerTotal.textContent = total;
    if (doneText) doneText.textContent = `0 dari ${total} selesai`;
    if (percentText) percentText.textContent = '0%';
    if (barFill) barFill.style.width = '0%';

    progressList.innerHTML = '';
    currentBulkRecipients.forEach(item => {
        const row = document.createElement('div');
        row.id = `bulkProgressItem_${item.id}`;
        row.className = 'flex items-center justify-between p-2.5 rounded-lg border border-gray-150 bg-gray-50 text-xs';
        row.innerHTML = `
            <div class="flex flex-col min-w-0 pr-2">
                <span class="font-bold text-gray-800 truncate">${escapeHtmlWhatsApp(item.nama)}</span>
                <span class="text-gray-500 font-mono text-[11px]">${escapeHtmlWhatsApp(item.telepon || '-')} &bull; ${escapeHtmlWhatsApp(item.sisa_piutang_formatted)}</span>
            </div>
            <div id="bulkProgressBadge_${item.id}" class="shrink-0">
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-200 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">
                    Menunggu
                </span>
            </div>
        `;
        progressList.appendChild(row);
    });

    progressModal.classList.remove('hidden');
    progressModal.classList.add('flex');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const sendUrlTemplate = @json(route('pelanggan.whatsapp-piutang.bulk-send', ['pelanggan' => '__ID__']));

    let successCount = 0;
    let failedCount = 0;

    // Loop sekuensial dengan jeda rate-limiting 1000ms
    for (let i = 0; i < total; i++) {
        const item = currentBulkRecipients[i];
        const badgeEl = document.getElementById(`bulkProgressBadge_${item.id}`);

        if (badgeEl) {
            badgeEl.innerHTML = `
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700 animate-pulse">
                    <svg class="animate-spin h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Mengirim...
                </span>
            `;
        }

        const itemRow = document.getElementById(`bulkProgressItem_${item.id}`);
        if (itemRow) {
            itemRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        const url = sendUrlTemplate.replace('__ID__', encodeURIComponent(item.id));

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({})
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.status) {
                successCount++;
                if (badgeEl) {
                    badgeEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Berhasil
                        </span>
                    `;
                }
            } else {
                failedCount++;
                const errMsg = data.message || 'Gagal terkirim';
                if (badgeEl) {
                    badgeEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-semibold text-red-700" title="${escapeHtmlWhatsApp(errMsg)}">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Gagal
                        </span>
                    `;
                }
            }
        } catch (err) {
            failedCount++;
            if (badgeEl) {
                badgeEl.innerHTML = `
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-semibold text-red-700" title="${escapeHtmlWhatsApp(err.message || 'Koneksi error')}">
                        Gagal
                    </span>
                `;
            }
        }

        const processed = i + 1;
        const percent = Math.round((processed / total) * 100);
        if (barFill) barFill.style.width = `${percent}%`;
        if (doneText) doneText.textContent = `${processed} dari ${total} selesai`;
        if (percentText) percentText.textContent = `${percent}%`;

        // Jeda rate limit 1 detik antar pengiriman
        if (i < total - 1) {
            await new Promise(r => setTimeout(r, 1000));
        }
    }

    // Jeda singkat sebelum menampilkan modal hasil
    await new Promise(r => setTimeout(r, 600));

    closeBulkWhatsAppProgressModal();

    const resultModal = document.getElementById('bulkWhatsAppResultModal');
    const resultTotal = document.getElementById('bulkResultTotal');
    const resultSuccess = document.getElementById('bulkResultSuccess');
    const resultFailed = document.getElementById('bulkResultFailed');

    if (resultTotal) resultTotal.textContent = total;
    if (resultSuccess) resultSuccess.textContent = successCount;
    if (resultFailed) resultFailed.textContent = failedCount;

    if (resultModal) {
        resultModal.classList.remove('hidden');
        resultModal.classList.add('flex');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updateBulkActionBar();
});
</script>