<!-- Modal Edit Pelanggan / Member -->
<div id="modal-edit-pelanggan"
    class="modal-backdrop-custom hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">

    <div class="modal-container-custom w-full mx-4 bg-white rounded-xl shadow-xl max-h-[92vh] overflow-hidden" style="max-width: 560px;">

        <!-- HEADER -->
        <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Edit Pelanggan / Member</h3>
                </div>
            </div>

            <button
                type="button"
                id="close-edit-pelanggan"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                title="Tutup"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- CONTENT -->
        <div class="overflow-y-auto max-h-[calc(92vh-132px)]">
            <form id="form-edit-pelanggan" novalidate>
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="px-5 py-5">
                    <div id="edit-pelanggan-error" class="hidden mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs text-red-700"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">

                        <!-- Nama -->
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                <x-heroicon-o-user class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" required class="form-input w-full" placeholder="Masukkan nama pelanggan">
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="nama"></p>
                            </div>
                        </div>

                        <!-- No Telp -->
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                <x-heroicon-o-phone class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">No. Telp <span class="text-red-500">*</span></label>
                                <input type="text" name="telepon" required class="form-input w-full" placeholder="Contoh: 08123456789">
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="telepon"></p>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="flex items-start gap-3 min-w-0 md:col-span-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                <x-heroicon-o-map-pin class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="alamat" required class="form-input w-full" rows="2" placeholder="Masukkan alamat pelanggan"></textarea>
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="alamat"></p>
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                <x-heroicon-o-calendar-days class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-input w-full">
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="tanggal_lahir"></p>
                            </div>
                        </div>

                        <!-- Status Member -->
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                <x-heroicon-o-identification class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">Status Member <span class="text-red-500">*</span></label>
                                <select name="status_member" required class="form-input w-full">
                                    <option value="">Pilih status member</option>
                                    <option value="Member Pelanggan Tetap">Member Pelanggan Tetap</option>
                                    <option value="Member Keluarga Nakes">Member Keluarga Nakes</option>
                                    <option value="Member Only">Member Only</option>
                                </select>
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="status_member"></p>
                            </div>
                        </div>

                        <!-- Custom Diskon (%) -->
                        <div class="flex items-start gap-3 min-w-0 md:col-span-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                <x-heroicon-o-tag class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="block text-[10px] font-medium text-gray-400 mb-1">Custom Diskon (%)</label>
                                <input type="number" name="custom_discount_percentage" min="0" max="100" step="any" class="form-input w-full" placeholder="Kosongkan untuk diskon default 10%">
                                <p class="text-[10px] text-gray-400 mt-1">Kosongkan untuk diskon default 10%.</p>
                                <p class="edit-field-error text-red-600 text-[10px] mt-1 hidden" data-error-for="custom_discount_percentage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- FOOTER -->
        <div class="flex justify-end items-center gap-2 px-5 py-3 border-t border-gray-100 bg-gray-50">
            <button
                type="button"
                id="cancel-edit-pelanggan"
                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition"
            >
                Batal
            </button>
            <button
                type="button"
                id="save-edit-pelanggan"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-edit-pelanggan');
    const form = document.getElementById('form-edit-pelanggan');
    const errorBox = document.getElementById('edit-pelanggan-error');
    const saveBtn = document.getElementById('save-edit-pelanggan');
    const editButtons = document.querySelectorAll('.btn-edit-pelanggan');

    if (!modal || !form) return;

    function clearEditErrors() {
        errorBox.classList.add('hidden');
        errorBox.textContent = '';
        form.querySelectorAll('.edit-field-error').forEach(function (el) {
            el.textContent = '';
            el.classList.add('hidden');
        });
        form.querySelectorAll('.form-input, select, textarea').forEach(function (el) {
            el.classList.remove('border-red-500');
        });
    }

    function showEditErrors(errors) {
        Object.keys(errors || {}).forEach(function (field) {
            const el = form.querySelector('[data-error-for="' + field + '"]');
            const input = form.querySelector('[name="' + field + '"]');
            if (el) {
                el.textContent = (errors[field] || []).join(' ');
                el.classList.remove('hidden');
            }
            if (input) input.classList.add('border-red-500');
        });
    }

    function openEditModal(button) {
        clearEditErrors();
        form.reset();

        form.elements.nama.value = button.dataset.nama || '';
        form.elements.telepon.value = button.dataset.telepon || '';
        form.elements.alamat.value = button.dataset.alamat || '';
        form.elements.tanggal_lahir.value = button.dataset.tanggal_lahir || '';
        form.elements.status_member.value = button.dataset.status_member || '';
        const customDisc = button.dataset.custom_discount_percentage;
        form.elements.custom_discount_percentage.value = (customDisc !== undefined && customDisc !== null && customDisc !== '')
            ? parseFloat(customDisc)
            : '';
        form.dataset.id = button.dataset.id || '';

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            openEditModal(this);
        });
    });

    document.getElementById('close-edit-pelanggan')?.addEventListener('click', closeEditModal);
    document.getElementById('cancel-edit-pelanggan')?.addEventListener('click', closeEditModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeEditModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeEditModal();
        }
    });

    saveBtn.addEventListener('click', function () {
        clearEditErrors();

        const id = form.dataset.id;
        if (!id) return;

        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        const formData = new FormData(form);
        const url = '{{ url('pelanggan') }}/' + id;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (response) {
            return response.json().then(function (body) {
                return { status: response.status, body: body };
            });
        })
        .then(function (result) {
            if (result.status === 422) {
                showEditErrors(result.body.errors || {});
                if (result.body.message) {
                    errorBox.textContent = result.body.message;
                    errorBox.classList.remove('hidden');
                }
                return;
            }

            if (result.status >= 200 && result.status < 300) {
                closeEditModal();
                window.location.reload();
                return;
            }

            errorBox.textContent = result.body.message || 'Terjadi kesalahan saat menyimpan perubahan.';
            errorBox.classList.remove('hidden');
        })
        .catch(function () {
            errorBox.textContent = 'Gagal menghubungi server.';
            errorBox.classList.remove('hidden');
        })
        .finally(function () {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan Perubahan';
        });
    });
})();
</script>
