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
                    Status Penerimaan
                </label>

                <select name="status_penerimaan" class="form-input">
                    <option value="">Semua Status</option>

                    <option value="lengkap" @selected(request('status_penerimaan') === 'lengkap')>
                        Lengkap
                    </option>

                    <option value="belum_lengkap" @selected(request('status_penerimaan') === 'belum_lengkap')>
                        Belum Lengkap
                    </option>

                    <option value="selesai" @selected(request('status_penerimaan') === 'selesai')>
                        Selesai
                    </option>
                </select>
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
            @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal_mulai', 'tanggal_akhir', 'status_penerimaan', 'status_pembayaran']))
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
       <table class="table-custom min-w-[100rem]">
            <thead class="table-custom-header">
                <tr>
                    <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col" class="text-center">Aksi</th>
                    <th scope="col">No. Faktur & Tanggal Terima</th>
                    <th scope="col">Supplier</th>
                    <th scope="col" class="text-center">Status Penerimaan</th>
                    <th scope="col" class="text-right">Total Transaksi</th>
                    <th scope="col" class="text-right">Piutang</th>
                    <th scope="col" class="text-center">Tgl Jatuh Tempo</th>
                    <th scope="col" class="text-center">Status Pembayaran</th>
                </tr>
                </tr>
            </thead>
            <tbody class="table-custom-body ">
                @forelse ($penerimaans as $index => $penerimaan)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">

                    {{-- No --}}
                    <td class="text-center">
                        {{ $penerimaans->firstItem() + $index }}
                    </td>

                    {{-- Aksi --}}
                    <td class="text-left space-x-1.5">
                        <div class="flex items-center justify-start gap-1">

                            <button
                                type="button"
                                class="btn-secondary !p-1.5 btn-detail-penerimaan"
                                style="color: #2563EB;"
                                title="Lihat Detail"
                                data-url="{{ route('penerimaan.show', $penerimaan) }}"
                            >
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </button>

                            <a
                                href="{{ route('penerimaan.edit', $penerimaan) }}"
                                class="btn-secondary !p-1.5 btn-edit-penerimaan"
                                style="color: #F59E0B;"
                                title="Edit"
                            >
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </a>

                            @if (!$penerimaan->lunas)
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-payment-penerimaan"
                                    style="color: #16A34A;"
                                    title="Pembayaran"
                                    data-id="{{ $penerimaan->id }}"
                                >
                                    <x-heroicon-o-banknotes class="w-4 h-4" />
                                </button>
                            @endif

                            @if ($penerimaan->statusPenerimaan() === 'BELUM LENGKAP')
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-susulan-penerimaan"
                                    style="color: #7C3AED;"
                                    title="Penerimaan Susulan"
                                    data-id="{{ $penerimaan->id }}"
                                >
                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                </button>
                            @endif

                            <a
                                href="{{ route('penerimaan.print', $penerimaan) }}"
                                class="btn-secondary !p-1.5"
                                title="Print"
                                target="_blank"
                            >
                                <x-heroicon-o-printer class="w-4 h-4" />
                            </a>

                            <form
                                action="{{ route('penerimaan.destroy', $penerimaan) }}"
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

                    {{-- No Faktur & Tanggal Terima --}}
                    <td>
                        <div class="font-semibold text-gray-800 font-mono">
                            {{ $penerimaan->no_faktur }}
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">
                            {{ $penerimaan->tanggal?->format('d M Y') ?? '—' }}
                        </div>
                    </td>

                    {{-- Supplier --}}
                    <td class="font-medium text-gray-800">
                        {{ $penerimaan->supplier->nama ?? '—' }}
                    </td>

                    {{-- Status Penerimaan --}}
                    <td class="text-center">
                        @if ($penerimaan->statusPenerimaan() === 'LENGKAP')
                            <span class="badge-success">Lengkap</span>
                        @elseif ($penerimaan->statusPenerimaan() === 'BELUM LENGKAP')
                            <span class="badge-warning">Belum Lengkap</span>
                        @else
                            <span class="badge-secondary">Selesai</span>
                        @endif
                    </td>

                    {{-- Total Transaksi --}}
                    <td class="text-right font-medium">
                        Rp {{ number_format($penerimaan->totalTagihan(), 0, ',', '.') }}
                    </td>

                    {{-- Piutang --}}
                    <td class="text-right font-medium">
                        Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
                    </td>

                    {{-- Tanggal Jatuh Tempo --}}
                    <td class="text-center text-gray-600">
                        {{ $penerimaan->jatuh_tempo?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Status Pembayaran --}}
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
                        <td colspan="9" class="p-0">
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


<!-- Modal Penerimaan Susulan -->
<div
    id="modal-susulan-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true"
>
    <div class="modal-container-custom max-w-6xl">
        <div class="modal-header-custom">
            <h3 class="text-lg font-semibold">
                Penerimaan Susulan
            </h3>

            <button
                type="button"
                id="close-susulan-penerimaan"
                class="..."
            >
                &times;
            </button>
        </div>

        <div
            id="susulan-penerimaan-content"
            class="modal-body-custom"
        >
            Memuat...
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
    // MODAL SUSULAN
    // =========================
    const susulanModal = document.getElementById('modal-susulan-penerimaan');
    const susulanContent = document.getElementById('susulan-penerimaan-content');
    const closeSusulanButton = document.getElementById('close-susulan-penerimaan');

    // BUKA FORM SUSULAN
    document.querySelectorAll('.btn-susulan-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;

            susulanModal.classList.remove('hidden');

            susulanContent.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat formulir penerimaan susulan...
                </div>
            `;

            fetch(`/penerimaan/${id}/susulan-form`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat formulir penerimaan susulan.');
                    }

                    return response.text();
                })
                .then(html => {
                    susulanContent.innerHTML = html;
                })
                .catch(error => {
                    console.error(error);

                    susulanContent.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            ${error.message}
                        </div>
                    `;
                });
        });
    });

    // TUTUP FORM SUSULAN
    if (closeSusulanButton) {
        closeSusulanButton.addEventListener('click', function () {
            susulanModal.classList.add('hidden');
            susulanContent.innerHTML = '';
        });
    }

    // SIMPAN PENERIMAAN SUSULAN
    document.addEventListener('submit', function (event) {
        if (event.target.id !== 'form-susulan-penerimaan') {
            return;
        }

        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('#btn-simpan-susulan');

        if (!button || button.disabled) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        data.errors?.jumlah_susulan?.[0] ||
                        'Gagal menyimpan penerimaan susulan.'
                    );
                }

                return data;
            })
            .then(data => {
                susulanModal.classList.add('hidden');
                susulanContent.innerHTML = '';

                window.location.reload();
            })
            .catch(error => {
                console.error('Penerimaan susulan:', error);

                alert(error.message || 'Gagal menyimpan penerimaan susulan.');

                button.disabled = false;
                button.textContent = 'Simpan Penerimaan Susulan';
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
