@extends('layouts.app')
@section('title', 'Pelanggan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Pelanggan</h1>
        <p class="text-caption mt-1">Kelola data pelanggan & membership POS Apotek.</p>
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
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Pelanggan</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, nomor HP, atau Member ID..."
                        class="form-input pr-8">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="member" @selected(request('status') === 'member')>Member</option>
                    <option value="non-member" @selected(request('status') === 'non-member')>Non-Member</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'status']))
                <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary !p-1.5" title="Filter">
                <x-heroicon-o-funnel class="w-4 h-4" />
            </button>
        </div>
    </form>
</div>

<!-- Table List -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="customer-table table-custom min-w-[61rem] w-full table-fixed">
            <colgroup>
                <col style="width: 136px;">
                <col style="width: 120px;">
                <col style="width: 130px;">
                <col style="width: 120px;">
                <col style="width: 100px;">
                <col style="width: 90px;">
                <col style="width: 85px;">
                <col style="width: 125px;">
                <col style="width: 125px;">
            </colgroup>
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Aksi</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Member ID</th>
                    <th scope="col" class="px-1 py-3 text-left align-middle">Nama</th>
                    <th scope="col" class="px-1 py-3 text-left align-middle whitespace-nowrap">Telepon</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle">Piutang</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle">Status</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle">Transaksi</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Total Belanja</th>
                    <th scope="col" class="px-1 py-3 text-center align-middle whitespace-nowrap">Total Hemat</th>
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
                                <button type="button"
                                    @if ($pelanggan->is_member)
                                        onclick="openCardModal('{{ addslashes($pelanggan->nama) }}', '{{ $pelanggan->member_id }}')"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-violet-500"
                                        style="color: #7C3AED; padding: 0; min-width: 28px; width: 28px; height: 28px;"
                                        title="Kartu"
                                        aria-label="Kartu"
                                    @else
                                        disabled
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 bg-white opacity-50"
                                        style="color: #9CA3AF; padding: 0; min-width: 28px; width: 28px; height: 28px; cursor: not-allowed;"
                                        title="Kartu hanya untuk member"
                                        aria-label="Kartu tidak tersedia"
                                    @endif
                                >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[10px] w-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width: 10px; height: 10px; flex-shrink: 0; display: block;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 14h2"/>
                                        </svg>
                                </button>
                                @if ($pelanggan->telepon)
                                    @php
                                        $nomorWhatsapp = preg_replace('/[^0-9]/', '', $pelanggan->telepon);
                                        if (str_starts_with($nomorWhatsapp, '0')) {
                                            $nomorWhatsapp = '62' . substr($nomorWhatsapp, 1);
                                        }
                                        $pesanWhatsapp = urlencode('Halo ' . $pelanggan->nama . ', kami mengingatkan saldo piutang Anda sebesar Rp ' . number_format($pelanggan->saldo_piutang ?? 0, 0, ',', '.') . '.');
                                    @endphp
                                    <a href="https://wa.me/{{ $nomorWhatsapp }}?text={{ $pesanWhatsapp }}" target="_blank" rel="noopener" class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 bg-white transition hover:border-green-600" style="color: #16A34A; padding: 0; min-width: 28px; width: 28px; height: 28px;" title="Hubungi WhatsApp" aria-label="Hubungi WhatsApp">
                                        <span class="text-[10px] font-bold">WA</span>
                                    </a>
                                @else
                                    <button type="button" disabled class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 bg-white opacity-50" style="color: #9CA3AF; padding: 0; min-width: 28px; width: 28px; height: 28px; cursor: not-allowed;" title="Nomor telepon belum tersedia" aria-label="WhatsApp tidak tersedia">
                                        <span class="text-[10px] font-bold">WA</span>
                                    </button>
                                @endif
                                <form action="{{ route('pelanggan.destroy', $pelanggan) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-1 py-3 align-middle font-mono text-center text-gray-600 whitespace-nowrap">{{ $pelanggan->member_id ?? '—' }}</td>
                        <td class="px-1 py-3 align-middle font-medium text-left text-gray-800">{{ $pelanggan->nama }}</td>
                        <td class="px-1 py-3 align-middle text-left text-gray-600 whitespace-nowrap">{{ $pelanggan->telepon ?? '—' }}</td>
                        <td class="px-1 py-3 align-middle text-center font-semibold whitespace-nowrap">
                            @if (($pelanggan->saldo_piutang ?? 0) > 0)
                                <span class="badge-warning">Rp {{ number_format($pelanggan->saldo_piutang, 0, ',', '.') }}</span>
                            @else
                                <span class="badge-success">Lunas</span>
                            @endif
                        </td>
                        <td class="px-1 py-3 align-middle text-center">
                            @if ($pelanggan->is_member)
                                <span class="badge-success">Member</span>
                            @else
                                <span class="badge-neutral">Non-Member</span>
                            @endif
                        </td>
                        <td class="px-1 py-3 align-middle text-center font-medium text-gray-700 whitespace-nowrap">{{ $pelanggan->penjualan_count ?? 0 }}x</td>
                        <td class="px-1 py-3 align-middle text-center font-semibold text-gray-800 whitespace-nowrap">
                            Rp {{ number_format($pelanggan->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-1 py-3 align-middle text-center font-semibold text-green-600 whitespace-nowrap">
                            Rp {{ number_format($pelanggan->total_hemat ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['cari', 'status']))
                                        Pelanggan Tidak Ditemukan
                                    @else
                                        Pelanggan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'status']))
                                        Tidak ada data pelanggan yang cocok dengan filter pencarian Anda.
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
    create-title="Tambah Pelanggan"
    edit-title="Edit Pelanggan"
    create-url="{{ route('pelanggan.store') }}"
    update-base="{{ url('pelanggan') }}"
    create-btn="#btn-tambah-pelanggan"
    edit-btn=".btn-edit-pelanggan"
    width="max-w-md">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Pelanggan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Ketik nama pelanggan...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nomor HP / Telepon</label>
        <input type="text" name="telepon" class="form-input" placeholder="Contoh: 08123456789">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="telepon"></p>
    </div>
    <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_member" value="1" class="mr-2.5 mt-0.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
        <div>
            <span class="font-semibold">Daftar sebagai Member</span>
            <p class="text-[10px] text-gray-400 mt-0.5">Member berhak mendapatkan diskon belanja {{ config('pos.diskon_member', 10) }}%.</p>
        </div>
    </label>
</x-modal-form>

<!-- Modal Kartu Member -->
<div id="modal-card" class="modal-backdrop-custom hidden">
    <div class="modal-container-custom max-w-sm w-full mx-4">
        <!-- Print Area -->
        <div id="print-area" class="border-2 border-blue-600 rounded-xl p-5 bg-gradient-to-br from-blue-50 to-white text-blue-900 w-full shadow-sm mx-auto font-sans">
            <div class="flex justify-between items-center border-b pb-2.5 mb-4">
                <span class="font-bold text-sm tracking-wider uppercase text-blue-800">Apotek Membership</span>
                <span class="badge-success text-[8px] tracking-widest font-bold">MEMBER CARD</span>
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
                    <div class="text-left">
                        <span class="text-[9px] text-gray-400 block font-semibold uppercase tracking-wide">Status Kartu</span>
                        <strong class="text-xs text-blue-600 font-bold uppercase tracking-wider">ACTIVE</strong>
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
function openCardModal(nama, memberId) {
    document.getElementById('card-name').textContent = nama;
    document.getElementById('card-id').textContent = memberId;

    if (window.QRCode) {
        window.QRCode.toDataURL(memberId, { width: 128, margin: 1 }, function (err, url) {
            if (!err) {
                document.getElementById('card-qrcode').src = url;
            } else {
                console.error(err);
            }
        });
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
