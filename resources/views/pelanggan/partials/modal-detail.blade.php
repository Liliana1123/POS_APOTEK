<<<<<<< HEAD
<!-- Modal Detail Pelanggan -->
<x-modal
    id="modal-detail-pelanggan"
    size="w-full max-w-4xl"
    close-action="closeDetailModal()">

    <x-slot:header>
        <div class="flex items-start gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                <x-heroicon-o-user class="w-5 h-5" />
            </div>

            <div>
                <h3 class="text-base font-semibold text-gray-800">
                    Detail Pelanggan / Member
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Informasi lengkap pelanggan dan riwayat transaksi.
                </p>
            </div>
=======
<!-- Modal Detail Pelanggan / Member -->
<div id="modal-detail-pelanggan"
    class="modal-backdrop-custom hidden fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 sm:p-6 transition-opacity duration-200"
    onclick="handleDetailBackdropClick(event)">

    <div class="modal-detail-card" onclick="event.stopPropagation()">

        <!-- HEADER (Tetap terlihat di atas) -->
        <div class="modal-header-fixed flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shadow-2xs border border-blue-100/60">
                    <x-heroicon-o-user class="w-5 h-5" />
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">
                        Detail Pelanggan / Member
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Informasi lengkap pelanggan dan riwayat transaksi.
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="closeDetailModal()"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition"
                title="Tutup (Esc)"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
>>>>>>> origin/feriena
        </div>
    </x-slot:header>

<<<<<<< HEAD
    <!-- CONTENT -->
    <div class="space-y-4">

        <!-- INFORMASI PELANGGAN + RINGKASAN -->
        <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] gap-5 p-1">

            <!-- KIRI -->
            <div class="space-y-4">

                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <x-heroicon-o-user class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-medium text-gray-400">Nama</div>
                            <div id="detail-nama" class="mt-0.5 text-sm font-semibold text-blue-600 break-words">-</div>
                        </div>
                    </div>
=======
        <!-- CONTENT (Scrollable jika layar kecil, fixed layout di desktop) -->
        <div class="modal-detail-scrollable">

            <div class="modal-layout-grid">

                <!-- PANEL PROFIL (KIRI) -->
                <div class="modal-profile-panel">
                    <div class="modal-profile-card">

                        <!-- Profil Utama: Avatar, Nama, Status, ID -->
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 shadow-2xs ring-3 ring-blue-50">
                                <x-heroicon-o-user class="w-7 h-7" />
                            </div>
>>>>>>> origin/feriena

                            <div class="min-w-0 flex-1">
                                <h4 id="detail-nama" class="text-base font-bold text-gray-900 break-words leading-tight">
                                    -
                                </h4>

                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <span id="detail-status-member-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                        Member Pelanggan Tetap
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <div class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">ID Pelanggan</div>
                                    <div id="detail-member-id" class="font-mono text-xs font-bold text-blue-600 mt-0.5">
                                        -
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Telepon & Alamat -->
                        <div class="modal-profile-contact space-y-3" style="border-top: 1px solid #f1f5f9; padding-top: 14px;">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                    <x-heroicon-o-phone class="w-4 h-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] font-medium text-gray-400">No. Telp</div>
                                    <div id="detail-telepon" class="text-xs font-bold text-gray-800 break-words">-</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 mt-0.5">
                                    <x-heroicon-o-map-pin class="w-4 h-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] font-medium text-gray-400">Alamat</div>
                                    <div id="detail-alamat" class="text-xs font-semibold text-gray-800 break-words whitespace-pre-line">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Benefit Diskon (Ikon > Dihilangkan) -->
                        <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-3 flex items-center gap-2.5 transition hover:bg-blue-50">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-blue-600 shadow-2xs font-bold text-xs">
                                %
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[10px] font-medium text-gray-500">Benefit Diskon</div>
                                <div id="detail-benefit-diskon" class="text-xs font-bold text-emerald-600 truncate">
                                    Diskon 10%
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Detail Profil -->
                        <div class="pt-3 border-t border-gray-100 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 font-medium text-[11px]">Status Member</span>
                                <span id="detail-status-member-pill" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70 text-right">
                                    Member Pelanggan Tetap
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 font-medium text-[11px]">Member Sejak</span>
                                <span id="detail-member-since" class="text-gray-800 font-semibold text-right">-</span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 font-medium text-[11px]">Tanggal Lahir</span>
                                <span id="detail-tanggal-lahir" class="text-gray-800 font-semibold text-right">-</span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 font-medium text-[11px]">Saldo Piutang</span>
                                <span id="detail-saldo-piutang-text" class="text-gray-800 font-bold text-right">Rp 0</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- PANEL RINGKASAN & RIWAYAT (KANAN) -->
                <div class="modal-right-panel">

                    <!-- Ringkasan Transaksi (4 Cards) -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 tracking-tight">
                            Ringkasan Transaksi
                        </h4>

                        <div class="modal-summary-grid">

                            <!-- Total Belanja -->
                            <div class="modal-summary-card card-total-belanja">
                                <div>
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100/80 text-blue-600 mb-2">
                                        <x-heroicon-o-shopping-cart class="w-4 h-4" />
                                    </div>
                                    <div class="text-[11px] font-medium text-gray-500">Total Belanja</div>
                                    <div id="detail-total-belanja" class="text-sm lg:text-base font-bold text-blue-700 mt-0.5 tracking-tight truncate">
                                        Rp 0
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-2 font-normal truncate">Total seluruh transaksi</div>
                            </div>

                            <!-- Total Diskon -->
                            <div class="modal-summary-card card-total-diskon">
                                <div>
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100/80 text-emerald-600 mb-2 font-bold text-xs">
                                        %
                                    </div>
                                    <div class="text-[11px] font-medium text-gray-500">Total Diskon</div>
                                    <div id="detail-total-diskon" class="text-sm lg:text-base font-bold text-emerald-700 mt-0.5 tracking-tight truncate">
                                        Rp 0
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-2 font-normal truncate">Total diskon yang diberikan</div>
                            </div>

                            <!-- Total Piutang -->
                            <div class="modal-summary-card card-total-piutang">
                                <div>
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100/80 text-amber-600 mb-2">
                                        <x-heroicon-o-banknotes class="w-4 h-4" />
                                    </div>
                                    <div class="text-[11px] font-medium text-gray-500">Total Piutang</div>
                                    <div id="detail-saldo-piutang" class="text-sm lg:text-base font-bold text-amber-700 mt-0.5 tracking-tight truncate">
                                        Rp 0
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-2 font-normal truncate">Sisa piutang pelanggan</div>
                            </div>

                            <!-- Jatuh Tempo -->
                            <div class="modal-summary-card card-jatuh-tempo">
                                <div>
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100/80 text-purple-600 mb-2">
                                        <x-heroicon-o-calendar-days class="w-4 h-4" />
                                    </div>
                                    <div class="text-[11px] font-medium text-gray-500">Jatuh Tempo</div>
                                    <div id="detail-jatuh-tempo" class="text-sm lg:text-base font-bold text-purple-700 mt-0.5 tracking-tight truncate">
                                        -
                                    </div>
                                </div>
                                <div id="detail-jatuh-tempo-caption" class="text-[10px] text-gray-400 mt-2 font-normal truncate">
                                    Belum ada piutang jatuh tempo
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Riwayat Transaksi -->
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2.5">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 tracking-tight">Riwayat Transaksi</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Daftar transaksi pelanggan dalam beberapa periode terakhir.
                                </p>
                            </div>

                            <span id="detail-jumlah-transaksi" class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-full whitespace-nowrap">
                                0 transaksi
                            </span>
                        </div>

                        <div class="modal-table-container">
                            <table class="modal-table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 22%; text-align: left;">Tanggal</th>
                                        <th scope="col" style="width: 28%; text-align: left;">No. Faktur</th>
                                        <th scope="col" style="width: 25%; text-align: center;">Metode Pembayaran</th>
                                        <th scope="col" style="width: 25%; text-align: right;">Total Transaksi</th>
                                    </tr>
                                </thead>

                                <tbody id="detail-riwayat-body" class="divide-y divide-gray-100">
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                                <span>Memuat riwayat transaksi...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

    </div>

<<<<<<< HEAD
    <x-slot:footer>
        <div class="flex justify-between items-center w-full">
            <button
                type="button"
                id="btn-detail-print-card"
                onclick="openCardModalFromCurrentDetail()"
                class="btn-secondary !text-xs inline-flex items-center gap-1.5 px-3 py-2 text-gray-700 hover:text-blue-600 hover:border-blue-300 transition"
                title="Cetak Kartu Member">
                <x-heroicon-o-printer class="w-4 h-4" />
                <span>Cetak Kartu Member</span>
            </button>
            <button
                type="button"
                onclick="closeDetailModal()"
                class="btn-secondary !text-xs px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100 transition">
                <span>Tutup</span>
=======
        <!-- FOOTER (Tetap terlihat di bawah) -->
        <div class="modal-footer-fixed flex justify-end items-center px-6 py-3.5 border-t border-gray-100 bg-white shrink-0">
            <button
                type="button"
                onclick="closeDetailModal()"
                class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-2xs transition active:scale-98"
            >
                Tutup
>>>>>>> origin/feriena
            </button>
        </div>
    </x-slot:footer>
</x-modal>

<style>
/* ==========================================================================
   Modal Detail Pelanggan - Fixed Consistency Engine (Acuan Utama: Gambar 2)
   ========================================================================== */
#modal-detail-pelanggan .modal-detail-card {
    width: 90vw !important;
    max-width: 1080px !important;
    height: min(84vh, 670px) !important;
    min-height: 590px !important;
    border-radius: 16px !important;
    background-color: #ffffff !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    border: 1px solid #e2e8f0 !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

@media (max-width: 1024px) {
    #modal-detail-pelanggan .modal-detail-card {
        width: 94vw !important;
        height: 90vh !important;
        max-height: 90vh !important;
        min-height: 0 !important;
    }
}

#modal-detail-pelanggan .modal-detail-scrollable {
    flex: 1 1 0% !important;
    overflow-y: auto !important;
    padding: 20px 24px !important;
}

#modal-detail-pelanggan .modal-layout-grid {
    display: flex !important;
    flex-direction: row !important;
    gap: 24px !important;
    height: 100% !important;
    align-items: stretch !important;
}

@media (max-width: 1024px) {
    #modal-detail-pelanggan .modal-layout-grid {
        flex-direction: column !important;
        height: auto !important;
    }
}

/* Panel Profil Kiri */
#modal-detail-pelanggan .modal-profile-panel {
    width: 310px !important;
    flex-shrink: 0 !important;
    height: 100% !important;
}

@media (max-width: 1024px) {
    #modal-detail-pelanggan .modal-profile-panel {
        width: 100% !important;
        height: auto !important;
    }
}

#modal-detail-pelanggan .modal-profile-card {
    height: 100% !important;
    background-color: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    padding: 20px !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    box-sizing: border-box !important;
}

#modal-detail-pelanggan .modal-profile-contact {
    border-top: 1px solid #f1f5f9 !important;
    padding-top: 14px !important;
}

/* Panel Kanan (Ringkasan & Riwayat) */
#modal-detail-pelanggan .modal-right-panel {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
}

@media (max-width: 1024px) {
    #modal-detail-pelanggan .modal-right-panel {
        gap: 20px !important;
        height: auto !important;
    }
}

/* Ringkasan Transaksi 4 Cards */
#modal-detail-pelanggan .modal-summary-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 12px !important;
    margin-top: 10px !important;
}

@media (max-width: 640px) {
    #modal-detail-pelanggan .modal-summary-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

#modal-detail-pelanggan .modal-summary-card {
    height: 112px !important;
    min-height: 112px !important;
    max-height: 112px !important;
    border-radius: 12px !important;
    padding: 12px 14px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    box-sizing: border-box !important;
    transition: box-shadow 0.15s ease !important;
}

#modal-detail-pelanggan .card-total-belanja {
    background-color: #f0f7ff !important;
    border: 1px solid #dbeafe !important;
}

#modal-detail-pelanggan .card-total-diskon {
    background-color: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
}

#modal-detail-pelanggan .card-total-piutang {
    background-color: #fffbeb !important;
    border: 1px solid #fde68a !important;
}

#modal-detail-pelanggan .card-jatuh-tempo {
    background-color: #faf5ff !important;
    border: 1px solid #e9d5ff !important;
}

/* Riwayat Transaksi Table Container */
#modal-detail-pelanggan .modal-table-container {
    height: 228px !important;
    min-height: 228px !important;
    max-height: 228px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 12px !important;
    background-color: #ffffff !important;
    overflow-y: auto !important;
    overflow-x: auto !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}

#modal-detail-pelanggan .modal-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 12px !important;
    text-align: left !important;
}

#modal-detail-pelanggan .modal-table thead {
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e5e7eb !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 10 !important;
}

#modal-detail-pelanggan .modal-table thead th {
    padding: 10px 16px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #4b5563 !important;
    white-space: nowrap !important;
    background-color: #f8fafc !important;
}

#modal-detail-pelanggan .modal-table tbody tr {
    border-bottom: 1px solid #f3f4f6 !important;
    height: 38px !important;
    transition: background-color 0.15s ease !important;
}

#modal-detail-pelanggan .modal-table tbody tr:hover {
    background-color: rgba(239, 246, 255, 0.4) !important;
}

#modal-detail-pelanggan .modal-table tbody tr:last-child {
    border-bottom: none !important;
}

#modal-detail-pelanggan .modal-table tbody td {
    padding: 9px 16px !important;
    font-size: 12px !important;
    white-space: nowrap !important;
}
</style>

<script>
let currentDetailPelanggan = null;

function openCardModalFromCurrentDetail() {
    if (!currentDetailPelanggan) return;
    const p = currentDetailPelanggan;
    if (typeof window.openCardModal === 'function') {
        const isActive = Boolean(p.is_member && p.member_aktif);
        window.openCardModal(
            p.nama || '',
            p.member_id || '',
            p.total_belanja || 0,
            p.status_member || '',
            p.custom_discount_percentage,
            isActive,
            p.diskon_percent
        );
    }
}
function formatDetailRupiah(angka) {
    return 'Rp ' + Math.round(Number(angka || 0)).toLocaleString('id-ID');
}

function formatDetailTanggal(tanggal) {
    if (!tanggal) return '-';

    const parts = String(tanggal).split('-');

    if (parts.length !== 3) {
        return tanggal;
    }

    return `${parts[2]}-${parts[1]}-${parts[0]}`;
}

function labelMetodePembayaran(metode) {
    const labels = {
        cash: 'Cash',
        qris: 'QRIS',
        debit: 'Debit',
        piutang: 'Piutang'
    };

    return labels[String(metode || '').toLowerCase()] || (metode || '-');
}

function getMemberStatusInfo(pelanggan) {
    if (!pelanggan.is_member) {
        return {
            label: 'Pelanggan Umum',
            badgeClass: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-50 text-gray-600 border border-gray-200'
        };
    }

    const rawStatus = (pelanggan.status_member || '').trim();
    const status = rawStatus || 'Member Pelanggan Tetap';

    if (status === 'Member Keluarga Nakes') {
        return {
            label: 'Member Keluarga Nakes',
            badgeClass: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/70'
        };
    }

    if (status === 'Member Only') {
        return {
            label: 'Member Only',
            badgeClass: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/70'
        };
    }

    // Default: Member Pelanggan Tetap
    return {
        label: 'Member Pelanggan Tetap',
        badgeClass: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70'
    };
}

function handleDetailBackdropClick(event) {
    if (event.target === document.getElementById('modal-detail-pelanggan')) {
        closeDetailModal();
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('modal-detail-pelanggan');
        if (modal && !modal.classList.contains('hidden')) {
            closeDetailModal();
        }
    }
});

function openDetailModal(pelangganId) {
    const modal = document.getElementById('modal-detail-pelanggan');
    const historyBody = document.getElementById('detail-riwayat-body');

    // Reset data ke default loading state
    document.getElementById('detail-nama').textContent = '-';
    document.getElementById('detail-member-id').textContent = '-';
    document.getElementById('detail-telepon').textContent = '-';
    document.getElementById('detail-alamat').textContent = 'Memuat...';
    document.getElementById('detail-benefit-diskon').textContent = 'Diskon 10%';

    const statusBadge = document.getElementById('detail-status-member-badge');
    if (statusBadge) {
        statusBadge.textContent = 'Memuat...';
        statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-50 text-gray-500 border border-gray-200';
    }

    const statusPill = document.getElementById('detail-status-member-pill');
    if (statusPill) {
        statusPill.textContent = '-';
        statusPill.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-50 text-gray-500 border border-gray-200 text-right';
    }

    document.getElementById('detail-member-since').textContent = '-';
    document.getElementById('detail-tanggal-lahir').textContent = '-';
    document.getElementById('detail-saldo-piutang-text').textContent = 'Rp 0';
    document.getElementById('detail-saldo-piutang-text').className = 'text-gray-800 font-bold text-right';

    document.getElementById('detail-total-belanja').textContent = 'Rp 0';
    document.getElementById('detail-saldo-piutang').textContent = 'Rp 0';
    document.getElementById('detail-jatuh-tempo').textContent = '-';
    document.getElementById('detail-jatuh-tempo-caption').textContent = 'Belum ada piutang jatuh tempo';
    document.getElementById('detail-total-diskon').textContent = 'Rp 0';
    document.getElementById('detail-jumlah-transaksi').textContent = '0 transaksi';

    historyBody.innerHTML = `
        <tr>
            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                <div class="flex flex-col items-center justify-center gap-2">
                    <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <span>Memuat riwayat transaksi...</span>
                </div>
            </td>
        </tr>
    `;

    modal.classList.remove('hidden');
    const scrollable = modal.querySelector('.modal-detail-scrollable');
    if (scrollable) {
        scrollable.scrollTop = 0;
    }

    fetch(`{{ url('pelanggan') }}/${pelangganId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal memuat detail pelanggan');
        }
        return response.json();
    })
    .then(data => {
        const pelanggan = data.pelanggan || {};
        currentDetailPelanggan = pelanggan;
        const printCardBtn = document.getElementById('btn-detail-print-card');
        if (printCardBtn) {
            printCardBtn.style.display = (pelanggan.member_id) ? 'inline-flex' : 'none';
        }
        const transaksi = Array.isArray(data.transaksi) ? data.transaksi : [];

        // Profil
        document.getElementById('detail-nama').textContent = pelanggan.nama || '-';
        document.getElementById('detail-member-id').textContent = pelanggan.member_id || '-';
        document.getElementById('detail-telepon').textContent = pelanggan.telepon || '-';
        document.getElementById('detail-alamat').textContent = pelanggan.alamat || '-';

        // Status Member (3 Jenis: Member Pelanggan Tetap, Member Keluarga Nakes, Member Only)
        const memberStatus = getMemberStatusInfo(pelanggan);

        if (statusBadge) {
            statusBadge.textContent = memberStatus.label;
            statusBadge.className = memberStatus.badgeClass;
        }

        if (statusPill) {
            statusPill.textContent = memberStatus.label;
            statusPill.className = memberStatus.badgeClass + ' text-right';
        }

        // Tanggal
        document.getElementById('detail-member-since').textContent = pelanggan.member_since || '-';
        document.getElementById('detail-tanggal-lahir').textContent = pelanggan.tanggal_lahir || '-';

        // Benefit Diskon
        const customDiscount = pelanggan.custom_discount_percentage;
        const benefitLabel =
            customDiscount !== null &&
            customDiscount !== undefined &&
            customDiscount !== ''
                ? `Diskon ${parseFloat(customDiscount)}%`
                : (pelanggan.benefit_diskon_label || 'Diskon 10%');

        document.getElementById('detail-benefit-diskon').textContent = benefitLabel;

        // Saldo Piutang
        const saldoPiutang = Number(pelanggan.saldo_piutang || 0);
        const formattedPiutang = formatDetailRupiah(saldoPiutang);
        document.getElementById('detail-saldo-piutang').textContent = formattedPiutang;
        
        const saldoPiutangTextEl = document.getElementById('detail-saldo-piutang-text');
        if (saldoPiutangTextEl) {
            saldoPiutangTextEl.textContent = formattedPiutang;
            if (saldoPiutang > 0) {
                saldoPiutangTextEl.className = 'text-red-600 font-bold text-right';
            } else {
                saldoPiutangTextEl.className = 'text-gray-800 font-bold text-right';
            }
        }

        // Ringkasan Transaksi
        document.getElementById('detail-total-belanja').textContent = formatDetailRupiah(pelanggan.total_belanja);
        document.getElementById('detail-total-diskon').textContent = formatDetailRupiah(pelanggan.total_diskon);
        
        const jatuhTempo = pelanggan.jatuh_tempo || '-';
        document.getElementById('detail-jatuh-tempo').textContent = jatuhTempo;
        const captionJatuhTempoEl = document.getElementById('detail-jatuh-tempo-caption');
        if (captionJatuhTempoEl) {
            if (saldoPiutang > 0 && jatuhTempo !== '-') {
                captionJatuhTempoEl.textContent = 'Jatuh tempo pembayaran';
                captionJatuhTempoEl.className = 'text-[10px] text-purple-600 font-medium mt-2 truncate';
            } else {
                captionJatuhTempoEl.textContent = 'Belum ada piutang jatuh tempo';
                captionJatuhTempoEl.className = 'text-[10px] text-gray-400 mt-2 font-normal truncate';
            }
        }

        document.getElementById('detail-jumlah-transaksi').textContent = `${transaksi.length} transaksi`;

        // Render Riwayat Transaksi (Kolom: Tanggal, No. Faktur, Metode, Total)
        historyBody.innerHTML = '';

        if (transaksi.length === 0) {
            historyBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center gap-1.5">
                            <span class="text-xs">Belum ada transaksi.</span>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        transaksi.forEach(item => {
            const row = document.createElement('tr');

            // Siapkan parameter URL agar saat klik "Kembali" di halaman struk,
            // pengguna kembali ke halaman Pelanggan dengan modal detail pelanggan yang sama tetap terbuka.
            const queryParams = new URLSearchParams(window.location.search);
            queryParams.delete('detail_pelanggan_id');
            queryParams.delete('open_detail');
            queryParams.set('from', 'pelanggan');
            if (pelanggan && pelanggan.id) {
                queryParams.set('pelanggan_id', pelanggan.id);
            }

            const urlDetail = `{{ url('penjualan') }}/${item.id}?${queryParams.toString()}`;

            // 1. Tanggal
            const tdTanggal = document.createElement('td');
            tdTanggal.style.width = '22%';
            tdTanggal.style.textAlign = 'left';
            tdTanggal.style.color = '#4b5563';
            tdTanggal.textContent = formatDetailTanggal(item.tanggal);

            // 2. No. Faktur (link aktif menuju detail transaksi)
            const tdFaktur = document.createElement('td');
            tdFaktur.style.width = '28%';
            tdFaktur.style.textAlign = 'left';
            tdFaktur.style.fontFamily = 'monospace';
            const linkFaktur = document.createElement('a');
            linkFaktur.href = urlDetail;
            linkFaktur.className = 'text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors';
            linkFaktur.textContent = item.no_faktur || '-';
            linkFaktur.title = `Buka faktur ${item.no_faktur || ''}`;
            tdFaktur.appendChild(linkFaktur);

            // 3. Metode Pembayaran (tanpa border)
            const tdMetode = document.createElement('td');
            tdMetode.style.width = '25%';
            tdMetode.style.textAlign = 'center';
            tdMetode.style.color = '#4b5563';
            tdMetode.style.fontWeight = '500';
            tdMetode.textContent = labelMetodePembayaran(item.metode_pembayaran);

            // 4. Total Transaksi
            const tdTotal = document.createElement('td');
            tdTotal.style.width = '25%';
            tdTotal.style.textAlign = 'right';
            tdTotal.style.fontWeight = '700';
            tdTotal.style.color = '#1f2937';
            tdTotal.textContent = formatDetailRupiah(item.total);

            row.appendChild(tdTanggal);
            row.appendChild(tdFaktur);
            row.appendChild(tdMetode);
            row.appendChild(tdTotal);

            historyBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error(error);
        document.getElementById('detail-alamat').textContent = 'Gagal memuat data';

        historyBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-red-500 text-xs">
                    Riwayat transaksi gagal dimuat.
                </td>
            </tr>
        `;
    });
}

function closeDetailModal() {
    const modal = document.getElementById('modal-detail-pelanggan');
    if (modal) {
        modal.classList.add('hidden');
    }

    // Bersihkan parameter detail_pelanggan_id dari address bar tanpa reload
    try {
        const url = new URL(window.location.href);
        if (url.searchParams.has('detail_pelanggan_id') || url.searchParams.has('open_detail')) {
            url.searchParams.delete('detail_pelanggan_id');
            url.searchParams.delete('open_detail');
            window.history.replaceState({}, document.title, url.toString());
        }
    } catch (e) {
        console.error(e);
    }
}

// Auto-buka modal Detail Pelanggan jika kembali dari struk dengan parameter detail_pelanggan_id
function checkAutoOpenDetailModal() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const detailPelangganId = urlParams.get('detail_pelanggan_id') || urlParams.get('open_detail');
        if (detailPelangganId) {
            openDetailModal(detailPelangganId);
        }
    } catch (e) {
        console.error(e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkAutoOpenDetailModal);
} else {
    checkAutoOpenDetailModal();
}
</script>
