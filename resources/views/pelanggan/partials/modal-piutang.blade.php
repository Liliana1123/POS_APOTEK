{{-- Modal Pembayaran Piutang --}}
<div id="piutangPaymentModal"
    class="piutang-modal-backdrop hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">

    {{-- Elements preserved for JS state management --}}
    <div class="hidden" style="display: none !important;">
        <span id="piutangMemberId">-</span>
        <span id="piutangMemberNama">-</span>
        <span id="piutangMemberTotal">Rp 0</span>
        <span id="piutangMemberJatuhTempo">-</span>
        <span id="piutangKeteranganCount">0</span>
    </div>

    {{-- Modal Box Container --}}
    <div class="piutang-modal-box">

        {{-- Header --}}
        <div class="piutang-header">
            <div class="piutang-header-left">
                <div class="piutang-user-icon-box">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="piutang-title">Pembayaran Piutang</h3>
                    <p class="piutang-subtitle">Kelola pembayaran piutang member</p>
                </div>
            </div>

            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="piutang-close-btn"
                title="Tutup">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="piutang-body">

            {{-- 2 Columns: Invoice on Left, Payment Form on Right --}}
            <div class="piutang-columns">

                {{-- Left Column --}}
                <div class="piutang-col-left">

                    {{-- Invoice Selector --}}
                    <div class="piutang-select-wrapper">
                        <select id="piutangPenjualanId" class="piutang-invoice-select">
                            <option value="">Pilih invoice piutang</option>
                        </select>
                    </div>

                    {{-- Detail Invoice Card --}}
                    <div id="piutangInvoiceDetail" class="piutang-invoice-card hidden">
                        <div class="piutang-card-header">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="piutang-card-title">Detail Invoice</span>
                        </div>

                        <div class="piutang-detail-grid">
                            <div class="piutang-detail-item">
                                <span class="piutang-detail-label">No. Invoice</span>
                                <span id="piutangNoFaktur" class="piutang-detail-val">-</span>
                            </div>

                            <div class="piutang-detail-item">
                                <span class="piutang-detail-label">Jatuh Tempo</span>
                                <span id="piutangInvoiceJatuhTempo" class="piutang-detail-val val-subtle">-</span>
                            </div>

                            <div class="piutang-detail-item">
                                <span class="piutang-detail-label">Total Belanja</span>
                                <span id="piutangTotalBelanja" class="piutang-detail-val val-big-navy">Rp 0</span>
                            </div>

                            <div class="piutang-detail-item">
                                <span class="piutang-detail-label">Total Dibayar</span>
                                <span id="piutangTotalDibayar" class="piutang-detail-val val-green">Rp 0</span>
                            </div>

                            <div class="piutang-detail-item" style="grid-column: span 2;">
                                <span class="piutang-detail-label">Sisa Piutang</span>
                                <span id="piutangSisa" class="piutang-detail-val val-red">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Payment Form --}}
                <div class="piutang-col-right">
                    <form id="piutangPaymentForm" onsubmit="return false;" class="piutang-payment-form hidden">

                        {{-- Tanggal Bayar --}}
                        <div class="piutang-form-field">
                            <label for="piutangTanggalBayar" class="piutang-form-label">
                                Tanggal Bayar
                            </label>
                            <input type="date"
                                id="piutangTanggalBayar"
                                class="piutang-input">
                        </div>

                        {{-- Jumlah Pembayaran --}}
                        <div class="piutang-form-field">
                            <label for="piutangJumlah" class="piutang-form-label">
                                Jumlah Pembayaran
                            </label>
                            <input type="number"
                                id="piutangJumlah"
                                min="0.01"
                                step="0.01"
                                class="piutang-input"
                                placeholder="Masukkan jumlah pembayaran">
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="piutang-form-field">
                            <label for="piutangMetodePembayaran" class="piutang-form-label">
                                Metode Pembayaran
                            </label>
                            <select id="piutangMetodePembayaran"
                                name="metode_pembayaran"
                                required
                                class="piutang-select">
                                <option value="">Pilih metode pembayaran</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Debit</option>
                            </select>
                        </div>

                        {{-- Keterangan --}}
                        <div class="piutang-form-field">
                            <label for="piutangKeterangan" class="piutang-form-label">
                                Keterangan <span class="piutang-optional-text">(Opsional)</span>
                            </label>
                            <input type="text"
                                id="piutangKeterangan"
                                maxlength="255"
                                class="piutang-input"
                                placeholder="Masukkan keterangan pembayaran...">
                        </div>

                        {{-- Tombol Simpan --}}
                        <button type="button"
                            id="btnSimpanPembayaranPiutang"
                            class="piutang-submit-btn">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="5" width="20" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="2" y1="10" x2="22" y2="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Simpan Pembayaran</span>
                        </button>
                    </form>
                </div>

            </div>

            {{-- Riwayat Pembayaran --}}
            <div id="piutangHistory" class="piutang-history-card hidden">
                <div class="piutang-card-header">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="12 7 12 12 15 15" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="piutang-card-title">Riwayat Pembayaran</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="piutang-history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Metode Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody id="piutangHistoryBody">
                            <tr>
                                <td colspan="4" class="px-4 py-5 text-center text-slate-400">
                                    Belum ada pembayaran.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="piutang-footer">
            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="piutang-close-footer-btn">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- Scoped Styles for Pembayaran Piutang Modal --}}
<style>
#piutangPaymentModal {
    font-family: inherit;
}

#piutangPaymentModal.piutang-modal-backdrop {
    background-color: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
}

#piutangPaymentModal .piutang-modal-box {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.18), 0 0 1px 1px rgba(15, 23, 42, 0.06);
    width: 92vw;
    max-width: 840px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

/* Header */
#piutangPaymentModal .piutang-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 28px 18px 28px;
    flex-shrink: 0;
}

#piutangPaymentModal .piutang-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

#piutangPaymentModal .piutang-user-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background-color: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    flex-shrink: 0;
}

#piutangPaymentModal .piutang-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    margin: 0;
}

#piutangPaymentModal .piutang-subtitle {
    font-size: 13px;
    color: #64748b;
    margin: 3px 0 0 0;
}

#piutangPaymentModal .piutang-close-btn {
    color: #94a3b8;
    background: transparent;
    border: none;
    border-radius: 8px;
    padding: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

#piutangPaymentModal .piutang-close-btn:hover {
    color: #475569;
    background-color: #f1f5f9;
}

/* Body */
#piutangPaymentModal .piutang-body {
    flex: 1 1 auto;
    overflow-y: auto;
    padding: 0 28px 20px 28px;
}

/* 2-Column Grid */
#piutangPaymentModal .piutang-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}

/* Left Column */
#piutangPaymentModal .piutang-select-wrapper {
    margin-bottom: 14px;
}

#piutangPaymentModal .piutang-invoice-select {
    width: 100%;
    height: 42px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 36px 8px 14px;
    font-size: 13.5px;
    font-weight: 500;
    color: #1e293b;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 15px 15px;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

#piutangPaymentModal .piutang-invoice-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Detail Invoice Card */
#piutangPaymentModal .piutang-invoice-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px 18px 20px;
    background: #ffffff;
}

#piutangPaymentModal .piutang-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}

#piutangPaymentModal .piutang-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
}

#piutangPaymentModal .piutang-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    row-gap: 14px;
    column-gap: 16px;
}

#piutangPaymentModal .piutang-detail-item {
    display: flex;
    flex-direction: column;
}

#piutangPaymentModal .piutang-detail-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 400;
    margin-bottom: 3px;
}

#piutangPaymentModal .piutang-detail-val {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    word-break: break-word;
}

#piutangPaymentModal .piutang-detail-val.val-subtle {
    font-weight: 500;
    color: #475569;
}

#piutangPaymentModal .piutang-detail-val.val-green {
    font-size: 15px;
    font-weight: 700;
    color: #16a34a;
}

#piutangPaymentModal .piutang-detail-val.val-red {
    font-size: 16px;
    font-weight: 700;
    color: #ef4444;
}

#piutangPaymentModal .piutang-detail-val.val-big-navy {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}

/* Right Column (Form) */
#piutangPaymentModal .piutang-payment-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

#piutangPaymentModal .piutang-form-field {
    display: flex;
    flex-direction: column;
}

#piutangPaymentModal .piutang-form-label {
    font-size: 12.5px;
    font-weight: 500;
    color: #334155;
    margin-bottom: 4px;
}

#piutangPaymentModal .piutang-optional-text {
    font-size: 11.5px;
    font-weight: 400;
    color: #94a3b8;
}

#piutangPaymentModal .piutang-input,
#piutangPaymentModal .piutang-select {
    width: 100%;
    height: 40px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    color: #0f172a;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

#piutangPaymentModal .piutang-input::placeholder {
    color: #94a3b8;
}

#piutangPaymentModal .piutang-input:focus,
#piutangPaymentModal .piutang-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

#piutangPaymentModal .piutang-select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px 14px;
    padding-right: 32px;
}

#piutangPaymentModal .piutang-submit-btn {
    width: 100%;
    height: 42px;
    margin-top: 2px;
    background-color: #1d6fee;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

#piutangPaymentModal .piutang-submit-btn:hover {
    background-color: #185ecc;
}

#piutangPaymentModal .piutang-submit-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Riwayat Pembayaran Card */
#piutangPaymentModal .piutang-history-card {
    margin-top: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    background: #ffffff;
}

#piutangPaymentModal .piutang-history-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}

#piutangPaymentModal .piutang-history-table th {
    background-color: #f8fafc;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    padding: 9px 14px;
    text-align: left;
    white-space: nowrap;
}

#piutangPaymentModal .piutang-history-table th:first-child {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

#piutangPaymentModal .piutang-history-table th:last-child {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

#piutangPaymentModal .piutang-history-table td {
    padding: 11px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 13px;
}

#piutangPaymentModal .piutang-history-table tr:last-child td {
    border-bottom: none;
}

#piutangPaymentModal .text-green-600 {
    color: #16a34a !important;
    font-weight: 600 !important;
}

/* Footer */
#piutangPaymentModal .piutang-footer {
    display: flex;
    justify-content: flex-end;
    padding: 14px 28px 22px 28px;
    flex-shrink: 0;
}

#piutangPaymentModal .piutang-close-footer-btn {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px 24px;
    font-size: 13.5px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}

#piutangPaymentModal .piutang-close-footer-btn:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}

/* Hidden override */
#piutangPaymentModal .hidden {
    display: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    #piutangPaymentModal .piutang-modal-box {
        width: 95vw;
        max-height: 94vh;
    }
    #piutangPaymentModal .piutang-header {
        padding: 18px 20px 14px 20px;
    }
    #piutangPaymentModal .piutang-body {
        padding: 0 20px 16px 20px;
    }
    #piutangPaymentModal .piutang-footer {
        padding: 12px 20px 18px 20px;
    }
    #piutangPaymentModal .piutang-columns {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}
</style>

<script>
let currentPiutangPelangganId = null;
let currentPiutangPenjualanId = null;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(Number(angka || 0)).toLocaleString('id-ID');
}

// Format tanggal riwayat pembayaran menjadi DD/MM/YYYY
function formatTanggalRiwayat(tanggal) {
    if (!tanggal) return '-';

    // Ambil tanggal saja agar tidak bergeser karena zona waktu
    const tanggalSaja = String(tanggal).substring(0, 10);
    const bagian = tanggalSaja.split('-');

    if (bagian.length !== 3) return tanggal;

    const [tahun, bulan, hari] = bagian;

    return `${hari}/${bulan}/${tahun}`;
}

function openPiutangPaymentModal(pelangganId) {
    currentPiutangPelangganId = pelangganId;
    currentPiutangPenjualanId = null;

    const modal = document.getElementById('piutangPaymentModal');
    const select = document.getElementById('piutangPenjualanId');

    document.getElementById('piutangMemberId').textContent = '-';
    document.getElementById('piutangMemberNama').textContent = '-';
    document.getElementById('piutangMemberTotal').textContent = 'Rp 0';
    document.getElementById('piutangMemberJatuhTempo').textContent = '-';

    document.getElementById('piutangInvoiceDetail').classList.add('hidden');
    document.getElementById('piutangPaymentForm').classList.add('hidden');
    document.getElementById('piutangHistory').classList.add('hidden');

    select.innerHTML = '<option value="">Memuat invoice piutang...</option>';
    modal.classList.remove('hidden');

    fetch(`{{ url('pelanggan') }}/${pelangganId}/piutang`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal mengambil data piutang.');
        }
        return response.json();
    })
    .then(data => {
        const pelanggan = data.pelanggan || {};
        const penjualans = Array.isArray(data.penjualans) ? data.penjualans : [];

        document.getElementById('piutangMemberId').textContent =
            pelanggan.member_id || '-';

        document.getElementById('piutangMemberNama').textContent =
            pelanggan.nama || '-';

        document.getElementById('piutangMemberJatuhTempo').textContent =
            pelanggan.jatuh_tempo || '-';

        const totalPiutang = penjualans.reduce(
            (total, item) => total + Number(item.sisa_piutang || 0),
            0
        );

        document.getElementById('piutangMemberTotal').textContent =
            formatRupiah(totalPiutang);

        select.innerHTML = '<option value="">Pilih invoice piutang</option>';

        penjualans.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;

            // Ambil tanggal jatuh tempo dari data invoice
            let jatuhTempo =
                item.due_date ||
                item.tanggal_jatuh_tempo ||
                item.due_date_formatted ||
                '';

            // Ubah format tanggal menjadi DD/MM/YY
            if (/^\d{4}-\d{2}-\d{2}$/.test(jatuhTempo)) {
                const [tahun, bulan, tanggal] = jatuhTempo.split('-');
                jatuhTempo = `${tanggal}/${bulan}/${tahun.slice(-2)}`;
            } else if (/^\d{2}-\d{2}-\d{4}$/.test(jatuhTempo)) {
                const [tanggal, bulan, tahun] = jatuhTempo.split('-');
                jatuhTempo = `${tanggal}/${bulan}/${tahun.slice(-2)}`;
            }

            // Tambahkan jatuh tempo lengkap ke pilihan invoice
            const jtText = jatuhTempo && jatuhTempo !== '-'
                ? ` (Jatuh Tempo: ${jatuhTempo})`
                : '';

            option.textContent =
                `${item.no_faktur} — ${formatRupiah(item.sisa_piutang)}${jtText}`;

            select.appendChild(option);
        });

        if (penjualans.length > 0) {
            select.value = penjualans[0].id;
            select.dispatchEvent(new Event('change'));
        } else {
            select.innerHTML =
                '<option value="">Tidak ada piutang yang belum lunas</option>';
        }
    })
    .catch(error => {
        console.error(error);
        select.innerHTML =
            '<option value="">Gagal memuat invoice</option>';
        alert('Data piutang gagal dimuat.');
    });
}

function closePiutangPaymentModal() {
    const modal = document.getElementById('piutangPaymentModal');

    if (modal) {
        modal.classList.add('hidden');
    }

    currentPiutangPelangganId = null;
    currentPiutangPenjualanId = null;
}

const piutangInvoiceSelect = document.getElementById('piutangPenjualanId');

if (piutangInvoiceSelect) {
    piutangInvoiceSelect.addEventListener('change', function () {
        const penjualanId = this.value;

        if (!penjualanId) {
            document.getElementById('piutangInvoiceDetail').classList.add('hidden');
            document.getElementById('piutangPaymentForm').classList.add('hidden');
            document.getElementById('piutangHistory').classList.add('hidden');
            return;
        }

        currentPiutangPenjualanId = penjualanId;

        fetch(
            `{{ url('penjualan') }}/${penjualanId}/piutang/payment-form`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil detail invoice.');
            }
            return response.json();
        })
        .then(data => {
            const penjualan = data.penjualan || {};

            document.getElementById('piutangNoFaktur').textContent =
                penjualan.no_faktur || '-';

            document.getElementById('piutangTotalBelanja').textContent =
                formatRupiah(penjualan.total);

            document.getElementById('piutangTotalDibayar').textContent =
                formatRupiah(data.total_dibayar);

            document.getElementById('piutangSisa').textContent =
                formatRupiah(data.sisa_piutang);

            document.getElementById('piutangInvoiceJatuhTempo').textContent =
                penjualan.due_date_formatted || '-';

            document.getElementById('piutangInvoiceDetail')
                .classList.remove('hidden');

            const tanggalInput =
                document.getElementById('piutangTanggalBayar');

            tanggalInput.value =
                new Date().toISOString().split('T')[0];

            document.getElementById('piutangJumlah').value = '';
            document.getElementById('piutangKeterangan').value = '';

            const countElement =
                document.getElementById('piutangKeteranganCount');

            if (countElement) {
                countElement.textContent = '0';
            }

            document.getElementById('piutangPaymentForm')
                .classList.remove('hidden');

            const historyBody =
                document.getElementById('piutangHistoryBody');

            historyBody.innerHTML = '';

            const riwayat = Array.isArray(data.riwayat)
                ? data.riwayat
                : [];

            if (riwayat.length > 0) {
                riwayat.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'border-b border-gray-100';

                    row.innerHTML = `
                        <td class="whitespace-nowrap px-4 py-3">
                           ${formatTanggalRiwayat(item.tanggal_bayar)}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-green-600">
                            ${formatRupiah(item.jumlah)}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            ${item.keterangan || '-'}
                        </td>
                         <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                            ${item.metode_pembayaran || '-'}
                        </td>
                    `;

                    historyBody.appendChild(row);
                });
            } else {
                historyBody.innerHTML = `
                    <tr>
                        <td colspan="4"
                            class="px-4 py-5 text-center text-slate-400">
                            Belum ada pembayaran.
                        </td>
                    </tr>
                `;
            }

            document.getElementById('piutangHistory')
                .classList.remove('hidden');
        })
        .catch(error => {
            console.error(error);
            alert('Detail invoice gagal dimuat.');
        });
    });
}

const btnSimpanPembayaranPiutang =
    document.getElementById('btnSimpanPembayaranPiutang');

if (btnSimpanPembayaranPiutang) {
    btnSimpanPembayaranPiutang.addEventListener('click', function () {
        if (!currentPiutangPenjualanId) {
            alert('Pilih invoice piutang terlebih dahulu.');
            return;
        }

        const tanggalBayar =
            document.getElementById('piutangTanggalBayar').value;

        const jumlah =
            document.getElementById('piutangJumlah').value;

        const metodePembayaran =
            document.getElementById('piutangMetodePembayaran').value;

        const keterangan =
            document.getElementById('piutangKeterangan').value.trim();

        if (!tanggalBayar) {
            alert('Tanggal pembayaran wajib diisi.');
            return;
        }

        if (!jumlah || Number(jumlah) <= 0) {
            alert('Jumlah pembayaran harus lebih dari 0.');
            return;
        }

        if (!['cash', 'qris', 'debit'].includes(metodePembayaran)) {
            alert('Pilih metode pembayaran yang valid.');
            return;
        }

        const button = this;
        button.disabled = true;
        button.textContent = 'Menyimpan...';

        fetch(
            `{{ url('penjualan') }}/${currentPiutangPenjualanId}/piutang/payments`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    tanggal_bayar: tanggalBayar,
                    jumlah: jumlah,
                    keterangan: keterangan,
                    metode_pembayaran: metodePembayaran
                })
            }
        )
        .then(async response => {
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const message =
                    data.message ||
                    data.errors?.jumlah?.[0] ||
                    data.errors?.tanggal_bayar?.[0] ||
                    data.errors?.metode_pembayaran?.[0] ||
                    'Pembayaran gagal disimpan.';

                throw new Error(message);
            }

            return data;
        })
        .then(data => {
            alert(data.message || 'Pembayaran piutang berhasil disimpan.');

            if (Number(data.sisa_piutang || 0) <= 0) {
                window.location.reload();
                return;
            }

            piutangInvoiceSelect.dispatchEvent(new Event('change'));
        })
        .catch(error => {
            console.error(error);
            alert(error.message || 'Pembayaran gagal disimpan.');
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = 'Simpan Pembayaran';
        });
    });
}

/* Counter keterangan */
const piutangKeterangan =
    document.getElementById('piutangKeterangan');

if (piutangKeterangan) {
    piutangKeterangan.addEventListener('input', function () {
        const counter =
            document.getElementById('piutangKeteranganCount');

        if (counter) {
            counter.textContent = this.value.length;
        }
    });
}
</script>


