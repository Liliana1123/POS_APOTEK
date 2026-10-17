// resources/js/pages/barang-index.js
// Page-specific JS extracted from barang/index.blade.php

document.addEventListener('DOMContentLoaded', function () {
    // --- DETAIL MODAL LOGIC ---
    var detailModal = document.getElementById('modal-detail-barang');
    var detailBackdrop = document.getElementById('modal-detail-barang-backdrop');
    var btnCloseDetail = document.getElementById('btn-close-detail-modal');
    var btnCancelDetail = document.getElementById('btn-cancel-detail-modal');
    var btnEditFromDetail = document.getElementById('btn-edit-from-detail');

    function openDetailModal(data) {
        document.getElementById('detail-title-nama').textContent = data.nama;
        document.getElementById('detail-kode-apotek').textContent = data.kode_apotek;
        document.getElementById('detail-kode-kfa').textContent = data.kode_kfa;
        document.getElementById('detail-nama').textContent = data.nama;
        document.getElementById('detail-merk').textContent = data.merk;
        document.getElementById('detail-barcode').textContent = data.barcode;
        document.getElementById('detail-kategori').textContent = data.kategori;
        document.getElementById('detail-satuan').textContent = data.satuan;
        document.getElementById('detail-pabrik').textContent = data.pabrik;
        document.getElementById('detail-supplier').textContent = data.supplier;
        document.getElementById('detail-stok').textContent = data.stok + ' ' + data.satuan;
        document.getElementById('detail-stok-minimum').textContent = data.stok_minimum + ' ' + data.satuan;

        var badge = document.getElementById('detail-status-stok-badge');
        if (badge) {
            badge.className = data.status_stok_badge;
            badge.textContent = data.status_stok;
        }

        document.getElementById('detail-butuh-resep').textContent = data.butuh_resep;
        document.getElementById('detail-status-aktif').textContent = data.aktif;

        window.__detailEditId = data.id;

        if (detailBackdrop) detailBackdrop.classList.remove('hidden');
        if (detailModal) detailModal.classList.remove('hidden');
    }

    function closeDetailModal() {
        if (detailBackdrop) detailBackdrop.classList.add('hidden');
        if (detailModal) detailModal.classList.add('hidden');
    }

    document.querySelectorAll('.btn-detail-barang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            try {
                var data = JSON.parse(this.dataset.json);
                openDetailModal(data);
            } catch (e) {
                console.error('Error parsing barang detail JSON', e);
            }
        });
    });

    if (btnCloseDetail) btnCloseDetail.addEventListener('click', closeDetailModal);
    if (btnCancelDetail) btnCancelDetail.addEventListener('click', closeDetailModal);
    if (detailBackdrop) detailBackdrop.addEventListener('click', closeDetailModal);
    if (btnEditFromDetail) btnEditFromDetail.addEventListener('click', function() {
        closeDetailModal();
        var editBtn = document.querySelector('.btn-edit-barang[data-id="' + window.__detailEditId + '"]');
        if (editBtn) editBtn.click();
    });

    // --- IMPORT MODAL LOGIC ---
    var importModal = document.getElementById('modal-import-barang');
    var importBackdrop = document.getElementById('modal-import-barang-backdrop');
    var btnOpen = document.getElementById('btn-import-barang');
    var btnClose = document.getElementById('btn-close-import-modal');
    var btnCancel = document.getElementById('btn-cancel-import-modal');
    var formImport = document.getElementById('form-import-barang');
    var submitBtn = document.getElementById('btn-submit-import');

    function openImportModal() {
        if (importBackdrop) importBackdrop.classList.remove('hidden');
        if (importModal) importModal.classList.remove('hidden');
    }

    function closeImportModal() {
        if (importBackdrop) importBackdrop.classList.add('hidden');
        if (importModal) importModal.classList.add('hidden');
    }

    if (btnOpen) btnOpen.addEventListener('click', openImportModal);
    if (btnClose) btnClose.addEventListener('click', closeImportModal);
    if (btnCancel) btnCancel.addEventListener('click', closeImportModal);
    if (importBackdrop) importBackdrop.addEventListener('click', closeImportModal);

    if (formImport) {
        formImport.addEventListener('submit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Memproses...</span>';
            }
        });
    }
});
