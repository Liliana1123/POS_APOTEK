@extends('layouts.app')
@section('title', 'Backup Database')

@section('content')
<!-- Page Header -->
<x-page-header title="Backup Database" subtitle="Kelola backup database sistem untuk menjaga keamanan data Anda.">
    <button type="button"
            onclick="openUploadModal()"
            class="btn-secondary flex items-center gap-2"
            title="Restore database dari file .sql di komputer lokal">
        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
        <span>Restore dari File</span>
    </button>

    <form id="form-create-backup" action="{{ route('backup.store') }}" method="POST" class="inline">
        @csrf
        <button type="submit"
                id="btn-create-backup"
                class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Buat Backup</span>
        </button>
    </form>
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('backup.index')"
    :reset-url="route('backup.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari nama file backup..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">#</th>
                    <th scope="col">Nama File</th>
                    <th scope="col" class="w-40">Ukuran</th>
                    <th scope="col" class="w-52">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($backups as $index => $b)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                :delete-url="route('backup.destroy', $b['name'])"
                                :delete-item="$b['name']"
                                delete-confirm="Yakin ingin menghapus file backup ini?"
                            >
                                {{-- 1. Simpan ke local device (Download) --}}
                                <a href="{{ route('backup.download', $b['name']) }}"
                                   class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                   style="color: #2563EB;"
                                   title="Simpan ke local device"
                                   aria-label="Simpan ke local device">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                </a>

                                {{-- 2. Restore Database dari File Ini --}}
                                <button type="button"
                                        onclick="openRestoreModal('{{ $b['name'] }}')"
                                        class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                        style="color: #F59E0B;"
                                        title="Restore Database"
                                        aria-label="Restore Database">
                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                </button>
                            </x-table-action>
                        </td>
                        <td class="table-num">{{ sprintf('%02d', $index + 1) }}</td>
                        <td class="font-medium text-gray-800 font-mono">{{ $b['name'] }}</td>
                        <td class="text-gray-600 font-mono">{{ $b['size'] }}</td>
                        <td class="text-gray-600">{{ $b['date'] }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="5" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Konfirmasi Restore dari File Server -->
<div id="modal-restore-server"
     class="fixed inset-0 z-[9998] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-restore-title">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 text-center transform transition-all duration-200 scale-95 opacity-0 modal-content-box">
        <!-- Icon Peringatan Restore -->
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-amber-50 text-amber-600 mb-4 ring-8 ring-amber-50/50">
            <x-heroicon-o-exclamation-triangle class="w-7 h-7" />
        </div>

        <h3 id="modal-restore-title" class="text-lg font-bold text-gray-900 mb-2">
            Konfirmasi Restore Database
        </h3>

        <p class="text-sm text-gray-500 mb-4 leading-relaxed">
            <span class="font-semibold text-rose-600 block mb-1">PERHATIAN PENTING:</span>
            Memulihkan database akan <strong>menimpa seluruh data saat ini</strong> dengan data dari file backup. Pastikan tidak ada aktivitas kasir/transaksi yang sedang berlangsung.
        </p>

        <!-- Kotak Nama File -->
        <div class="mb-5 p-3 bg-amber-50/60 border border-amber-200 rounded-xl text-xs text-gray-700 flex items-center justify-center gap-2">
            <span class="text-amber-700 font-semibold shrink-0">File:</span>
            <span id="restore-filename-display" class="font-mono font-bold text-gray-900 truncate max-w-[280px]"></span>
        </div>

        <!-- Form Restore -->
        <form id="form-restore-server" action="{{ route('backup.restore') }}" method="POST">
            @csrf
            <input type="hidden" name="filename" id="restore-filename-input" value="">

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                        onclick="closeRestoreModal()"
                        class="btn-secondary w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        id="btn-confirm-restore"
                        class="w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-2 focus:ring-2 focus:ring-amber-500 focus:outline-none cursor-pointer">
                    <x-heroicon-o-arrow-path class="w-4 h-4 shrink-0" />
                    <span>Ya, Restore</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Upload & Restore dari File Komputer Lokal -->
<div id="modal-upload-restore"
     class="fixed inset-0 z-[9998] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-upload-title">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-lg w-full p-6 transform transition-all duration-200 scale-95 opacity-0 modal-upload-content-box">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                </div>
                <h3 id="modal-upload-title" class="text-base sm:text-lg font-bold text-gray-900">
                    Restore Database dari File Lokal
                </h3>
            </div>
            <button type="button" onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <p class="text-xs sm:text-sm text-gray-500 mb-4 leading-relaxed">
            Pilih file backup database berformat <strong>.sql</strong> dari komputer Anda. Fitur ini berguna saat memindahkan data atau memulihkan sistem setelah error fatal.
        </p>

        <form id="form-upload-restore" action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label for="file_sql" class="form-label block text-xs font-semibold text-gray-700 mb-1.5">
                    File SQL Backup <span class="text-red-500">*</span>
                </label>
                <div class="dropzone-modern p-5 text-center cursor-pointer group" onclick="document.getElementById('file_sql').click()">
                    <input type="file"
                           name="file_sql"
                           id="file_sql"
                           accept=".sql"
                           required
                           class="sr-only">
                    <div class="dropzone-default-content flex flex-col items-center justify-center space-y-1.5 transition-opacity">
                        <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                        </div>
                        <div class="text-xs font-semibold text-slate-700">
                            <span class="text-blue-600 underline">Pilih file SQL</span> atau seret file ke sini
                        </div>
                        <p class="text-[11px] text-gray-400">Format file harus .sql (Maks. 100 MB)</p>
                    </div>
                    <div class="dropzone-preview hidden mt-2 text-xs font-bold text-emerald-700 bg-emerald-50 py-1 px-2.5 rounded-lg border border-emerald-200 inline-block"></div>
                </div>
            </div>

            <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-800 leading-relaxed flex items-start gap-2">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <span>Seluruh data saat ini akan ditimpa dengan isi file SQL yang Anda pilih.</span>
            </div>

            <div class="flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="closeUploadModal()"
                        class="btn-secondary py-2 px-4 text-xs sm:text-sm font-semibold rounded-lg text-gray-700">
                    Batal
                </button>
                <button type="submit"
                        id="btn-submit-upload-restore"
                        class="btn-primary py-2 px-4 text-xs sm:text-sm font-semibold rounded-lg flex items-center gap-2 cursor-pointer">
                    <x-heroicon-o-arrow-path class="w-4 h-4 shrink-0" />
                    <span>Mulai Restore</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Indikator loading saat submit 'Buat Backup'
    const formCreate = document.getElementById('form-create-backup');
    const btnCreate = document.getElementById('btn-create-backup');
    if (formCreate && btnCreate) {
        formCreate.addEventListener('submit', function () {
            btnCreate.disabled = true;
            btnCreate.classList.add('opacity-75', 'cursor-not-allowed');
            btnCreate.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>Membuat Backup...</span>';
        });
    }

    // Modal Restore Server
    const modalRestore = document.getElementById('modal-restore-server');
    const restoreContent = modalRestore ? modalRestore.querySelector('.modal-content-box') : null;
    const restoreFilenameDisplay = document.getElementById('restore-filename-display');
    const restoreFilenameInput = document.getElementById('restore-filename-input');
    const formRestoreServer = document.getElementById('form-restore-server');
    const btnConfirmRestore = document.getElementById('btn-confirm-restore');

    window.openRestoreModal = function (filename) {
        if (!modalRestore) return;
        if (restoreFilenameDisplay) restoreFilenameDisplay.textContent = filename;
        if (restoreFilenameInput) restoreFilenameInput.value = filename;

        modalRestore.classList.remove('hidden');
        setTimeout(() => {
            modalRestore.classList.remove('opacity-0');
            if (restoreContent) {
                restoreContent.classList.remove('scale-95', 'opacity-0');
                restoreContent.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    };

    window.closeRestoreModal = function () {
        if (!modalRestore) return;
        if (restoreContent) {
            restoreContent.classList.remove('scale-100', 'opacity-100');
            restoreContent.classList.add('scale-95', 'opacity-0');
        }
        modalRestore.classList.add('opacity-0');
        setTimeout(() => {
            modalRestore.classList.add('hidden');
        }, 200);
    };

    if (formRestoreServer && btnConfirmRestore) {
        formRestoreServer.addEventListener('submit', function () {
            btnConfirmRestore.disabled = true;
            btnConfirmRestore.classList.add('opacity-75', 'cursor-not-allowed');
            btnConfirmRestore.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>Memulihkan...</span>';
        });
    }

    // Modal Upload & Restore
    const modalUpload = document.getElementById('modal-upload-restore');
    const uploadContent = modalUpload ? modalUpload.querySelector('.modal-upload-content-box') : null;
    const formUploadRestore = document.getElementById('form-upload-restore');
    const btnSubmitUploadRestore = document.getElementById('btn-submit-upload-restore');

    window.openUploadModal = function () {
        if (!modalUpload) return;
        modalUpload.classList.remove('hidden');
        setTimeout(() => {
            modalUpload.classList.remove('opacity-0');
            if (uploadContent) {
                uploadContent.classList.remove('scale-95', 'opacity-0');
                uploadContent.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    };

    window.closeUploadModal = function () {
        if (!modalUpload) return;
        if (uploadContent) {
            uploadContent.classList.remove('scale-100', 'opacity-100');
            uploadContent.classList.add('scale-95', 'opacity-0');
        }
        modalUpload.classList.add('opacity-0');
        setTimeout(() => {
            modalUpload.classList.add('hidden');
        }, 200);
    };

    if (formUploadRestore && btnSubmitUploadRestore) {
        formUploadRestore.addEventListener('submit', function () {
            btnSubmitUploadRestore.disabled = true;
            btnSubmitUploadRestore.classList.add('opacity-75', 'cursor-not-allowed');
            btnSubmitUploadRestore.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>Mengunggah & Memulihkan...</span>';
        });
    }

    // Event listener escape & backdrop
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (modalRestore && !modalRestore.classList.contains('hidden')) closeRestoreModal();
            if (modalUpload && !modalUpload.classList.contains('hidden')) closeUploadModal();
        }
    });

    if (modalRestore) {
        modalRestore.addEventListener('click', function (e) {
            if (e.target === modalRestore) closeRestoreModal();
        });
    }

    if (modalUpload) {
        modalUpload.addEventListener('click', function (e) {
            if (e.target === modalUpload) closeUploadModal();
        });
    }
</script>
@endpush
@endsection
