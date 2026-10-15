@props([
    'id' => 'modal-form',
    'createTitle' => 'Tambah',
    'editTitle' => 'Edit',
    'createUrl' => '#',
    'updateBase' => '#',
    'createBtn' => '',
    'editBtn' => '',
    'submitLabel' => 'Simpan',
    'width' => 'max-w-lg',
])

<div id="{{ $id }}-backdrop"
     class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity">
</div>

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[5vh] sm:pt-[10vh] px-4">

    <div class="w-full {{ $width }} bg-white rounded-xl shadow-2xl relative max-h-[85vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 shrink-0">

            <h3 id="{{ $id }}-title"
                class="text-sm font-bold text-gray-800">
                {{ $createTitle }}
            </h3>

            <button
                type="button"
                class="{{ $id }}-close text-gray-400 hover:text-gray-600 transition-colors p-1"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>

        </div>

        {{-- ERROR UMUM --}}
        <div
            id="{{ $id }}-error"
            class="hidden bg-red-50 border border-red-200 text-red-700 text-xs px-5 py-2.5 shrink-0"
        ></div>

        {{-- FORM CONTENT --}}
        <div class="px-5 py-4 overflow-y-auto flex-1">

            <form
                id="{{ $id }}-form"
                class="space-y-4"
                method="POST"
                novalidate
            >

                @csrf

                <input
                    type="hidden"
                    name="_method"
                    id="{{ $id }}-method"
                    value="POST"
                >

                {{ $slot }}

            </form>

        </div>

        {{-- FOOTER --}}
        <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 shrink-0">

            <button
                type="button"
                class="{{ $id }}-close btn-secondary py-1.5 px-4"
            >
                Batal
            </button>

            <button
                type="button"
                class="{{ $id }}-submit btn-primary py-1.5 px-5"
            >
                {{ $submitLabel }}
            </button>

        </div>

    </div>
</div>


<script>
(function () {

    'use strict';

    var prefix = @json($id);

    var modalEl = document.getElementById(prefix);
    var backdrop = document.getElementById(prefix + '-backdrop');
    var titleEl = document.getElementById(prefix + '-title');
    var errorEl = document.getElementById(prefix + '-error');
    var form = document.getElementById(prefix + '-form');
    var methodEl = document.getElementById(prefix + '-method');

    if (!modalEl || !backdrop || !form || !methodEl) {
        return;
    }

    var submitBtn = modalEl.querySelector('.' + prefix + '-submit');

    if (!submitBtn) {
        return;
    }

    var createUrl = @json($createUrl);
    var updateBase = @json($updateBase);
    var createBtnSel = @json($createBtn);
    var editBtnSel = @json($editBtn);
    var submitLabel = @json($submitLabel);
    var createTitle = @json($createTitle);
    var editTitle = @json($editTitle);

    /*
    |--------------------------------------------------------------------------
    | CLEAR ERROR
    |--------------------------------------------------------------------------
    */

    function clearErrors() {

        form.querySelectorAll('.modal-field-error').forEach(function (el) {
            el.textContent = '';
            el.classList.add('hidden');
        });

        form.querySelectorAll('.form-input, select, textarea').forEach(function (el) {
            el.classList.remove('border-red-500');
        });

        errorEl.classList.add('hidden');
        errorEl.textContent = '';
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW FIELD ERROR
    |--------------------------------------------------------------------------
    */

    function showError(field, messages) {

        var errorElField = form.querySelector(
            '[data-error-for="' + field + '"]'
        );

        if (errorElField) {
            errorElField.textContent = Array.isArray(messages)
                ? messages.join(' ')
                : messages;

            errorElField.classList.remove('hidden');
        }

        var input = form.querySelector(
            '[name="' + field + '"]'
        );

        if (input) {
            input.classList.add('border-red-500');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN MODAL
    |--------------------------------------------------------------------------
    */

    function openModal(mode) {

        clearErrors();

        form.reset();

        if (mode === 'edit') {

            titleEl.textContent = editTitle;

            submitBtn.textContent = 'Simpan Perubahan';

            methodEl.value = 'PUT';

        } else {

            titleEl.textContent = createTitle;

            submitBtn.textContent = submitLabel;

            methodEl.value = 'POST';
        }

        form.querySelectorAll('[data-edit-only]').forEach(function (el) {

            if (mode === 'edit') {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }

        });

        form.querySelectorAll('[data-create-only]').forEach(function (el) {

            if (mode === 'create') {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }

        });

        backdrop.classList.remove('hidden');
        modalEl.classList.remove('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function hideModal() {

        backdrop.classList.add('hidden');
        modalEl.classList.add('hidden');

        clearErrors();

        form.reset();

        methodEl.value = 'POST';

    }


    /*
    |--------------------------------------------------------------------------
    | PREFILL EDIT DATA
    |--------------------------------------------------------------------------
    */

    function prefillFromBtn(btn) {

        /*
        |--------------------------------------------------------------------------
        | DATA JSON
        |--------------------------------------------------------------------------
        */

        if (btn.dataset.json) {

            try {

                var obj = JSON.parse(btn.dataset.json);

                form.querySelectorAll('[name]').forEach(function (input) {

                    var name = input.getAttribute('name');

                    if (
                        !name ||
                        name === '_method' ||
                        name === '_token'
                    ) {
                        return;
                    }

                    var cleanName = name.replace(/\[\]$/, '');

                    var value = obj[cleanName];

                    if (input.type === 'checkbox') {

                        if (input.name.endsWith('[]')) {

                            input.checked =
                                Array.isArray(value) &&
                                value.map(String).includes(
                                    String(input.value)
                                );

                        } else {

                            input.checked =
                                String(value) === '1' ||
                                value === true;

                        }

                    } else if (input.type === 'radio') {

                        input.checked =
                            String(input.value) === String(value);

                    } else {

                        input.value =
                            value === undefined || value === null
                                ? ''
                                : value;
                    }

                });

            } catch (error) {

                console.error(
                    'Gagal membaca data edit:',
                    error
                );

            }

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DATA ATTRIBUTE BIASA
        |--------------------------------------------------------------------------
        */

        form.querySelectorAll('[name]').forEach(function (input) {

            var name = input.getAttribute('name');

            if (
                !name ||
                name === '_method' ||
                name === '_token'
            ) {
                return;
            }

            var value = btn.dataset[name];

            if (value === undefined) {
                return;
            }

            if (input.type === 'checkbox') {

                input.checked = value === '1';

            } else if (input.type === 'radio') {

                input.checked =
                    String(input.value) === String(value);

            } else {

                input.value = value;

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE BUTTON
    |--------------------------------------------------------------------------
    */

    if (createBtnSel) {

        var createBtn = document.querySelector(createBtnSel);

        if (createBtn) {

            createBtn.addEventListener('click', function (event) {

                event.preventDefault();

                methodEl.value = 'POST';

                form.dataset.url = createUrl;

                form.dataset.mode = 'create';

                openModal('create');

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EDIT BUTTON
    |--------------------------------------------------------------------------
    */

    if (editBtnSel) {

        document.querySelectorAll(editBtnSel).forEach(function (btn) {

            btn.addEventListener('click', function (event) {

                event.preventDefault();

                var id = this.dataset.id;

                if (!id) {

                    console.error(
                        'Tombol edit tidak memiliki data-id.'
                    );

                    return;
                }

                methodEl.value = 'PUT';

                form.dataset.url =
                    updateBase.replace(/\/$/, '') +
                    '/' +
                    id;

                form.dataset.mode = 'edit';

                openModal('edit');

                prefillFromBtn(this);

            });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE BUTTON
    |--------------------------------------------------------------------------
    */

    modalEl
        .querySelectorAll('.' + prefix + '-close')
        .forEach(function (button) {

            button.addEventListener('click', function (event) {

                event.preventDefault();

                hideModal();

            });

        });


    /*
    |--------------------------------------------------------------------------
    | BACKDROP CLOSE
    |--------------------------------------------------------------------------
    */

    backdrop.addEventListener('click', function () {

        hideModal();

    });


    /*
    |--------------------------------------------------------------------------
    | ESCAPE CLOSE
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {

        if (
            event.key === 'Escape' &&
            !modalEl.classList.contains('hidden')
        ) {

            hideModal();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | SUBMIT / SIMPAN
    |--------------------------------------------------------------------------
    */

    submitBtn.addEventListener('click', function (event) {

        event.preventDefault();

        clearErrors();

        var url = form.dataset.url;

        var mode = form.dataset.mode || 'create';

        /*
        |--------------------------------------------------------------------------
        | CEK URL
        |--------------------------------------------------------------------------
        */

        if (!url || url === '#') {

            errorEl.textContent =
                'Alamat tujuan penyimpanan belum tersedia.';

            errorEl.classList.remove('hidden');

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN STATE TOMBOL
        |--------------------------------------------------------------------------
        */

        var originalText =
            mode === 'edit'
                ? 'Simpan Perubahan'
                : submitLabel;

        submitBtn.disabled = true;

        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

        submitBtn.textContent = 'Menyimpan...';


        /*
        |--------------------------------------------------------------------------
        | FORM DATA
        |--------------------------------------------------------------------------
        */

        var formData = new FormData(form);


        /*
        |--------------------------------------------------------------------------
        | FETCH
        |--------------------------------------------------------------------------
        */

        fetch(url, {

            method: 'POST',

            headers: {

                'X-Requested-With': 'XMLHttpRequest',

                'Accept': 'application/json'

            },

            credentials: 'same-origin',

            body: formData

        })

        .then(function (response) {

            return response.text().then(function (text) {

                var body = {};

                try {

                    body = text
                        ? JSON.parse(text)
                        : {};

                } catch (error) {

                    body = {
                        message:
                            'Server tidak mengembalikan respons JSON yang valid.'
                    };

                }

                return {

                    status: response.status,

                    ok: response.ok,

                    body: body

                };

            });

        })

        .then(function (result) {

            /*
            |--------------------------------------------------------------------------
            | VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            if (result.status === 422) {

                var errors =
                    result.body.errors || {};

                Object.keys(errors).forEach(function (field) {

                    showError(
                        field,
                        errors[field]
                    );

                });

                if (result.body.message) {

                    errorEl.textContent =
                        result.body.message;

                    errorEl.classList.remove('hidden');

                }

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            if (
                result.status >= 200 &&
                result.status < 300
            ) {

                hideModal();

                window.location.reload();

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SERVER ERROR
            |--------------------------------------------------------------------------
            */

            errorEl.textContent =
                result.body.message ||
                'Terjadi kesalahan saat menyimpan data.';

            errorEl.classList.remove('hidden');

        })

        .catch(function (error) {

            console.error(
                'Error submit modal:',
                error
            );

            errorEl.textContent =
                'Gagal menghubungi server. Silakan coba lagi.';

            errorEl.classList.remove('hidden');

        })

        .finally(function () {

            submitBtn.disabled = false;

            submitBtn.classList.remove(
                'opacity-70',
                'cursor-not-allowed'
            );

            submitBtn.textContent = originalText;

        });

    });


})();
</script>