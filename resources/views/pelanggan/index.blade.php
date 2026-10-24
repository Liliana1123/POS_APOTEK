@extends('layouts.app')
@section('title', 'Pelanggan atau Member')

@section('content')
<!-- Page Header -->
<x-page-header title="Daftar Pelanggan atau Member" subtitle="Kelola data pelanggan dan member POS Apotek.">
    @if(auth()->user()->canManage('master.pelanggan'))
        <button type="button" id="btn-tambah-pelanggan" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Pelanggan</span>
        </button>
    @endif
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('pelanggan.index')"
    :reset-url="route('pelanggan.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-5"
    :live-search="true"
    :has-reset="request()->filled('cari') || $status !== 'semua' || $statusPiutang !== 'semua'">
    <x-search-input name="cari" placeholder="Cari nama, nomor HP, ID..." class="sm:col-span-2 md:col-span-2" />

    <select name="status" class="form-input" aria-label="Status Member" title="Status Member">
        <option value="semua" @selected($status === 'semua')>Member: Semua</option>
        <option value="pelanggan_tetap" @selected($status === 'pelanggan_tetap')>Member Pelanggan Tetap</option>
        <option value="keluarga_nakes" @selected($status === 'keluarga_nakes')>Member Keluarga Nakes</option>
        <option value="member_only" @selected($status === 'member_only')>Member Only</option>
        <option value="umum" @selected($status === 'umum')>Pelanggan Umum</option>
    </select>

    <select name="status_piutang" class="form-input" aria-label="Status Piutang" title="Status Piutang">
        <option value="semua" @selected($statusPiutang === 'semua')>Piutang: Semua</option>
        <option value="lunas" @selected($statusPiutang === 'lunas')>Lunas</option>
        <option value="belum_lunas" @selected($statusPiutang === 'belum_lunas')>Belum Lunas</option>
        <option value="terlambat" @selected($statusPiutang === 'terlambat')>Terlambat</option>
    </select>
</x-card-filter>

<!-- Bar Aksi Pilihan WhatsApp Massal -->
<div id="bulkWhatsAppActionBar" class="hidden mb-3 items-center justify-between bg-blue-50 border border-blue-200 shadow-sm rounded-xl px-4 py-2.5 transition-all">
    <div class="flex items-center gap-3">
        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">
            <x-heroicon-s-check class="w-4 h-4" />
        </div>
        <div>
            <span class="font-bold text-gray-800 text-xs sm:text-sm">
                <span id="bulkSelectedCount">0</span> member dipilih
            </span>
            <span class="text-xs text-gray-500 ml-2 border-l border-gray-300 pl-2">
                Total piutang <strong id="bulkSelectedTotalPiutang" class="text-red-600 font-bold">Rp 0</strong>
            </span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="button"
            onclick="openWhatsAppCategoryModal()"
            id="btnOpenBulkWhatsAppModal"
            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
            </svg>
            <span>Kirim WhatsApp (<span id="bulkBtnCountText">0</span>)</span>
        </button>

        <button type="button"
            onclick="deselectAllMember()"
            class="text-xs font-semibold text-gray-500 hover:text-gray-700 underline transition">
            Hapus Pilihan
        </button>
    </div>
</div>

<!-- Table Custom Wrapper --> 
<div class="table-custom-container w-full min-w-0 max-w-full">
    <div class="table-pelanggan-wrapper">
        <table class="table-custom table-pelanggan">
            <colgroup>
                <col style="width: 250px;">
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
                    <th scope="col" class="text-left whitespace-nowrap pl-3" style="width: 250px;">
                        <div class="flex items-center justify-start gap-2">
                            <input type="checkbox"
                                id="checkAllMember"
                                class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer"
                                title="Pilih semua member yang memenuhi syarat di halaman ini"
                                onchange="toggleSelectAllMember(this)">
                            <span class="uppercase tracking-wider">Aksi</span>
                        </div>
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
                        <td class="text-left space-x-1.5 whitespace-nowrap pl-3">
                            <div class="flex items-center justify-start gap-1">
                                @php
                                    $pengingat = $pelanggan->pengingat_piutang;
                                    $hasPiutang = ($pengingat !== null && ($pengingat['total_sisa'] ?? 0) > 0);
                                    $sisaPiutangNilai = $pengingat ? $pengingat['total_sisa'] : ($pelanggan->saldo_piutang ?? 0);
                                    $isActiveMember = ($pelanggan->is_member && $pelanggan->member_aktif);
                                @endphp

                                {{-- Checkbox Pemilihan Massal --}}
                                @if($isActiveMember)
                                    <input type="checkbox"
                                        class="member-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer mr-1"
                                        value="{{ $pelanggan->id }}"
                                        data-id="{{ $pelanggan->id }}"
                                        data-nama="{{ $pelanggan->nama }}"
                                        data-telepon="{{ $pelanggan->telepon }}"
                                        data-has-piutang="{{ $hasPiutang ? '1' : '0' }}"
                                        data-sisa="{{ $sisaPiutangNilai }}"
                                        data-sisa-formatted="Rp {{ number_format($sisaPiutangNilai, 0, ',', '.') }}"
                                        data-jatuh-tempo="{{ $pengingat['jatuh_tempo_pesan'] ?? '-' }}"
                                        title="Pilih member ini untuk pesan WhatsApp"
                                        onchange="handleMemberCheckboxChange()">
                                @else
                                    <input type="checkbox"
                                        disabled
                                        class="w-4 h-4 rounded text-gray-300 border-gray-200 bg-gray-100 opacity-40 cursor-not-allowed mr-1"
                                        title="Bukan member aktif.">
                                @endif

                                <!-- 1. Detail Modal Button -->
                                <button
                                    type="button"
                                    onclick="openDetailModal({{ $pelanggan->id }})"
                                    class="btn-secondary !p-1.5"
                                    style="color: #2563EB;"
                                    title="Lihat Detail"
                                    aria-label="Lihat Detail">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                @php
                                    $canManagePelanggan = auth()->user()->canManage('master.pelanggan');
                                @endphp

                                <!-- 2. Edit Modal Button -->
                                @if($canManagePelanggan)
                                    <button
                                        type="button"
                                        class="btn-secondary !p-1.5 btn-edit-pelanggan"
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
                                @endif

                                <!-- 3. Print Card Button -->
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-print-member-card"
                                    style="color: #000000;"
                                    title="Cetak Kartu Member"
                                    aria-label="Cetak Kartu Member"
                                    data-nama="{{ $pelanggan->nama }}"
                                    data-member-id="{{ $pelanggan->member_id }}"
                                    data-total-belanja="{{ $pelanggan->total_belanja ?? 0 }}"
                                    data-status-member="{{ $pelanggan->status_member }}"
                                    data-custom-discount="{{ $pelanggan->custom_discount_percentage }}"
                                    data-is-active="{{ ($pelanggan->is_member && $pelanggan->member_aktif) ? '1' : '0' }}"
                                    data-discount="{{ $pelanggan->diskon_percent }}"
                                    onclick="openCardModalFromBtn(this)">
                                    <x-heroicon-o-printer class="w-4 h-4" />
                                </button>

                                <!-- 4. Piutang Payment Button -->
                                @if($pelanggan->is_member && $pelanggan->member_aktif && ($pelanggan->saldo_piutang ?? 0) > 0)
                                    <button
                                        type="button"
                                        class="btn-secondary !p-1.5"
                                        style="color: #16A34A;"
                                        title="Pembayaran Piutang"
                                        aria-label="Pembayaran Piutang"
                                        onclick="openPiutangPaymentModal({{ $pelanggan->id }})">
                                        <x-heroicon-o-banknotes class="w-4 h-4" />
                                    </button>
                                @endif

                                <!-- 5. WhatsApp Button (Tampil di setiap baris member tanpa terkecuali) -->
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5"
                                    style="color: #10B981;"
                                    title="Kirim Pesan WhatsApp ke {{ $pelanggan->nama }}"
                                    aria-label="Kirim Pesan WhatsApp ke {{ $pelanggan->nama }}"
                                    onclick="openIndividualWhatsAppModal({{ $pelanggan->id }})">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                                    </svg>
                                </button>

                                <!-- 6. Delete Button -->
                                @if($canManagePelanggan)
                                    <form action="{{ route('pelanggan.destroy', $pelanggan) }}" method="POST" onsubmit="return confirm('Hapus data pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-secondary !p-1.5"
                                            style="color: #DC2626;"
                                            title="Hapus"
                                            aria-label="Hapus">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
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
                                <div class="font-medium text-gray-800 text-xs whitespace-nowrap">@tanggalId($due)</div>
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

<div class="mt-4 pagination-container">{{ $pelanggans->links('vendor.pagination.apotek') }}</div>

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
    <x-form.input name="nama" label="Nama Pelanggan" required placeholder="Ketik nama membership..." />
    <x-form.input name="telepon" label="Nomor HP / Telepon" required placeholder="Contoh: 08123456789" />
    <x-form.textarea name="alamat" label="Alamat" required rows="2" />
    <x-form.input type="date" name="tanggal_lahir" label="Tanggal Lahir" />
    <div id="status-member-field">
        <x-form.select name="status_member" label="Status Member" required placeholder="Pilih status member" :options="[
            'Member Pelanggan Tetap' => 'Member Pelanggan Tetap',
            'Member Keluarga Nakes' => 'Member Keluarga Nakes',
            'Member Only' => 'Member Only'
        ]" />
    </div>
    <x-form.input type="number" name="custom_discount_percentage" label="Custom Diskon (%)" min="0" max="100" step="any" placeholder="Kosongkan untuk diskon default 10%" hint="Kosongkan untuk diskon default 10%." />
</x-modal-form>

<!-- Modal Partials -->
@include('pelanggan.partials.modal-edit')
@include('pelanggan.partials.modal-detail')
@include('pelanggan.partials.modal-card')
@include('pelanggan.partials.modal-piutang')
@include('pelanggan.partials.modal-whatsapp-piutang')
@endsection
