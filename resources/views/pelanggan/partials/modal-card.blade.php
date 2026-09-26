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
                        <strong id="card-discount" class="text-sm text-green-600 font-bold uppercase tracking-wide">BENEFIT DISKON {{ config('pos.diskon_member', 10) }}%</strong>
                    </div>
                </div>
            </div>
            <div class="flex justify-center border-t pt-4">
                <img id="card-qrcode" class="border p-1.5 bg-white w-32 h-32 rounded-lg shadow-sm" alt="QR Code">
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="modal-footer-custom mt-4">
            <button onclick="closeCardModal()" class="btn-secondary">Tutup</button>
            <button onclick="printCard()" class="btn-primary">Cetak Kartu</button>
        </div>
    </div>
</div>

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
    .modal-footer-custom {
        display: none !important;
    }
}
</style>

<script>
function openCardModal(nama, memberId, totalTransaksi, statusMember, customDiscount) {
    document.getElementById('card-name').textContent = nama || '-';
    document.getElementById('card-id').textContent = memberId || '-';

    const discountVal = (customDiscount !== null && customDiscount !== undefined && customDiscount !== '')
        ? parseFloat(customDiscount)
        : {{ config('pos.diskon_member', 10) }};
    document.getElementById('card-discount').textContent = `BENEFIT DISKON ${discountVal}%`;

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

    if (window.QRCode) {
        window.QRCode.toDataURL(
            memberId || '',
            { width: 128, margin: 1 },
            function (err, url) {
                if (!err) {
                    document.getElementById('card-qrcode').src = url;
                }
            }
        );
    }

    document.getElementById('modal-card').classList.remove('hidden');
}

function closeCardModal() {
    const modal = document.getElementById('modal-card');
    if (modal) modal.classList.add('hidden');
}

function printCard() {
    window.print();
}
</script>
