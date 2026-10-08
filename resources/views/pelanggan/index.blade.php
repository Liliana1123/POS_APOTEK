@extends('layouts.app')
@section('title', 'Pelanggan atau Member')

@section('content')
<!-- Page Header -->
<x-page-header title="Daftar Pelanggan atau Member" subtitle="Kelola data pelanggan dan member POS Apotek.">
    <button type="button" id="btn-tambah-pelanggan" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Pelanggan</span>
    </button>
    
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('pelanggan.index')"
    :reset-url="route('pelanggan.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3"
    :has-reset="request()->filled('cari') || $status !== 'semua' || $statusPiutang !== 'semua'">
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Cari Pelanggan / Member
        </label>
        <div class="relative">
            <input
                type="text"
                name="cari"
                value="{{ request('cari') }}"
                placeholder="Cari nama, nomor HP, ID..."
                class="form-input pr-8">
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
            <option value="semua" @selected($status === 'semua')>Semua</option>
            <option value="pelanggan_tetap" @selected($status === 'pelanggan_tetap')>Member Pelanggan Tetap</option>
            <option value="keluarga_nakes" @selected($status === 'keluarga_nakes')>Member Keluarga Nakes</option>
            <option value="member_only" @selected($status === 'member_only')>Member Only</option>
        </select>
    </div>

    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Status Piutang
        </label>
        <select name="status_piutang" class="form-input">
            <option value="semua" @selected($statusPiutang === 'semua')>Semua</option>
            <option value="lunas" @selected($statusPiutang === 'lunas')>Lunas</option>
            <option value="belum_lunas" @selected($statusPiutang === 'belum_lunas')>Belum Lunas</option>
            <option value="terlambat" @selected($statusPiutang === 'terlambat')>Terlambat</option>
        </select>
    </div>
</x-card-filter>

<!-- Table Custom Wrapper --> 
<div class="table-custom-container w-full min-w-0 max-w-full">
    <div class="table-pelanggan-wrapper">
        <table class="table-custom table-pelanggan">
            <colgroup>
                <col style="width: 190px;">
                <col style="width: 120px;">
                <col style="width: auto; min-width: 180px;">
                <col style="width: 165px;">
                <col style="width: 110px;">
                <col style="width: 115px;">
                <col style="width: 125px;">
                <col style="width: 135px;">
            </colgroup>
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center whitespace-nowrap" style="width: 190px;">
                        Aksi
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 120px;">
                        ID Pelanggan
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="min-width: 180px;">
                        Nama & No Telp
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 165px;">
                        Status Member
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 110px;">
                        Status Piutang
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 115px;">
                        Jatuh Tempo
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 125px;">
                        Total Diskon
                    </th>

                    <th scope="col" class="text-center whitespace-nowrap" style="width: 135px;">
                        Total Belanja
                    </th>
                </tr>
            </thead>
                        
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($pelanggans as $index => $pelanggan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-center whitespace-nowrap">
                            <x-table-action
                                align="justify-center flex-nowrap"
                                :delete-url="route('pelanggan.destroy', $pelanggan)"
                                delete-confirm="Hapus data pelanggan ini?">
                                
                                <!-- 1. Detail Modal Button -->
                                <button
                                    type="button"
                                    onclick="openDetailModal({{ $pelanggan->id }})"
                                    class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors shrink-0"
                                    style="color: #2563EB;"
                                    title="Lihat Detail"
                                    aria-label="Lihat Detail">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                <!-- 2. Edit Modal Button -->
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transition-colors btn-edit-pelanggan shrink-0"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    aria-label="Edit"
                                    data-id="{{ $pelanggan->id }}"
                                    data-nama="{{ $pelanggan->nama }}"
                                    data-telepon="{{ $pelanggan->telepon }}"
                                    data-alamat="{{ $pelanggan->alamat }}"
                                    data-tanggal_lahir="{{ $pelanggan->tanggal_lahir }}"
                                    data-status_member="{{ $pelanggan->status_member }}"
                                    data-custom_discount_percentage="{{ $pelanggan->custom_discount_percentage }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>

                                <!-- 3. Print Card Button -->
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors shrink-0"
                                    style="color: #000000;"
                                    title="Cetak Kartu Member"
                                    aria-label="Cetak Kartu Member"
                                    onclick="openCardModal(@js($pelanggan->nama), @js($pelanggan->member_id), @js($pelanggan->total_belanja ?? 0), @js($pelanggan->status_member), @js($pelanggan->custom_discount_percentage))">
                                    <x-heroicon-o-printer class="w-4 h-4" />
                                </button>

                                <!-- 4. Piutang Payment Button -->
                                @if($pelanggan->is_member && $pelanggan->member_aktif && ($pelanggan->saldo_piutang ?? 0) > 0)
                                    <button
                                        type="button"
                                        class="btn-secondary !p-1.5 hover:bg-green-50 hover:border-green-300 transition-colors shrink-0"
                                        style="color: #16A34A;"
                                        title="Pembayaran Piutang"
                                        aria-label="Pembayaran Piutang"
                                        onclick="openPiutangPaymentModal({{ $pelanggan->id }})">
                                        <x-heroicon-o-banknotes class="w-4 h-4" />
                                    </button>
                                @endif
                            </x-table-action>
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="font-mono text-gray-700 font-semibold text-xs">{{ $pelanggan->member_id ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="font-medium text-gray-800 text-xs truncate max-w-full" title="{{ $pelanggan->nama }}">{{ $pelanggan->nama }}</div>
                            <div class="text-[11px] text-gray-500 font-mono mt-0.5 whitespace-nowrap">{{ $pelanggan->telepon ?? '-' }}</div>
                        </td>
                        <td class="text-center whitespace-nowrap">
                            @php
                                $statusMember = trim((string) $pelanggan->status_member);
                                $memberVariant = match($statusMember) {
                                    'Member Keluarga Nakes' => 'info',
                                    'Member Only' => 'warning',
                                    default => 'success',
                                };
                            @endphp
                            <x-badge :variant="$memberVariant" class="whitespace-nowrap">
                                {{ $statusMember ?: 'Member Pelanggan Tetap' }}
                            </x-badge>
                        </td>
                        <td class="text-center whitespace-nowrap">
                            @if(($pelanggan->saldo_piutang ?? 0) > 0)
                                <x-badge variant="danger" class="whitespace-nowrap">Belum Lunas</x-badge>
                            @else
                                <x-badge variant="success" class="whitespace-nowrap">Lunas</x-badge>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            @php
                                $jatuhTempo = $pelanggan->jatuh_tempo_aktif;
                            @endphp
                            @if ($jatuhTempo)
                                @php
                                    $today = \Illuminate\Support\Carbon::today();
                                    $due = \Illuminate\Support\Carbon::parse($jatuhTempo);
                                @endphp
                                <div class="font-medium text-gray-800 text-xs whitespace-nowrap">{{ $due->format('d M Y') }}</div>
                                @if ($due->lt($today))
                                    <span class="inline-flex items-center text-[10px] font-semibold text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded mt-0.5 whitespace-nowrap">
                                        Terlambat
                                    </span>
                                @elseif ($due->isSameDay($today))
                                    <span class="inline-flex items-center text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded mt-0.5 whitespace-nowrap">
                                        Hari ini
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-medium text-gray-500 mt-0.5 whitespace-nowrap">
                                        Belum jatuh tempo
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 font-mono text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-center font-semibold text-green-600 whitespace-nowrap text-xs">
                            Rp {{ number_format($pelanggan->total_diskon ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-semibold text-gray-800 whitespace-nowrap text-xs">
                            Rp {{ number_format($pelanggan->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="8" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $pelanggans->links() }}</div>

<!-- Modal Tambah Pelanggan -->
<x-modal-form
    id="modal-pelanggan"
    create-title="Tambah Pelanggan / Member"
    edit-title="Edit Pelanggan / Member"
    create-url="{{ route('pelanggan.store') }}"
    update-base="{{ url('pelanggan') }}"
    create-btn="#btn-tambah-pelanggan"
    edit-btn=""
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
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Status Member <span class="text-red-500">*</span></label>
        <select name="status_member" required class="form-input">
            <option value="">Pilih status member</option>
            <option value="Member Pelanggan Tetap">Member Pelanggan Tetap</option>
            <option value="Member Keluarga Nakes">Member Keluarga Nakes</option>
            <option value="Member Only">Member Only</option>
        </select>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="status_member"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Custom Diskon (%)</label>
        <input type="number" name="custom_discount_percentage" min="0" max="100" step="any" class="form-input" placeholder="Kosongkan untuk diskon default 10%">
        <p class="text-[11px] text-gray-400 mt-1 font-sans">Kosongkan untuk diskon default 10%.</p>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="custom_discount_percentage"></p>
    </div>
</x-modal-form>

<!-- Modal Partials -->
@include('pelanggan.partials.modal-edit')
@include('pelanggan.partials.modal-detail')
@include('pelanggan.partials.modal-card')
@include('pelanggan.partials.modal-piutang')
@endsection
