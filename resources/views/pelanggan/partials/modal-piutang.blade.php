{{-- Modal Pembayaran Piutang --}}
<div id="piutangPaymentModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">

    <div class="modal-container-custom w-full max-w-4xl rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b px-5 py-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Pembayaran Piutang
                </h3>
                <p class="text-sm text-gray-500">
                    Kelola pembayaran piutang member
                </p>
            </div>

            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        {{-- Informasi Member --}}
        <div class="grid grid-cols-1 gap-4 border-b px-5 py-4 sm:grid-cols-3">

            <div>
                <div class="text-xs text-gray-500">
                    ID Member
                </div>
                <div id="piutangMemberId"
                    class="mt-1 font-medium text-gray-800">
                    -
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500">
                    Nama Member
                </div>
                <div id="piutangMemberNama"
                    class="mt-1 font-medium text-gray-800">
                    -
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500">
                    Total Piutang
                </div>
                <div id="piutangMemberTotal"
                    class="mt-1 font-semibold text-red-600">
                    Rp 0
                </div>
            </div>

        </div>

        {{-- Pilih Invoice --}}
        <div class="px-5 py-4">

            <label for="piutangPenjualanId"
                class="block text-sm font-medium text-gray-700">
                Invoice Piutang
            </label>

            <select id="piutangPenjualanId"
                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">
                    Pilih invoice piutang
                </option>
            </select>

        </div>

        {{-- Detail Invoice --}}
        <div id="piutangInvoiceDetail"
            class="hidden px-5 pb-4">

            <div class="grid grid-cols-1 gap-4 rounded-lg bg-gray-50 p-4 sm:grid-cols-3">

                <div>
                    <div class="text-xs text-gray-500">
                        No. Faktur
                    </div>
                    <div id="piutangNoFaktur"
                        class="mt-1 font-medium text-gray-800">
                        -
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        Total Belanja
                    </div>
                    <div id="piutangTotalBelanja"
                        class="mt-1 font-medium text-gray-800">
                        Rp 0
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        Total Dibayar
                    </div>
                    <div id="piutangTotalDibayar"
                        class="mt-1 font-medium text-green-600">
                        Rp 0
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        Sisa Piutang
                    </div>
                    <div id="piutangSisa"
                        class="mt-1 font-semibold text-red-600">
                        Rp 0
                    </div>
                </div>

            </div>

        </div>

        {{-- Form Pembayaran --}}
        <div id="piutangPaymentForm"
            class="hidden px-5 pb-5">

            <div class="rounded-lg border p-4">

                <h4 class="mb-4 text-sm font-semibold text-gray-800">
                    Tambah Pembayaran
                </h4>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <div>
                        <label for="piutangTanggalBayar"
                            class="block text-sm font-medium text-gray-700">
                            Tanggal Bayar
                        </label>

                        <input type="date"
                            id="piutangTanggalBayar"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm">
                    </div>

                    <div>
                        <label for="piutangJumlah"
                            class="block text-sm font-medium text-gray-700">
                            Jumlah Pembayaran
                        </label>

                        <input type="number"
                            id="piutangJumlah"
                            min="0.01"
                            step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm"
                            placeholder="0">
                    </div>

                    <div>
                        <label for="piutangKeterangan"
                            class="block text-sm font-medium text-gray-700">
                            Keterangan
                        </label>

                        <input type="text"
                            id="piutangKeterangan"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm"
                            placeholder="Keterangan pembayaran">
                    </div>

                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button"
                        id="btnSimpanPembayaranPiutang"
                        class="btn-primary">
                        Simpan Pembayaran
                    </button>
                </div>

            </div>

        </div>

        {{-- Riwayat Pembayaran --}}
        <div id="piutangHistory"
            class="hidden border-t px-5 py-4">

            <h4 class="mb-3 text-sm font-semibold text-gray-800">
                Riwayat Pembayaran
            </h4>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead>
                        <tr class="border-b text-left text-xs text-gray-500">
                            <th class="px-3 py-2">
                                Tanggal
                            </th>
                            <th class="px-3 py-2">
                                Jumlah
                            </th>
                            <th class="px-3 py-2">
                                Keterangan
                            </th>
                        </tr>
                    </thead>

                    <tbody id="piutangHistoryBody">
                        <tr>
                            <td colspan="3"
                                class="px-3 py-4 text-center text-gray-400">
                                Belum ada pembayaran.
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>

        {{-- Footer --}}
        <div class="modal-footer-custom flex justify-end border-t px-5 py-3">

            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                Tutup
            </button>

        </div>

    </div>
</div>

<script>
let currentPiutangPelangganId = null;
let currentPiutangPenjualanId = null;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(Number(angka || 0)).toLocaleString('id-ID');
}

function openPiutangPaymentModal(pelangganId) {
    currentPiutangPelangganId = pelangganId;
    currentPiutangPenjualanId = null;

    const modal = document.getElementById('piutangPaymentModal');
    const select = document.getElementById('piutangPenjualanId');

    document.getElementById('piutangMemberId').textContent = '-';
    document.getElementById('piutangMemberNama').textContent = '-';
    document.getElementById('piutangMemberTotal').textContent = 'Rp 0';

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
            option.textContent =
                `${item.no_faktur} — ${formatRupiah(item.sisa_piutang)}`;
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

            document.getElementById('piutangInvoiceDetail')
                .classList.remove('hidden');

            const tanggalInput =
                document.getElementById('piutangTanggalBayar');

            tanggalInput.value =
                new Date().toISOString().split('T')[0];

            document.getElementById('piutangJumlah').value = '';
            document.getElementById('piutangKeterangan').value = '';

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
                    row.className = 'border-b';

                    row.innerHTML = `
                        <td class="px-3 py-2">
                            ${item.tanggal_bayar || '-'}
                        </td>
                        <td class="px-3 py-2 font-semibold text-green-600">
                            ${formatRupiah(item.jumlah)}
                        </td>
                        <td class="px-3 py-2 text-gray-600">
                            ${item.keterangan || '-'}
                        </td>
                    `;

                    historyBody.appendChild(row);
                });
            } else {
                historyBody.innerHTML = `
                    <tr>
                        <td colspan="3"
                            class="px-3 py-4 text-center text-gray-400">
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

        const keterangan =
            document.getElementById('piutangKeterangan').value;

        if (!tanggalBayar) {
            alert('Tanggal pembayaran wajib diisi.');
            return;
        }

        if (!jumlah || Number(jumlah) <= 0) {
            alert('Jumlah pembayaran harus lebih dari 0.');
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
                    keterangan: keterangan
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

            // Jika masih ada sisa piutang, cukup muat ulang detail invoice.
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
</script>
