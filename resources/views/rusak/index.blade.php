@extends('layouts.app')
@section('title', 'Barang Rusak')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Daftar Pencatatan Barang Rusak</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola dan laporkan obat rusak atau kadaluarsa.</p>
    </div>
    <button type="button" onclick="openTambahRusak()" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Catat Barang Rusak</span>
    </button>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('rusak.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama barang..."
                class="form-input">
        </div>
        <div class="w-full md:w-48">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
            @if(request()->anyFilled(['cari', 'tanggal']))
                <a href="{{ route('rusak.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="table-custom-container shadow-sm">
    <div class="overflow-x-auto">
    <table class="table-custom min-w-[55rem]">
        <thead class="table-custom-header">
            <tr>
                <th class="px-5 py-3 text-center">Aksi</th>
                <th class="px-5 py-3">Tanggal</th>
                <th class="px-5 py-3">Barang / Obat</th>
                <th class="px-5 py-3">No. Batch</th>
                <th class="px-5 py-3 text-right">Jumlah</th>
                <th class="px-5 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-150">
            @forelse ($rusaks as $index => $rusak)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors">
                    <td class="text-left space-x-1.5">
                        <div class="flex items-center justify-start gap-1">

                            <button type="button" class="btn-secondary !p-1.5" style="color: #2563EB;" title="Lihat Detail" onclick="openDetailRusak({{ $rusak->id }})" >
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </button>

                            <button
                                type="button"
                                class="btn-secondary !p-1.5"
                                style="color: #F59E0B;"
                                title="Edit"
                                onclick="openEditRusak({{ $rusak->id }})"
                            >
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>

                            <a
                                href="{{ route('rusak.print', $rusak) }}"
                                class="btn-secondary !p-1.5"
                                title="Print"
                                target="_blank"
                            >
                                <x-heroicon-o-printer class="w-4 h-4" />
                            </a>

                            <form
                                action="{{ route('rusak.destroy', $rusak) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-secondary !p-1.5"
                                    style="color: #DC2626;"
                                    title="Hapus"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>

                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $rusak->tanggal->format('d M Y') }}</td>
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $rusak->detailPenerimaan->barang->nama }}</td>
                    <td class="px-5 py-3.5 font-mono text-gray-600">{{ $rusak->detailPenerimaan->no_batch }}</td>
                    <td class="px-5 py-3.5 text-right font-bold text-red-600">{{ $rusak->jumlah }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $rusak->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Belum ada data barang rusak.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $batches = $batches ?? \App\Models\DetailPenerimaan::with('barang')
            ->where('aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('expired_date')
            ->get();
    @endphp

    <!-- Modal Tambah Barang Rusak -->
    <div id="modal-tambah-rusak"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">

            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Catat Barang Rusak Baru
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Laporkan obat yang rusak, pecah, atau kadaluarsa untuk dikurangi dari stok batch.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeTambahRusak()"
                    class="text-gray-400 hover:text-gray-600 text-xl"
                >
                    &times;
                </button>
            </div>

            <form action="{{ route('rusak.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                @csrf

                @if ($errors->any())
                    <div class="p-3 mb-2 text-xs text-red-700 bg-red-100 rounded-lg">
                        <strong class="block font-bold mb-1">Perbaiki kesalahan berikut:</strong>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Batch Barang / Obat <span class="text-red-500">*</span></label>
                    <select name="detail_penerimaan_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-blue-500">
                        <option value="">Pilih Batch</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected(old('detail_penerimaan_id') == $batch->id)>
                                {{ $batch->barang->nama }} — Batch {{ $batch->no_batch }} (Sisa Stok: {{ $batch->stok }}, ED: {{ $batch->expired_date ? $batch->expired_date->format('d M Y') : '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('detail_penerimaan_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Lapor <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-blue-500">
                        @error('tanggal') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Rusak <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" min="1" value="{{ old('jumlah') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-blue-500" placeholder="Masukkan jumlah...">
                        @error('jumlah') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-blue-500" placeholder="Keterangan kerusakan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button
                        type="button"
                        onclick="closeTambahRusak()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-4 py-2 rounded-lg font-semibold transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-5 py-2 rounded-lg font-semibold shadow-sm transition-colors"
                    >
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Modal Detail Barang Rusak -->
    <div id="modal-detail-rusak"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">

            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Detail Barang Rusak
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Informasi pencatatan barang rusak.
                    </p>
                </div>

                <button type="button"
                        onclick="closeDetailRusak()"
                        class="text-gray-400 hover:text-gray-600 text-xl">
                    &times;
                </button>
            </div>

            <div id="detail-rusak-content" class="p-6">
            </div>

        </div>
    </div>

    <!-- Modal Edit Barang Rusak -->
    <div id="modal-edit-rusak"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">

            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Edit Barang Rusak
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Koreksi data pencatatan barang rusak.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeEditRusak()"
                    class="text-gray-400 hover:text-gray-600 text-xl"
                >
                    &times;
                </button>
            </div>

            <form id="form-edit-rusak" class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Tanggal Lapor
                        </label>

                        <input
                            type="date"
                            id="edit-tanggal-rusak"
                            name="tanggal"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Nama Barang
                        </label>

                        <input
                            type="text"
                            id="edit-barang-rusak"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-100"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            No. Batch
                        </label>

                        <input
                            type="text"
                            id="edit-batch-rusak"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-100 font-mono"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            No. Rak
                        </label>

                        <input
                            type="text"
                            id="edit-rak-rusak"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-100"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Jumlah Rusak
                        </label>

                        <input
                            type="number"
                            id="edit-jumlah-rusak"
                            name="jumlah"
                            min="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Expired Date
                        </label>

                        <input
                            type="text"
                            id="edit-expired-rusak"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs bg-gray-100"
                            readonly
                        >
                    </div>

                </div>

                <div class="mt-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Keterangan
                    </label>

                    <textarea
                        id="edit-keterangan-rusak"
                        name="keterangan"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button
                        type="button"
                        onclick="closeEditRusak()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-4 py-2 rounded-lg font-semibold"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-5 py-2 rounded-lg font-semibold"
                    >
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>



</div>

<div class="mt-4">{{ $rusaks->links() }}</div>

<script>
    const dataRusak = @json($rusaks->items());

    function formatTanggalRusak(tanggal) {
        if (!tanggal) {
            return '—';
        }

        const date = new Date(tanggal);

        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }


    function openDetailRusak(id) {
        const rusak = dataRusak.find(item => item.id === id);

        if (!rusak) {
            return;
        }

        const modal = document.getElementById('modal-detail-rusak');
        const content = document.getElementById('detail-rusak-content');

        content.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5 text-xs">

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        Tanggal Lapor
                    </span>
                    <strong class="text-gray-800 text-sm">
                        ${formatTanggalRusak(rusak.tanggal)}
                    </strong>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        Nama Barang
                    </span>
                    <strong class="text-gray-800 text-sm">
                        ${rusak.detail_penerimaan?.barang?.nama ?? '—'}
                    </strong>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        No. Batch
                    </span>
                    <strong class="text-gray-800 text-sm font-mono">
                        ${rusak.detail_penerimaan?.no_batch ?? '—'}
                    </strong>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        No. Rak
                    </span>
                    <strong class="text-gray-800 text-sm">
                        ${rusak.detail_penerimaan?.no_rak ?? '—'}
                    </strong>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        Expired Date
                    </span>
                    <strong class="text-gray-800 text-sm font-mono">
                        ${formatTanggalRusak(rusak.detail_penerimaan?.expired_date)}
                    </strong>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                        Jumlah Rusak
                    </span>
                    <strong class="text-red-600 text-sm font-bold">
                        ${rusak.jumlah}
                    </strong>
                </div>

            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                    Keterangan
                </span>

                <p class="text-sm text-gray-700">
                    ${rusak.keterangan ?? '—'}
                </p>
            </div>
        `;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailRusak() {
        const modal = document.getElementById('modal-detail-rusak');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openEditRusak(id) {
        window.editRusakId = id;
        fetch(`/rusak/${id}/edit`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data barang rusak.');
            }

            return response.json();
        })
        .then(rusak => {
            document.getElementById('edit-tanggal-rusak').value =
                rusak.tanggal ? rusak.tanggal.substring(0, 10) : '';

            document.getElementById('edit-barang-rusak').value =
                rusak.detail_penerimaan?.barang?.nama ?? '';

            document.getElementById('edit-batch-rusak').value =
                rusak.detail_penerimaan?.no_batch ?? '';

            document.getElementById('edit-rak-rusak').value =
                rusak.detail_penerimaan?.no_rak ?? '';

            document.getElementById('edit-expired-rusak').value =
                formatTanggalRusak(rusak.detail_penerimaan?.expired_date);

            document.getElementById('edit-jumlah-rusak').value =
                rusak.jumlah ?? '';

            document.getElementById('edit-keterangan-rusak').value =
                rusak.keterangan ?? '';

            document.getElementById('modal-edit-rusak').classList.remove('hidden');
            document.getElementById('modal-edit-rusak').classList.add('flex');
        })
        .catch(error => {
            console.error(error);
            alert('Gagal mengambil data barang rusak.');
        });
    }

    function closeEditRusak() {
        const modal = document.getElementById('modal-edit-rusak');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('form-edit-rusak').addEventListener('submit', function (event) {
    event.preventDefault();

    const form = this;

    fetch(`/rusak/${window.editRusakId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _method: 'PUT',
            tanggal: document.getElementById('edit-tanggal-rusak').value,
            jumlah: document.getElementById('edit-jumlah-rusak').value,
            keterangan: document.getElementById('edit-keterangan-rusak').value
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal menyimpan perubahan.');
        }

        closeEditRusak();
        window.location.reload();
    })
    .catch(error => {
        console.error(error);
        alert('Gagal menyimpan perubahan.');
    });
});

function openTambahRusak() {
    const modal = document.getElementById('modal-tambah-rusak');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeTambahRusak() {
    const modal = document.getElementById('modal-tambah-rusak');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.getElementById('modal-tambah-rusak')?.addEventListener('click', function (e) {
    if (e.target === this) {
        closeTambahRusak();
    }
});

@if ($errors->any())
    openTambahRusak();
@endif

</script>

@endsection
