@extends('layouts.app')
@section('title', 'Pelanggan/Member')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Pelanggan / Member</h1>
        <p class="text-caption mt-1">Kelola data pelanggan dan member POS Apotek.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-tambah-pelanggan" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Pelanggan</span>
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('pelanggan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Cari pelanggan/member
        </label>

        <div class="relative">
            <input
                type="text"
                name="cari"
                value="{{ request('cari') }}"
                placeholder="Cari nama, nomor HP, atau ID Pelanggan..."
                class="form-input pr-8"
            >

            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Status Member
        </label>

        <select name="status" class="form-input">
        <option value="semua" @selected($status === 'semua')>
            Semua
        </option>

        <option value="pelanggan_tetap" @selected($status === 'pelanggan_tetap')>
            Member Pelanggan Tetap
        </option>

        <option value="keluarga_nakes" @selected($status === 'keluarga_nakes')>
            Member Keluarga Nakes
        </option>

        <option value="member_only" @selected($status === 'member_only')>
            Member Only
        </option>
    </select>
    </div>

    <div>
    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
        Status Piutang
    </label>

    <select name="status_piutang" class="form-input">
        <option value="semua" @selected($statusPiutang === 'semua')>
            Semua
        </option>

        <option value="lunas" @selected($statusPiutang === 'lunas')>
            Lunas
        </option>

        <option value="belum_lunas" @selected($statusPiutang === 'belum_lunas')>
            Belum Lunas
        </option>
    </select>
</div>

</div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->filled('cari') || $status !== 'semua' || $statusPiutang !== 'semua')
                <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4" />
                <span>Filter</span>
            </button>
        </div>
    </form>
</div>

<!-- Table List -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="customer-table table-custom min-w-[80rem] w-full table-fixed">
            <colgroup>
                <col style="width: 180px;">
                <col style="width: 120px;">
                <col style="width: 150px;">
                <col style="width: 130px;">
                <col style="width: 190px;">
                <col style="width: 130px;">
                <col style="width: 140px;">
                <col style="width: 140px;">
            </colgroup>
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Aksi</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">ID Pelanggan</th>
                    <th scope="col" class="px-1 py-3 text-left align-middle">Nama</th>
                    <th scope="col" class="px-1 py-3 text-left align-middle whitespace-nowrap">No HP</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle">Status Member</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle">Piutang</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Total Diskon</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Total Belanja</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($pelanggans as $pelanggan)
                    <tr class="customer-data-row">
                        <td class="px-1 py-3 align-middle text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1" style="padding: 0; margin: 0;">
                                <a href="{{ route('pelanggan.show', $pelanggan) }}" class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-blue-500" style="color: #2563EB; padding: 0; min-width: 28px; width: 28px; height: 28px;" title="Detail" aria-label="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[10px] w-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width: 10px; height: 10px; flex-shrink: 0; display: block;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                </a>
                                <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-yellow-500" style="color: #F59E0B; padding: 0; min-width: 28px; width: 28px; height: 28px;" title="Edit" aria-label="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[10px] w-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width: 10px; height: 10px; flex-shrink: 0; display: block;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 2.651 2.651M18.5 2.5a2.121 2.121 0 1 1 3 3L7.5 18.5l-4 1 1-4L18.5 2.5Z"/>
                                    </svg>
                                </a>

                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-blue-500"
                                    style="color: #2563EB; padding: 0; min-width: 28px; width: 28px; height: 28px;"
                                    title="Cetak Kartu Member"
                                    aria-label="Cetak Kartu Member"
                                    onclick="openCardModal(@js($pelanggan->nama), @js($pelanggan->member_id), @js($pelanggan->total_belanja ?? 0), @js($pelanggan->status_member))"
                                >
                                    <x-heroicon-o-printer class="w-3 h-3" />
                                </button>

                                @if(
                                $pelanggan->is_member &&
                                $pelanggan->member_aktif &&
                                ($pelanggan->saldo_piutang ?? 0) > 0
                            )
                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-green-500"
                                    style="color: #16A34A; padding: 0; min-width: 28px; width: 28px; height: 28px;"
                                    title="Pembayaran Piutang"
                                    aria-label="Pembayaran Piutang"
                                    onclick="openPiutangPaymentModal({{ $pelanggan->id }})"
                                >
                                    <x-heroicon-o-banknotes class="w-3 h-3" />
                                </button>
                            @endif

                                <form method="POST" action="{{ route('pelanggan.destroy', $pelanggan) }}" onsubmit="return confirm('Hapus data pelanggan ini?')" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-red-500" style="color: #DC2626; padding: 0; min-width: 28px; width: 28px; height: 28px;" title="Hapus" aria-label="Hapus">
                                        <x-heroicon-o-trash class="w-3 h-3" />
                                    </button>
                                </form>
                                <button type="button" disabled class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed" style="padding: 0; min-width: 28px; width: 28px; height: 28px;" title="WhatsApp tersedia pada tahap Penjualan/Kasir" aria-label="WhatsApp">
                                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-3 h-3" />
                                </button>
                            </div>
                        </td>
                        <td class="px-1 py-3 align-middle font-mono text-center text-gray-600 whitespace-nowrap">{{ $pelanggan->member_id ?? '-' }}</td>
                        <td class="px-1 py-3 align-middle font-medium text-left text-gray-800">{{ $pelanggan->nama }}</td>
                        <td class="px-1 py-3 align-middle text-left text-gray-600 whitespace-nowrap">{{ $pelanggan->telepon ?? '-' }}</td>
                        <td class="px-1 py-3 align-middle text-center">
                           @php
                                $statusMember = trim((string) $pelanggan->status_member);
                            @endphp

                                @if($statusMember === 'Member Keluarga Nakes') 
                                <span 
                                    class="inline-flex items-center justify-center text-center" 
                                    style=" 
                                        font-size: 0.5625rem; 
                                        font-weight: 600; 
                                        text-transform: uppercase; 
                                        letter-spacing: 0.05em; 
                                        line-height: 1.2; 
                                        padding: 0.125rem 0.5rem; 
                                        border-radius: 0.25rem; 
                                        background-color: #F3E8FF; 
                                        color: #7E22CE; 
                                    " 
                                > 
                                    Member Keluarga Nakes 
                                </span> 

                            @elseif($statusMember === 'Member Only') 
                                <span 
                                    class="inline-flex items-center justify-center text-center" 
                                    style=" 
                                        font-size: 0.5625rem; 
                                        font-weight: 600; 
                                        text-transform: uppercase; 
                                        letter-spacing: 0.05em; 
                                        line-height: 1.2; 
                                        padding: 0.125rem 0.5rem; 
                                        border-radius: 0.25rem; 
                                        background-color: #FEF9C3; 
                                        color: #A16207; 
                                    " 
                                > 
                                    Member Only 
                                </span> 

                            @else 
                                <span 
                                    class="inline-flex items-center justify-center text-center" 
                                    style=" 
                                        font-size: 0.5625rem; 
                                        font-weight: 600; 
                                        text-transform: uppercase; 
                                        letter-spacing: 0.05em; 
                                        line-height: 1.2; 
                                        padding: 0.125rem 0.5rem; 
                                        border-radius: 0.25rem; 
                                        background-color: #DCFCE7; 
                                        color: #15803D; 
                                    " 
                                > 
                                    Member Pelanggan Tetap 
                                </span> 
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(($pelanggan->saldo_piutang ?? 0) > 0)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Belum Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-1 py-3 align-middle text-center font-semibold text-green-600 whitespace-nowrap">
                            Rp {{ number_format($pelanggan->total_diskon ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-1 py-3 align-middle text-center font-semibold text-gray-800 whitespace-nowrap">
                            Rp {{ number_format($pelanggan->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                   @if(
                                        request()->filled('cari') ||
                                        request()->filled('tanggal_awal') ||
                                        request()->filled('tanggal_akhir') ||
                                        $status !== 'semua'
                                    )
                                        Pelanggan Tidak Ditemukan
                                    @else
                                        Pelanggan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->filled('cari'))
                                        Tidak ada data pelanggan yang cocok dengan filter Anda.
                                    @else
                                        Belum ada data pelanggan terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $pelanggans->links() }}</div>

<!-- Modal Tambah / Edit Pelanggan -->
<x-modal-form
    id="modal-pelanggan"
    create-title="Tambah Pelanggan / Member"
    edit-title="Edit Pelanggan / Member"
    create-url="{{ route('pelanggan.store') }}"
    update-base="{{ url('pelanggan') }}"
    create-btn="#btn-tambah-pelanggan"
    edit-btn=".btn-edit-pelanggan"
    width="max-w-md">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Pelanggan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Ketik nama membership...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nomor HP / Telepon <span class="text-red-500">*</span></label>
        <input type="text" name="telepon" required class="form-input" placeholder="Contoh: 08123456789">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="telepon"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Alamat <span class="text-red-500">*</span></label>
        <textarea name="alamat" required class="form-input" rows="2"></textarea>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="alamat"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-input">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="tanggal_lahir"></p>
    </div>

    <div id="status-member-field">
    <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">
        Status Member <span class="text-red-500">*</span>
    </label>
    <select name="status_member" required class="form-input">
        <option value="">Pilih status member</option>
        <option value="Member Pelanggan Tetap">
            Member Pelanggan Tetap
        </option>
        <option value="Member Keluarga Nakes">
            Member Keluarga Nakes
        </option>
        <option value="Member Only">
            Member Only
        </option>
    </select>

    <p
        class="modal-field-error text-red-600 text-xs mt-1 hidden"
        data-error-for="status_member">
    </p>
    </div>
</x-modal-form>

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
                        <strong class="text-sm text-green-600 font-bold">{{ config('pos.diskon_member', 10) }}% OFF</strong>
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

<script>
function openCardModal(nama, memberId, totalTransaksi, statusMember) {
    document.getElementById('card-name').textContent = nama || '-';
    document.getElementById('card-id').textContent = memberId || '-';

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
    document.getElementById('modal-card').classList.add('hidden');
}

function printCard() {
    const printContent = document.getElementById('print-area').outerHTML;
    const printWindow = window.open('', '_blank', 'height=500,width=500');

    if (!printWindow) {
        alert('Popup diblokir browser. Izinkan popup untuk mencetak kartu.');
        return;
    }

    printWindow.document.write('<html><head><title>Cetak Kartu Member</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">');
    printWindow.document.write('<style>body { display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function () {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCardModal();
        closePiutangPaymentModal();
    }
});
</script>

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

            // Muat ulang detail invoice agar total dibayar,
            // sisa piutang, dan riwayat langsung berubah.
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
@endsection
