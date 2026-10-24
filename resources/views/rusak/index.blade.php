@extends('layouts.app')
@section('title', 'Barang Rusak')

@section('content')
<x-page-header title="Daftar Pencatatan Barang Rusak" subtitle="Kelola dan laporkan obat rusak atau kadaluarsa.">
    <button type="button" onclick="openTambahRusak()" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Catat Barang Rusak</span>
    </button>
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('rusak.index')"
    :reset-url="route('rusak.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-8"
    action-cols="sm:col-span-2 md:col-span-2"
    :live-search="true"
    :has-reset="request()->anyFilled(['cari', 'no_batch', 'tanggal_mulai', 'tanggal_akhir', 'tanggal'])">
    @if(request()->filled('tanggal'))
        <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
    @endif

    <div class="relative sm:col-span-2 md:col-span-3">
        <input
            type="text"
            name="cari"
            value="{{ request('cari', request('no_batch')) }}"
            placeholder="Cari nama barang atau no. batch..."
            class="form-input pr-8"
            aria-label="Cari barang atau no. batch"
            title="Cari barang atau no. batch"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <div class="flex items-center gap-1.5 sm:col-span-2 md:col-span-3">
        <input
            type="date"
            name="tanggal_mulai"
            value="{{ request('tanggal_mulai', $tanggalMulai) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="text-sm text-gray-400">-</span>
        <input
            type="date"
            name="tanggal_akhir"
            value="{{ request('tanggal_akhir', $tanggalAkhir) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>
</x-card-filter>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col" class="text-center">Aksi</th>
                    <th scope="col">Tanggal Lapor</th>
                    <th scope="col">Barang atau Obat</th>
                    <th scope="col">No. Batch</th>
                    <th scope="col" class="text-right">Jumlah</th>
                    <th scope="col">Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($rusaks as $index => $rusak)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">

                    {{-- No --}}
                    <td class="text-center">
                        {{ $rusaks->firstItem() + $index }}
                    </td>

                    {{-- Aksi --}}
                    <td class="text-left space-x-1.5">
                        <div class="flex items-center justify-start gap-1">

                            <button
                                type="button"
                                class="btn-secondary !p-1.5"
                                style="color: #2563EB;"
                                title="Lihat Detail"
                                onclick="openDetailRusak({{ $rusak->id }})"
                            >
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

                            <button
                                type="button"
                                class="btn-secondary !p-1.5 cursor-pointer hover:bg-red-50 hover:border-red-300 transition-colors"
                                style="color: #DC2626;"
                                title="Hapus"
                                aria-label="Hapus"
                                data-confirm-delete="{{ route('rusak.destroy', $rusak) }}"
                                data-item-name="{{ $rusak->detailPenerimaan?->barang?->nama ?? 'Barang Rusak' }} (Batch: {{ $rusak->detailPenerimaan?->no_batch ?? '-' }})"
                                data-confirm-message="Apakah Anda yakin ingin menghapus data barang rusak ini? Stok akan dikembalikan ke batch terkait.">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>

                        </div>
                    </td>

                    {{-- Tanggal Lapor --}}
                    <td class="text-gray-600">
                        @tanggalId($rusak->tanggal, 'd F Y')
                    </td>

                    {{-- Barang / Obat --}}
                    <td class="font-medium text-gray-800">
                        {{ $rusak->detailPenerimaan->barang->nama ?? '—' }}
                    </td>

                    {{-- No. Batch --}}
                    <td class="font-mono text-gray-600">
                        {{ $rusak->detailPenerimaan->no_batch ?? '—' }}
                    </td>

                    {{-- Jumlah --}}
                    <td class="text-right font-bold text-red-600">
                        {{ $rusak->jumlah }}
                    </td>

                    {{-- Keterangan --}}
                    <td class="text-gray-600">
                        {{ $rusak->keterangan ?? '-' }}
                    </td>
                </tr>
                @empty
                    <x-empty-state colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$rusaks" />

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
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Batch Barang atau Obat <span class="text-red-500">*</span></label>
                    <select name="detail_penerimaan_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-blue-500">
                        <option value="">Pilih Batch</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected(old('detail_penerimaan_id') == $batch->id)>
                                {{ $batch->barang->nama }} — Batch {{ $batch->no_batch }} (Sisa Stok: {{ $batch->stok }}, ED: {{ $batch->expired_date ? $batch->expired_date->translatedFormat('d F Y') : '-' }})
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
    <x-modal
        id="modal-detail-rusak"
        title="Detail Barang Rusak"
        subtitle="Informasi pencatatan barang rusak."
        size="max-w-2xl"
        close-action="closeDetailRusak()"
        content-id="detail-rusak-content"
        body-class="p-6" />

    <!-- Modal Edit Barang Rusak -->
    <x-modal
        id="modal-edit-rusak"
        title="Edit Barang Rusak"
        subtitle="Koreksi data pencatatan barang rusak."
        size="max-w-2xl"
        close-action="closeEditRusak()"
        body-class="p-0">

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
    </x-modal>


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
    .then(async response => {
        if (!response.ok) {
            let errorMsg = 'Gagal menyimpan perubahan.';
            try {
                const errData = await response.json();
                if (errData?.errors) {
                    const firstErr = Object.values(errData.errors).flat()[0];
                    if (firstErr) errorMsg = firstErr;
                } else if (errData?.message) {
                    errorMsg = errData.message;
                }
            } catch (e) {}
            throw new Error(errorMsg);
        }

        closeEditRusak();
        window.location.reload();
    })
    .catch(error => {
        console.error(error);
        alert(error.message || 'Gagal menyimpan perubahan.');
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
