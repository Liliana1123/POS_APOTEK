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
            @if(request()->filled('cari') || $status !== 'semua')
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
                <col style="width: 125px;">
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
                                @if($pelanggan->is_member && $pelanggan->member_id)
                                    <button
                                        type="button"
                                        onclick="openCardModal(
                                            @js($pelanggan->nama),
                                            @js($pelanggan->member_id),
                                            @js($pelanggan->penjualan_count),
                                            @js($pelanggan->status_member)
                                        )"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-blue-500"
                                        style="color: #2563EB; padding: 0; min-width: 28px; width: 28px; height: 28px;"
                                        title="Cetak Kartu Member"
                                        aria-label="Cetak Kartu Member"
                                    >
                                        <x-heroicon-o-credit-card class="w-3 h-3" />
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
    document.getElementById('card-name').textContent = nama;
    document.getElementById('card-id').textContent = memberId;

    // Status member
    const statusElement = document.getElementById('card-status-member');

    statusElement.textContent = statusMember || 'Member Pelanggan Tetap';

    // Reset warna status
    statusElement.classList.remove(
        'bg-green-100',
        'text-green-700',
        'bg-purple-100',
        'text-purple-700',
        'bg-yellow-100',
        'text-yellow-700'
    );

    // Tentukan warna berdasarkan status member
    if (statusMember === 'Member Keluarga Nakes') {
        statusElement.classList.add(
            'bg-purple-100',
            'text-purple-700'
        );
    } else if (statusMember === 'Member Only') {
        statusElement.classList.add(
            'bg-yellow-100',
            'text-yellow-700'
        );
    } else {
        statusElement.classList.add(
            'bg-green-100',
            'text-green-700'
        );
    }

    // Generate QR Code
    if (window.QRCode) {
        window.QRCode.toDataURL(
            memberId,
            { width: 128, margin: 1 },
            function (err, url) {
                if (!err) {
                    document.getElementById('card-qrcode').src = url;
                } else {
                    console.error(err);
                }
            }
        );
    } else {
        console.error("QRCode library not loaded.");
    }

    document.getElementById('modal-card').classList.remove('hidden');
}

function closeCardModal() {
    document.getElementById('modal-card').classList.add('hidden');
}

function printCard() {
    const printContent = document.getElementById('print-area').outerHTML;
    const printWindow = window.open('', '_blank', 'height=500,width=500');
    printWindow.document.write('<html><head><title>Cetak Kartu Member</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; height: 100vh; padding: 20px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCardModal();
    }
});
</script>
@endsection
