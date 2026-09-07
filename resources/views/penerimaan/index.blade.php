@extends('layouts.app')
@section('title', 'Penerimaan Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Penerimaan Barang</h1>
        <p class="text-caption mt-1">Kelola data faktur obat masuk, supplier, dan status pembayaran.</p>
    </div>
    <a href="{{ route('penerimaan.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Faktur Penerimaan Baru</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('penerimaan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">No. Faktur</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari no. faktur..."
                        class="form-input pr-8 font-mono">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Supplier</label>
                <select name="supplier_id" class="form-input">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Penerimaan
                </label>

                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                        class="form-input min-w-0"
                    >

                    <span class="text-sm text-gray-400">-</span>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}"
                        class="form-input min-w-0"
                    >
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Pembayaran
                </label>

                <select name="status_pembayaran" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="lunas" @selected(request('status_pembayaran') === 'lunas')>
                        Lunas
                    </option>
                    <option value="belum_lunas" @selected(request('status_pembayaran') === 'belum_lunas')>
                        Belum Lunas
                    </option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal_mulai', 'tanggal_akhir', 'status_pembayaran']))
                <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[72rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col">No. Faktur</th>
                    <th scope="col">Tanggal Terima</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Dicatat Oleh</th>
                    <th scope="col" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body ">
                @forelse ($penerimaans as $index => $penerimaan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left space-x-1.5">
                            <div class="flex items-center justify-star gap-1">
                                <button type="button"class="btn-secondary !p-1.5 btn-detail-penerimaan" style="color: #2563EB;" title="Lihat Detail" data-url="{{ route('penerimaan.show', $penerimaan) }}"><x-heroicon-o-eye class="w-4 h-4" /></button>
                                <a href="{{ route('penerimaan.edit', $penerimaan) }}"
                                    class="btn-secondary !p-1.5 btn-edit-penerimaan"
                                    style="color: #F59E0B;"
                                    title="Edit">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                @if (!$penerimaan->lunas)
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-payment-penerimaan" style="color: #16A34A;"
                                    title="Pembayaran"
                                    data-id="{{ $penerimaan->id }}">
                                    <x-heroicon-o-banknotes class="w-4 h-4" />
                                </button>@endif
                                <a href="{{ route('penerimaan.print', $penerimaan) }}"
                                    class="btn-secondary !p-1.5"
                                    title="Print"
                                    target="_blank">
                                    <x-heroicon-o-printer class="w-4 h-4" />
                                </a>

                                <form action="{{ route('penerimaan.destroy', $penerimaan) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary !p-1.5" style="color: #DC2626;" title="Hapus">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="font-semibold text-gray-800 font-mono">{{ $penerimaan->no_faktur }}</td>
                        <td class="text-gray-600">{{ $penerimaan->tanggal->format('d M Y') }}</td>
                        <td class="font-medium text-gray-800">{{ $penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="text-gray-600">{{ $penerimaan->user->name ?? '—' }}</td>
                        <td class="text-center">
                            @if ($penerimaan->lunas)
                                <span class="badge-success">Lunas</span>
                            @else
                                <span class="badge-warning">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'status_pembayaran']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'status_pembayaran']))
                                        Tidak ada faktur penerimaan yang cocok dengan filter kriteria Anda.
                                    @else
                                        Belum ada data faktur masuk terdaftar di sistem.
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

<div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <span>Tampilkan</span>

        <select
            name="per_page"
            class="form-input py-1.5 w-20"
            onchange="this.form.submit()"
        >
            <option value="10" @selected(request('per_page', 15) == 10)>10</option>
            <option value="15" @selected(request('per_page', 15) == 15)>15</option>
            <option value="25" @selected(request('per_page', 15) == 25)>25</option>
            <option value="50" @selected(request('per_page', 15) == 50)>50</option>
            <option value="100" @selected(request('per_page', 15) == 100)>100</option>
        </select>

        <span>data</span>
    </div>

    <div>
        {{ $penerimaans->links() }}
    </div>
</div>

<!-- Modal Detail Penerimaan -->
<div id="modal-detail-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true">

    <div class="modal-container-custom max-w-5xl">

        <div class="modal-header-custom">
            <div>
                <h2>Detail Penerimaan</h2>
                <p class="text-caption mt-1">
                    Informasi lengkap faktur penerimaan barang.
                </p>
            </div>

            <button type="button"
                id="btn-tutup-detail"
                class="btn-secondary !p-1.5"
                title="Tutup">
                ✕
            </button>
        </div>

        <div id="detail-penerimaan-content" class="modal-body-custom">
            <div class="text-center py-8 text-gray-500">
                Memuat detail...
            </div>
        </div>

    </div>
</div>


<!-- Modal Pembayaran Penerimaan -->
<div id="modal-payment-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true">

    <div class="modal-container-custom max-w-lg">

        <div class="modal-header-custom">
            <div>
                <h2>Pembayaran Penerimaan</h2>
                <p class="text-caption mt-1">
                    Catat pembayaran untuk faktur penerimaan.
                </p>
            </div>

            <button type="button"
                id="btn-tutup-payment"
                class="btn-secondary !p-1.5"
                title="Tutup">
                ✕
            </button>
        </div>

        <div id="payment-penerimaan-content" class="modal-body-custom">
            <div class="text-center py-8 text-gray-500">
                Memuat pembayaran...
            </div>
        </div>

    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal-detail-penerimaan');
    const content = document.getElementById('detail-penerimaan-content');
    const btnTutup = document.getElementById('btn-tutup-detail');

    const paymentModal = document.getElementById('modal-payment-penerimaan');
    const paymentContent = document.getElementById('payment-penerimaan-content');
    const btnTutupPayment = document.getElementById('btn-tutup-payment');

    // =========================
    // MODAL DETAIL PENERIMAAN
    // =========================
    document.querySelectorAll('.btn-detail-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = button.dataset.url;

            modal.classList.remove('hidden');

            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat detail...
                </div>
            `;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil detail penerimaan.');
                    }

                    return response.text();
                })
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Gagal memuat detail penerimaan.
                        </div>
                    `;

                    console.error(error);
                });
        });
    });

    // =========================
    // MODAL PEMBAYARAN
    // =========================
    document.querySelectorAll('.btn-payment-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;

            paymentModal.classList.remove('hidden');

            paymentContent.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat pembayaran...
                </div>
            `;

            fetch(`/penerimaan/${id}/payment-form`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil form pembayaran.');
                    }

                    return response.text();
                })
                .then(html => {
                    paymentContent.innerHTML = html;
                })
                .catch(error => {
                    paymentContent.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Gagal memuat form pembayaran.
                        </div>
                    `;

                    console.error(error);
                });
        });
    });

    // =========================
    // TUTUP MODAL DETAIL
    // =========================
    btnTutup.addEventListener('click', function () {
        modal.classList.add('hidden');
        content.innerHTML = '';
    });

    // =========================
    // TUTUP MODAL PEMBAYARAN
    // =========================
    btnTutupPayment.addEventListener('click', function () {
        paymentModal.classList.add('hidden');
        paymentContent.innerHTML = '';
    });
});
</script>



@endsection
