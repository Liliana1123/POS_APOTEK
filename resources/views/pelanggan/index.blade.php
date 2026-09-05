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
        <table class="table-custom min-w-[72rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-40">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Member ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col" class="w-40">Telepon</th>
                    <th scope="col" class="text-center w-28">Status</th>
                    <th scope="col" class="text-center w-24">Transaksi</th>
                    <th scope="col" class="text-center w-32">Total Belanja</th>
                    <th scope="col" class="text-center w-32">Total Hemat</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($pelanggans as $index => $pelanggan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <a href="{{ route('pelanggan.show', $pelanggan) }}"
                                    class="btn-secondary !p-1.5"
                                    style="color: #2563EB;"
                                    title="Detail"
                                    aria-label="Detail">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-pelanggan"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    aria-label="Edit"
                                    data-id="{{ $pelanggan->id }}"
                                    data-json="{{ json_encode([
                                        'nama' => $pelanggan->nama,
                                        'telepon' => $pelanggan->telepon,
                                        'is_member' => (int) $pelanggan->is_member,
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                @if ($pelanggan->is_member)
                                    <button type="button" onclick="openCardModal('{{ addslashes($pelanggan->nama) }}', '{{ $pelanggan->member_id }}')"
                                        class="btn-secondary !p-1.5"
                                        style="color: #7C3AED;"
                                        title="Kartu"
                                        aria-label="Kartu">
                                        <x-heroicon-o-credit-card class="w-4 h-4" />
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
                        <td class="table-num">{{ $pelanggan->id }}</td>
                        <td class="font-mono text-gray-600">{{ $pelanggan->member_id ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $pelanggan->nama }}</td>
                        <td class="text-gray-600">{{ $pelanggan->telepon ?? '—' }}</td>
                        <td class="text-center">
                            @if ($pelanggan->is_member)
                                <span class="badge-success">Member</span>
                            @else
                                <span class="badge-neutral">Non-Member</span>
                            @endif
                        </td>
                        <td class="text-center font-medium text-gray-700">{{ $pelanggan->penjualan_count ?? 0 }}x</td>
                        <td class="text-center font-semibold text-gray-800">
                            Rp {{ number_format($pelanggan->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-semibold text-green-600">
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
