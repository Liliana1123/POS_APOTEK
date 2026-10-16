{{-- Modal Pembayaran Piutang --}}
<div id="piutangPaymentModal"
    class="modal-backdrop-custom hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">

    {{-- Popup / modal yang dapat di-scroll --}}
   <div class="modal-container-custom flex w-full max-w-4xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        {{-- Header --}}
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-5">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white">
                    <x-heroicon-o-credit-card class="h-6 w-6" />
                </div>

                <div>
                    <h3 class="text-xl font-bold text-gray-800">
                        Pembayaran Piutang
                    </h3>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Kelola pembayaran piutang member
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-6 w-6" />
            </button>
        </div>

        {{-- Isi popup yang di-scroll --}}
        <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">

            {{-- Ringkasan Member --}}
            <div class="grid grid-cols-2 gap-2 rounded-xl border border-blue-100 bg-blue-50/50 p-2.5 sm:grid-cols-4">
                <div class="border-r border-blue-100 pr-3">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        ID Member
                    </div>
                    <div id="piutangMemberId" class="mt-1 break-words text-base font-bold text-gray-800">
                        -
                    </div>
                </div>

                <div class="border-r border-blue-100 px-3">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        Nama Member
                    </div>
                    <div id="piutangMemberNama" class="mt-1 break-words text-base font-bold text-gray-800">
                        -
                    </div>
                </div>

                <div class="border-r border-blue-100 px-3">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        Total Piutang
                    </div>
                    <div id="piutangMemberTotal" class="mt-1 text-base font-bold text-red-600">
                        Rp 0
                    </div>
                </div>

                <div class="pl-3">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        Jatuh Tempo
                    </div>
                    <div id="piutangMemberJatuhTempo" class="mt-1 text-base font-bold text-gray-800">
                        -
                    </div>
                </div>

            </div>

            {{-- Konten Utama: Invoice di kiri, Form di kanan --}}
            <div class="mt-3 grid grid-cols-1 items-start gap-3 lg:grid-cols-2">

                {{-- Kolom Kiri --}}
                <div class="space-y-3">

                    {{-- Pilih Invoice --}}
                    <div class="rounded-xl border border-gray-200 p-3">
                        <div class="mb-3 flex items-center gap-2">
                            <x-heroicon-o-document-text class="h-5 w-5 text-blue-600" />
                            <h4 class="text-sm font-bold text-gray-800">
                                Pilih Invoice Piutang
                            </h4>
                        </div>

                        <label for="piutangPenjualanId"
                            class="mb-1.5 block text-xs font-medium text-gray-700">
                            Invoice Piutang
                        </label>

                        <select id="piutangPenjualanId"
                            class="block w-full min-w-0 rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih invoice piutang</option>
                        </select>
                    </div>

                    {{-- Detail Invoice --}}
                    <div id="piutangInvoiceDetail"
                        class="hidden rounded-xl border border-gray-200 p-3">

                        <div class="mb-3 flex items-center gap-2">
                            <x-heroicon-o-document-text class="h-5 w-5 text-blue-600" />
                            <h4 class="text-sm font-bold text-gray-800">
                                Detail Invoice
                            </h4>
                        </div>

                        <div class="grid grid-cols-2 gap-3 rounded-lg bg-gray-50 p-3 sm:grid-cols-2">

                            <div class="min-w-0 border-b border-gray-200 pb-2">
                                <div class="text-xs text-gray-500">No. Invoice</div>
                                <div id="piutangNoFaktur"
                                    class="mt-1 break-words text-sm font-semibold text-gray-800">
                                    -
                                </div>
                            </div>

                            <div class="min-w-0 border-b border-gray-200 pb-2">
                                <div class="text-xs text-gray-500">Jatuh Tempo</div>
                                <div id="piutangInvoiceJatuhTempo"
                                    class="mt-1 text-sm font-semibold text-gray-800">
                                    -
                                </div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs text-gray-500">Total Belanja</div>
                                <div id="piutangTotalBelanja"
                                    class="mt-1 text-sm font-semibold text-gray-800">
                                    Rp 0
                                </div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs text-gray-500">Total Dibayar</div>
                                <div id="piutangTotalDibayar"
                                    class="mt-1 text-sm font-semibold text-green-600">
                                    Rp 0
                                </div>
                            </div>

                            <div class="col-span-2 border-t border-gray-200 pt-2">
                                <div class="text-xs text-gray-500">Sisa Piutang</div>
                                <div id="piutangSisa"
                                    class="mt-1 text-base font-bold text-red-600">
                                    Rp 0
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Form Pembayaran --}}
                <div id="piutangPaymentForm"
                    class="hidden rounded-xl border border-gray-200 p-4">

                    <div class="mb-4 flex items-center gap-2">
                        <x-heroicon-o-credit-card class="h-5 w-5 text-blue-600" />
                        <h4 class="text-sm font-bold text-gray-800">
                            Form Pembayaran
                        </h4>
                    </div>

                    <div class="space-y-3">

                        {{-- Tanggal Bayar --}}
                        <div>
                            <label for="piutangTanggalBayar"
                                class="mb-1 block text-xs font-medium text-gray-700">
                                Tanggal Bayar
                            </label>

                            <input type="date"
                                id="piutangTanggalBayar"
                                class="block w-full rounded-lg border-gray-300 px-1 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Jumlah Pembayaran --}}
                        <div>
                            <label for="piutangJumlah"
                                class="mb-1 block text-xs font-medium text-gray-700">
                                Jumlah Pembayaran
                            </label>

                            <div class="flex">
                                <input type="number"
                                    id="piutangJumlah"
                                    min="0.01"
                                    step="0.01"
                                    class="block w-full min-w-0 rounded-lg border-gray-300 px-1 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Masukkan jumlah pembayaran">
                            </div>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div>
                            <label for="piutangMetodePembayaran"
                                class="mb-1 block text-xs font-medium text-gray-700">
                                Metode Pembayaran
                            </label>

                            <select
                                id="piutangMetodePembayaran"
                                name="metode_pembayaran"
                                required
                                class="block w-full rounded-lg border-gray-300 px-1 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="">Pilih metode pembayaran</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Debit</option>
                            </select>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label for="piutangKeterangan"
                                class="mb-1 block text-xs font-medium text-gray-700">
                                Keterangan <span class="font-normal text-gray-400">(Opsional)</span>
                            </label>

                            <textarea
                                id="piutangKeterangan"
                                rows="2"
                                maxlength="255"
                                class="block w-full resize-none rounded-lg border-gray-300 px-1 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Masukkan keterangan pembayaran..."></textarea>

                        </div>

                        {{-- Tombol Simpan --}}
                        <button type="button"
                            id="btnSimpanPembayaranPiutang"
                            class="btn-primary inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold">
                            <x-heroicon-o-credit-card class="h-4 w-4" />
                            Simpan Pembayaran
                        </button>

                    </div>
                </div>

            </div>

            {{-- Riwayat Pembayaran --}}
            <div id="piutangHistory"
                class="hidden mt-4 rounded-xl border border-gray-200 p-3">

                <div class="mb-3 flex items-center gap-2">
                    <x-heroicon-o-clock class="h-5 w-5 text-blue-600" />
                    <h4 class="text-sm font-bold text-gray-800">
                        Riwayat Pembayaran
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">

                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500">
                                <th class="whitespace-nowrap px-3 py-2">Tanggal</th>
                                <th class="whitespace-nowrap px-3 py-2">Jumlah</th>
                                <th class="whitespace-nowrap px-3 py-2">Keterangan</th>
                                <th class="whitespace-nowrap px-3 py-2">Metode Pembayaran</th>
                            </tr>
                        </thead>

                        <tbody id="piutangHistoryBody">
                            <tr>
                                <td colspan="4" class="px-3 py-5 text-center text-gray-400">
                                    Belum ada pembayaran.
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="modal-footer-custom flex shrink-0 justify-end border-t border-gray-200 bg-white px-6 py-4">

            <button type="button"
                onclick="closePiutangPaymentModal()"
                class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
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
                        <td colspan="3"
                            class="px-4 py-6 text-center text-gray-400">
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


