<!-- Modal Kartu Member -->
<div id="modal-card" class="modal-backdrop-custom hidden">
    <div class="modal-container-custom max-w-sm w-full mx-4">
        <!-- Print Area -->
        <div id="print-area" class="border-2 border-blue-600 rounded-xl p-5 bg-gradient-to-br from-blue-50 to-white text-blue-900 w-full shadow-sm mx-auto font-sans">
            <div class="flex justify-between items-center border-b pb-2.5 mb-4">
                <span class="font-bold text-sm tracking-wider uppercase text-blue-800">Apotek Membership</span>
                <span
                    id="card-status-member"
                    class="inline-flex items-center rounded-full px-2 py-1 text-[8px] tracking-widest font-bold uppercase bg-green-100 text-green-700"
                >
                    MEMBER PELANGGAN TETAP
                </span>
            </div>
            <div class="space-y-3 text-xs mb-4">
                <div>
                    <span class="text-[9px] text-gray-400 block font-semibold uppercase tracking-wide">Nama Pemegang</span>
                    <strong id="card-name" class="text-sm text-gray-800 font-bold"></strong>
                </div>
                <div>
                    <span class="text-[9px] text-gray-400 block font-semibold uppercase tracking-wide">Nomor Kartu / ID</span>
                    <strong id="card-id" class="text-sm text-blue-700 font-mono font-bold tracking-wide"></strong>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-[9px] text-gray-400 block font-semibold uppercase tracking-wide">Benefit Diskon</span>
                        <strong id="card-discount" class="text-sm text-green-600 font-bold uppercase tracking-wide">
                            DISKON {{ config('pos.diskon_member', 10) }}%
                        </strong>
                    </div>
                </div>
                    <div class="flex flex-col items-center border-t pt-4">
                        <div class="relative w-32 h-32 flex items-center justify-center">
                            <img id="card-qrcode" class="border p-1.5 bg-white w-32 h-32 rounded-lg shadow-sm" alt="QR Code">
                            <div id="card-qrcode-spinner" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg hidden">
                                <span class="text-[10px] text-gray-500 font-medium">Memuat QR...</span>
                            </div>
                        </div>
                    </div>
                </div> <!-- Penutup space-y-3 -->
            </div> <!-- Penutup print-area -->

            <!-- Error State & Retry (Di luar print-area agar tidak tercetak) -->
            <div id="card-qr-status-container" class="mt-2 text-center hidden">
                <p id="card-qr-error" class="text-[11px] text-red-600 font-semibold mb-1"></p>
                <button type="button" id="btn-retry-qr" class="text-xs text-blue-600 hover:text-blue-800 font-semibold underline">
                    Coba lagi
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="modal-footer-custom mt-4">
                <button type="button" onclick="closeCardModal()" class="btn-secondary">Tutup</button>
                <button type="button" id="btn-print-card" onclick="printCard()" class="btn-primary" disabled>Cetak Kartu</button>
            </div>
        </div> <!-- Penutup modal-container-custom -->
    </div> <!-- Penutup modal-card -->

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #modal-card, #modal-card #print-area, #modal-card #print-area * {
        visibility: visible !important;
    }
    #modal-card {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #print-area {
        margin: 20px auto !important;
        box-shadow: none !important;
    }
    .modal-footer-custom, #card-qr-status-container {
        display: none !important;
    }
}
</style>

<script>
let currentCardRequestId = 0;
let isCardQrReady = false;
let currentMemberId = '';

function setPrintButtonState(enabled, text) {
    const btn = document.getElementById('btn-print-card');
    if (!btn) return;
    btn.disabled = !enabled;
    if (enabled) {
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    if (text) {
        btn.textContent = text;
    }
}

function generateCardQr(memberId, requestId) {
    isCardQrReady = false;
    setPrintButtonState(false, 'Menyiapkan QR...');

    const img = document.getElementById('card-qrcode');
    const spinner = document.getElementById('card-qrcode-spinner');
    const errorContainer = document.getElementById('card-qr-status-container');
    const errorText = document.getElementById('card-qr-error');

    if (errorContainer) errorContainer.classList.add('hidden');
    if (spinner) spinner.classList.remove('hidden');

    if (img) {
        img.onload = null;
        img.onerror = null;
        img.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128"/>';
    }

    if (!memberId) {
        if (requestId !== currentCardRequestId) return;
        if (spinner) spinner.classList.add('hidden');
        if (errorContainer) errorContainer.classList.remove('hidden');
        if (errorText) errorText.textContent = 'Nomor kartu / ID pelanggan tidak tersedia.';
        setPrintButtonState(false, 'Cetak Kartu');
        return;
    }

    const loadFn = window.loadQRCode || (window.QRCode ? function () { return Promise.resolve(window.QRCode); } : null);
    if (!loadFn) {
        if (requestId !== currentCardRequestId) return;
        if (spinner) spinner.classList.add('hidden');
        if (errorContainer) errorContainer.classList.remove('hidden');
        if (errorText) errorText.textContent = 'Library QR Code tidak tersedia.';
        setPrintButtonState(false, 'Cetak Kartu');
        return;
    }

    loadFn()
        .then(function (qr) {
            if (requestId !== currentCardRequestId) return;
            return new Promise(function (resolve, reject) {
                qr.toDataURL(memberId, { width: 128, margin: 1 }, function (err, url) {
                    if (err) reject(err);
                    else resolve(url);
                });
            });
        })
        .then(function (dataUrl) {
            if (requestId !== currentCardRequestId || !dataUrl) return;

            img.onload = function () {
                if (requestId !== currentCardRequestId) return;
                isCardQrReady = true;
                if (spinner) spinner.classList.add('hidden');
                if (errorContainer) errorContainer.classList.add('hidden');
                setPrintButtonState(true, 'Cetak Kartu');
            };

            img.onerror = function () {
                if (requestId !== currentCardRequestId) return;
                isCardQrReady = false;
                if (spinner) spinner.classList.add('hidden');
                if (errorContainer) errorContainer.classList.remove('hidden');
                if (errorText) errorText.textContent = 'Gambar QR Code gagal dimuat.';
                setPrintButtonState(false, 'Cetak Kartu');
            };

            img.src = dataUrl;
        })
        .catch(function (err) {
            if (requestId !== currentCardRequestId) return;
            isCardQrReady = false;
            if (spinner) spinner.classList.add('hidden');
            if (errorContainer) errorContainer.classList.remove('hidden');
            if (errorText) errorText.textContent = 'Gagal membuat QR Code (' + (err.message || 'Error jaringan/modul') + ').';
            setPrintButtonState(false, 'Cetak Kartu');
        });
}

function openCardModal(nama, memberId, totalTransaksi, statusMember, customDiscount) {
    currentMemberId = memberId || '';
    const requestId = ++currentCardRequestId;

    document.getElementById('card-name').textContent = nama || '-';
    document.getElementById('card-id').textContent = memberId || '-';

    const discountVal = (customDiscount !== null && customDiscount !== undefined && customDiscount !== '')
        ? parseFloat(customDiscount)
        : {{ config('pos.diskon_member', 10) }};
    document.getElementById('card-discount').textContent = `DISKON ${discountVal}%`;

    const statusElement = document.getElementById('card-status-member');
    statusElement.textContent = statusMember || 'Member Pelanggan Tetap';

    statusElement.classList.remove(
        'bg-green-100',
        'text-green-700',
        'bg-purple-100',
        'text-purple-700',
        'bg-yellow-100',
        'text-yellow-700'
    );

    if (statusMember === 'Member Keluarga Nakes') {
        statusElement.classList.add('bg-purple-100', 'text-purple-700');
    } else if (statusMember === 'Member Only') {
        statusElement.classList.add('bg-yellow-100', 'text-yellow-700');
    } else {
        statusElement.classList.add('bg-green-100', 'text-green-700');
    }

    generateCardQr(currentMemberId, requestId);

    document.getElementById('modal-card').classList.remove('hidden');
}

function closeCardModal() {
    const modal = document.getElementById('modal-card');
    if (modal) modal.classList.add('hidden');
}

function printCard() {
    if (!isCardQrReady) {
        return;
    }
    window.print();
}

document.addEventListener('DOMContentLoaded', function () {
    const retryBtn = document.getElementById('btn-retry-qr');
    if (retryBtn) {
        retryBtn.addEventListener('click', function () {
            const reqId = ++currentCardRequestId;
            generateCardQr(currentMemberId, reqId);
        });
    }
});
</script>
