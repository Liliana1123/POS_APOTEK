<!-- Modal Detail Pelanggan -->
<div id="modal-detail-pelanggan"
    class="modal-backdrop-custom hidden fixed inset-0 z-[9998] flex items-center justify-center bg-black/50 p-4">

    <div class="modal-container-custom w-full max-w-3xl mx-4 bg-white rounded-xl shadow-xl max-h-[92vh] overflow-hidden">

        <!-- HEADER -->
        <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100">
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
            </div>

            <button
                type="button"
                onclick="closeDetailModal()"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                title="Tutup"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- CONTENT -->
        <div class="overflow-y-auto max-h-[calc(92vh-128px)]">

            <!-- INFORMASI PELANGGAN + RINGKASAN -->
            <div class="grid grid-cols-1 gap-5 px-5 py-5 md:grid-cols-2">

                <!-- KIRI -->
                <div class="space-y-4">

                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                            <x-heroicon-o-user class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-medium text-gray-400">Nama</div>
                            <div id="detail-nama" class="mt-0.5 text-sm font-semibold text-gray-800 break-words">-</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                            <x-heroicon-o-identification class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-medium text-gray-400">ID Pelanggan</div>
                            <div id="detail-member-id" class="mt-0.5 text-sm font-mono font-semibold text-blue-600 break-words">-</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                            <x-heroicon-o-phone class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-medium text-gray-400">No. Telp</div>
                            <div id="detail-telepon" class="mt-0.5 text-sm font-semibold text-gray-800 break-words">-</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                            <x-heroicon-o-map-pin class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-medium text-gray-400">Alamat</div>
                            <div id="detail-alamat" class="mt-0.5 text-sm font-semibold text-gray-800 break-words whitespace-pre-line">Memuat...</div>
                        </div>
                    </div>

                </div>

                <!-- KANAN -->
                <div class="space-y-3">

                    <div class="rounded-lg bg-blue-50 px-3.5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600">
                                <x-heroicon-o-shopping-cart class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-[10px] font-medium text-gray-500">Total Belanja</div>
                                <div id="detail-total-belanja" class="mt-0.5 text-sm font-bold text-gray-800">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-blue-50 px-3.5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600">
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-[10px] font-medium text-gray-500">Total Piutang</div>
                                <div id="detail-saldo-piutang" class="mt-0.5 text-sm font-bold text-gray-800">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-blue-50 px-3.5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600">
                                <x-heroicon-o-tag class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-[10px] font-medium text-gray-500">Total Diskon</div>
                                <div id="detail-total-diskon" class="mt-0.5 text-sm font-bold text-gray-800">Rp 0</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIWAYAT TRANSAKSI -->
            <div class="border-t border-gray-100 px-5 py-4">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">Riwayat Transaksi</h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Daftar transaksi pelanggan.
                        </p>
                    </div>

                    <span id="detail-jumlah-transaksi" class="text-[10px] text-blue-600 whitespace-nowrap">
                        0 transaksi
                    </span>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-xs">
                        <thead class="bg-blue-50">
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-2 text-left font-semibold text-gray-800 whitespace-nowrap">
                                    Tanggal
                                </th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-800 whitespace-nowrap">
                                    No. Faktur
                                </th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-800 whitespace-nowrap">
                                    Metode
                                </th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-800 whitespace-nowrap">
                                    Total
                                </th>
                            </tr>
                        </thead>

                        <tbody id="detail-riwayat-body">
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-gray-400">
                                    Memuat riwayat transaksi...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="flex justify-end items-center px-5 py-3 border-t border-gray-100 bg-gray-50">
            <button
                type="button"
                onclick="closeDetailModal()"
                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition"
            >
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
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

function openDetailModal(pelangganId) {
    const modal = document.getElementById('modal-detail-pelanggan');
    const historyBody = document.getElementById('detail-riwayat-body');

    document.getElementById('detail-nama').textContent = '-';
    document.getElementById('detail-member-id').textContent = '-';
    document.getElementById('detail-telepon').textContent = '-';
    document.getElementById('detail-alamat').textContent = 'Memuat...';
    document.getElementById('detail-total-belanja').textContent = 'Rp 0';
    document.getElementById('detail-saldo-piutang').textContent = 'Rp 0';
    document.getElementById('detail-total-diskon').textContent = 'Rp 0';
    document.getElementById('detail-jumlah-transaksi').textContent = '0 transaksi';

    historyBody.innerHTML = `
        <tr>
            <td colspan="4" class="px-3 py-6 text-center text-gray-400">
                Memuat riwayat transaksi...
            </td>
        </tr>
    `;

    modal.classList.remove('hidden');

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
        const transaksi = Array.isArray(data.transaksi_terbaru) ? data.transaksi_terbaru : [];

        document.getElementById('detail-nama').textContent = pelanggan.nama || '-';
        document.getElementById('detail-member-id').textContent = pelanggan.member_id || '-';
        document.getElementById('detail-telepon').textContent = pelanggan.telepon || '-';
        document.getElementById('detail-alamat').textContent = pelanggan.alamat || '-';
        document.getElementById('detail-total-belanja').textContent = formatDetailRupiah(pelanggan.total_belanja);
        document.getElementById('detail-saldo-piutang').textContent = formatDetailRupiah(pelanggan.saldo_piutang);
        document.getElementById('detail-total-diskon').textContent = formatDetailRupiah(pelanggan.total_diskon);
        document.getElementById('detail-jumlah-transaksi').textContent = `${transaksi.length} transaksi`;

        historyBody.innerHTML = '';

        if (transaksi.length === 0) {
            historyBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-gray-400">
                        Belum ada transaksi.
                    </td>
                </tr>
            `;
            return;
        }

        transaksi.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition border-b border-gray-100';

            const tanggal = document.createElement('td');
            tanggal.className = 'px-3 py-2.5 whitespace-nowrap text-gray-600';
            tanggal.textContent = formatDetailTanggal(item.tanggal);

            const faktur = document.createElement('td');
            faktur.className = 'px-3 py-2.5 whitespace-nowrap font-mono text-[10px] text-blue-600';
            faktur.textContent = item.no_faktur || '-';

            const metode = document.createElement('td');
            metode.className = 'px-3 py-2.5 whitespace-nowrap text-gray-600';
            metode.textContent = labelMetodePembayaran(item.metode_pembayaran);

            const total = document.createElement('td');
            total.className = 'px-3 py-2.5 whitespace-nowrap text-right font-semibold text-gray-800';
            total.textContent = formatDetailRupiah(item.total);

            row.appendChild(tanggal);
            row.appendChild(faktur);
            row.appendChild(metode);
            row.appendChild(total);

            historyBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error(error);

        document.getElementById('detail-alamat').textContent = 'Gagal memuat data';

        historyBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-3 py-6 text-center text-red-500">
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
}
</script>
