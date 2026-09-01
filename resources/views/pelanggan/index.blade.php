@extends('layouts.app')
@section('title', 'Pelanggan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Pelanggan</h1>
        <p class="text-caption mt-1">Kelola data pelanggan & membership POS Apotek.</p>
    </div>
    <a href="{{ route('pelanggan.create') }}" class="btn-primary py-2 px-4 shrink-0">
        + Tambah Pelanggan
    </a>
</div>

@if (session('success'))
    <div class="alert-success p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert-danger p-3 mb-4">
        {{ session('error') }}
    </div>
@endif

<!-- Filter Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('pelanggan.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, nomor HP, atau Member ID..." class="form-input">
        </div>
        <div class="w-full md:w-48">
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="member" @selected(request('status') === 'member')>Member</option>
                <option value="non-member" @selected(request('status') === 'non-member')>Non-Member</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-1.5 px-5">
                Filter
            </button>
            <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table List -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col" class="w-36">Telepon</th>
                    <th scope="col" class="w-28">Status</th>
                    <th scope="col" class="w-32">Member ID</th>
                    <th scope="col" class="text-center w-28">Transaksi</th>
                    <th scope="col" class="text-right w-36">Total Belanja</th>
                    <th scope="col" class="text-right w-36">Total Hemat</th>
                    <th scope="col" class="text-right w-56">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($pelanggans as $pelanggan)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $pelanggan->nama }}</td>
                        <td class="text-gray-600">{{ $pelanggan->telepon ?? '—' }}</td>
                        <td>
                            @if ($pelanggan->is_member)
                                <span class="badge-success">Member</span>
                            @else
                                <span class="badge-neutral">Non-Member</span>
                            @endif
                        </td>
                        <td class="font-mono text-gray-600">{{ $pelanggan->member_id ?? '—' }}</td>
                        <td class="text-center font-medium text-gray-700">{{ $pelanggan->penjualan_count ?? 0 }}x</td>
                        <td class="table-num font-semibold text-gray-800">
                            Rp {{ number_format($pelanggan->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="table-num font-semibold text-green-600">
                            Rp {{ number_format($pelanggan->total_hemat ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-right space-x-2">
                            <a href="{{ route('pelanggan.show', $pelanggan) }}" class="btn-secondary py-1 px-2.5 text-[10px] font-semibold">Detail</a>
                            <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn-secondary py-1 px-2.5 text-[10px] font-semibold">Edit</a>
                            @if ($pelanggan->is_member)
                                <button onclick="openCardModal('{{ addslashes($pelanggan->nama) }}', '{{ $pelanggan->member_id }}')" class="btn-secondary py-1 px-2.5 text-[10px] font-semibold text-green-600 hover:text-green-700">Kartu</button>
                            @endif
                            <form action="{{ route('pelanggan.destroy', $pelanggan) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs py-1 px-1.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Pelanggan Tidak Ditemukan</div>
                                <div class="empty-state-desc">Tidak ada data pelanggan yang terdaftar atau cocok dengan kata kunci pencarian.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $pelanggans->links() }}
</div>

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
                    <div class="text-right">
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

// Escape key to close card modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCardModal();
    }
});
</script>
@endsection
