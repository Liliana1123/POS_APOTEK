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

<div id="{{ $id }}-backdrop" class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity"></div>
<div id="{{ $id }}" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[5vh] sm:pt-[10vh] px-4">
    <div class="w-full {{ $width }} bg-white rounded-xl shadow-2xl relative max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 shrink-0">
            <h3 id="{{ $id }}-title" class="text-sm font-bold text-gray-800">{{ $createTitle }}</h3>
            <button type="button" class="{{ $id }}-close text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        <div id="{{ $id }}-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-xs px-5 py-2.5 shrink-0"></div>
        <div class="px-5 py-4 overflow-y-auto flex-1">
            <form id="{{ $id }}-form" class="space-y-4" novalidate>
                @csrf
                <input type="hidden" name="_method" id="{{ $id }}-method" value="POST">
                {{ $slot }}
            </form>
        </div>
        <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 shrink-0">
            <button type="button" class="{{ $id }}-close btn-secondary py-1.5 px-4">Batal</button>
            <button type="button" class="{{ $id }}-submit btn-primary py-1.5 px-5">{{ $submitLabel }}</button>
        </div>
    </div>
</div>

<script>
(function () {
    var prefix = '{{ $id }}';
    var modalEl = document.getElementById(prefix);
    var backdrop = document.getElementById(prefix + '-backdrop');
    var titleEl = document.getElementById(prefix + '-title');
    var errorEl = document.getElementById(prefix + '-error');
    var form = document.getElementById(prefix + '-form');
    var methodEl = document.getElementById(prefix + '-method');
    var submitBtn = modalEl.querySelector('.' + prefix + '-submit');
    var createUrl = '{{ $createUrl }}';
    var updateBase = '{{ $updateBase }}';
    var createBtnSel = '{{ $createBtn }}';
    var editBtnSel = '{{ $editBtn }}';
    var submitLabel = '{{ $submitLabel }}';

    function clearErrors() {
        form.querySelectorAll('.modal-field-error').forEach(function (e) { e.textContent = ''; e.classList.add('hidden'); });
        form.querySelectorAll('.form-input, select, textarea').forEach(function (el) { el.classList.remove('border-red-500'); });
    }
    function showError(field, msgs) {
        var el = form.querySelector('[data-error-for="' + field + '"]');
        if (el) { el.textContent = msgs.join(' '); el.classList.remove('hidden'); }
        var inp = form.querySelector('[name="' + field + '"]');
        if (inp) inp.classList.add('border-red-500');
    }

    function openModal(mode) {
        errorEl.classList.add('hidden'); errorEl.textContent = '';
        clearErrors(); form.reset();
        if (mode === 'edit') { titleEl.textContent = '{{ $editTitle }}'; submitBtn.textContent = 'Simpan Perubahan'; }
        else { titleEl.textContent = '{{ $createTitle }}'; submitBtn.textContent = '{{ $submitLabel }}'; }
        form.querySelectorAll('[data-edit-only]').forEach(function (el) { el.classList.toggle('hidden', mode !== 'edit'); });
        form.querySelectorAll('[data-create-only]').forEach(function (el) { el.classList.toggle('hidden', mode !== 'create'); });
        backdrop.classList.remove('hidden');
        modalEl.classList.remove('hidden');
    }
    function hideModal() { backdrop.classList.add('hidden'); modalEl.classList.add('hidden'); }

    function prefillFromBtn(btn) {
        // data-json preferred for complex prefill (checkbox arrays, radio groups)
        if (btn.dataset.json) {
            try {
                var obj = JSON.parse(btn.dataset.json);
                form.querySelectorAll('[name]').forEach(function (inp) {
                    var name = inp.getAttribute('name');
                    if (!name || name === '_method' || name === '_token') return;
                    var clean = name.replace(/\[\]$/, '');
                    var val = obj[clean];
                    if (inp.type === 'checkbox') {
                        if (inp.name.endsWith('[]')) { inp.checked = (val || []).map(String).includes(String(inp.value)); }
                        else { inp.checked = String(val) === '1' || val === true; }
                    } else if (inp.type === 'radio') { inp.checked = String(inp.value) === String(val); }
                    else { inp.value = (val === undefined || val === null) ? '' : val; }
                });
            } catch (_) {}
            return;
        }
        // simple data-* attrs
        form.querySelectorAll('[name]').forEach(function (inp) {
            var name = inp.getAttribute('name');
            if (!name || name === '_method' || name === '_token') return;
            var v = btn.dataset[name];
            if (v !== undefined) {
                if (inp.type === 'checkbox') { inp.checked = v === '1'; }
                else if (inp.type === 'radio') { inp.checked = String(inp.value) === String(v); }
                else { inp.value = v; }
            }
        });
    }

    // create button
    var createBtn = document.querySelector(createBtnSel);
    if (createBtn) {
        createBtn.addEventListener('click', function () {
            methodEl.value = 'POST';
            form.dataset.url = createUrl;
            form.dataset.mode = 'create';
            openModal('create');
        });
    }

    // edit buttons
    document.querySelectorAll(editBtnSel).forEach(function (btn) {
        btn.addEventListener('click', function () {
            methodEl.value = 'PUT';
            form.dataset.url = updateBase + '/' + this.dataset.id;
            form.dataset.mode = 'edit';
            openModal('edit');
            prefillFromBtn(this);
        });
    });

    // close
    modalEl.querySelectorAll('.' + prefix + '-close').forEach(function (b) { b.addEventListener('click', hideModal); });
    backdrop.addEventListener('click', hideModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modalEl.classList.contains('hidden')) hideModal(); });

    // submit
    submitBtn.addEventListener('click', function () {
        clearErrors(); errorEl.classList.add('hidden');
        var fd = new FormData(form);
        var isEdit = form.dataset.mode === 'edit';
        var url = form.dataset.url;

        submitBtn.disabled = true; submitBtn.textContent = 'Menyimpan...';

        fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd })
            .then(function (res) { return res.json().then(function (b) { return { status: res.status, body: b }; }); })
            .then(function (r) {
                if (r.status === 422) { var errs = r.body.errors || {}; Object.keys(errs).forEach(function (k) { showError(k, errs[k]); }); if (r.body.message) { errorEl.textContent = r.body.message; errorEl.classList.remove('hidden'); } return; }
                if (r.status >= 200 && r.status < 300) { hideModal(); window.location.reload(); }
                else { errorEl.textContent = r.body.message || 'Terjadi kesalahan.'; errorEl.classList.remove('hidden'); }
            })
            .catch(function () { errorEl.textContent = 'Gagal menghubungi server.'; errorEl.classList.remove('hidden'); })
            .finally(function () { submitBtn.disabled = false; submitBtn.textContent = isEdit ? 'Simpan Perubahan' : '{{ $submitLabel }}'; });
    });
})();
</script>