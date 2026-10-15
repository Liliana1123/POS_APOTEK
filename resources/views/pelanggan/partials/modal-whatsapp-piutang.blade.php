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
</script>