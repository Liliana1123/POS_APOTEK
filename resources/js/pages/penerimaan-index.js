// resources/js/pages/penerimaan-index.js
// Page-specific JS extracted from penerimaan/index.blade.php

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

            if (modal) modal.classList.remove('hidden');

            if (content) {
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
            }
        });
    });

    // Edit Penerimaan
    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.btn-edit-penerimaan');
        if (!button) return;

        const modalEdit = document.getElementById('modal-edit-penerimaan');
        const contentEdit = document.getElementById('edit-penerimaan-content');
        const url = button.dataset.url;

        if (!modalEdit || !contentEdit || !url) return;

        contentEdit.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                Memuat form edit...
            </div>
        `;

        modalEdit.classList.remove('hidden');
        modalEdit.setAttribute('aria-hidden', 'false');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                let errorMsg = 'Gagal memuat form edit.';
                try {
                    const errData = await response.json();
                    if (errData && errData.message) errorMsg = errData.message;
                } catch (e) {}
                throw new Error(errorMsg);
            }

            const html = await response.text();
            contentEdit.innerHTML = html;

            contentEdit.querySelectorAll('script').forEach(function (oldScript) {
                const newScript = document.createElement('script');
                newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
                oldScript.remove();
            });

            if (typeof initEditPenerimaanForm === 'function') {
                initEditPenerimaanForm();
            }
        } catch (error) {
            contentEdit.innerHTML = `
                <div class="p-6 text-center">
                    <div class="text-amber-600 font-bold mb-2">Pemberitahuan</div>
                    <div class="text-sm text-gray-700">${error.message || 'Penerimaan tidak dapat diedit karena sudah memiliki transaksi lanjutan.'}</div>
                </div>
            `;
            console.error(error);
        }
    });

    function tutupModalEdit() {
        const modalEdit = document.getElementById('modal-edit-penerimaan');
        const contentEdit = document.getElementById('edit-penerimaan-content');
        if (modalEdit) {
            modalEdit.classList.add('hidden');
            modalEdit.setAttribute('aria-hidden', 'true');
        }
        if (contentEdit) {
            contentEdit.innerHTML = '';
        }
    }

    // Tutup modal Edit
    document.addEventListener('click', function (event) {
        if (event.target.closest('#btn-tutup-edit') || event.target.closest('#btn-batal-edit')) {
            tutupModalEdit();
        }
    });

    const modalEditContainer = document.getElementById('modal-edit-penerimaan');
    if (modalEditContainer) {
        modalEditContainer.addEventListener('click', function (e) {
            if (e.target === modalEditContainer) {
                tutupModalEdit();
            }
        });
    }

    // =========================
    // MODAL PEMBAYARAN
    // =========================
    document.querySelectorAll('.btn-payment-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;
            if (paymentModal) paymentModal.classList.remove('hidden');

            if (paymentContent) {
                paymentContent.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        Memuat pembayaran...
                    </div>
                `;

                fetch(`/penerimaan/${id}/payment-form`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengambil form pembayaran.');
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
            }
        });
    });

    // =========================
    // MODAL SUSULAN
    // =========================
    const susulanModal = document.getElementById('modal-susulan-penerimaan');
    const susulanContent = document.getElementById('susulan-penerimaan-content');
    const closeSusulanButton = document.getElementById('close-susulan-penerimaan');

    document.querySelectorAll('.btn-susulan-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;
            if (susulanModal) susulanModal.classList.remove('hidden');

            if (susulanContent) {
                susulanContent.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        Memuat formulir penerimaan susulan...
                    </div>
                `;

                fetch(`/penerimaan/${id}/susulan-form`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memuat formulir penerimaan susulan.');
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
            }
        });
    });

    if (closeSusulanButton) {
        closeSusulanButton.addEventListener('click', function () {
            if (susulanModal) susulanModal.classList.add('hidden');
            if (susulanContent) susulanContent.innerHTML = '';
        });
    }

    // SIMPAN PENERIMAAN SUSULAN
    document.addEventListener('submit', function (event) {
        if (event.target.id !== 'form-susulan-penerimaan') return;

        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('#btn-simpan-susulan');
        if (!button || button.disabled) return;

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
                if (susulanModal) susulanModal.classList.add('hidden');
                if (susulanContent) susulanContent.innerHTML = '';
                window.location.reload();
            })
            .catch(error => {
                console.error('Penerimaan susulan:', error);
                alert(error.message || 'Gagal menyimpan penerimaan susulan.');
                button.disabled = false;
                button.textContent = 'Simpan Penerimaan Susulan';
            });
    });

    if (btnTutup && modal) {
        btnTutup.addEventListener('click', function () {
            modal.classList.add('hidden');
            if (content) content.innerHTML = '';
        });
    }

    if (btnTutupPayment && paymentModal) {
        btnTutupPayment.addEventListener('click', function () {
            paymentModal.classList.add('hidden');
            if (paymentContent) paymentContent.innerHTML = '';
        });
    }

    // ==========================================
    // MODAL TAMBAH FAKTUR PENERIMAAN BARU
    // ==========================================
    const modalTambah = document.getElementById('modal-tambah-penerimaan');
    const btnBukaTambah = document.getElementById('btn-tambah-penerimaan');
    const btnTutupTambah = document.getElementById('btn-tutup-tambah');
    const btnBatalTambah = document.getElementById('btn-batal-tambah');

    let rowIndexTambah = 0;
    const tbodyTambah = document.getElementById('item-rows-tambah');
    const templateTambah = document.getElementById('row-template-tambah');
    const emptyHintTambah = document.getElementById('empty-hint-tambah');
    const totalFakturTambah = document.getElementById('total-faktur-tambah');
    const ppnTambah = document.getElementById('ppn_tambah');
    const totalTagihanTambah = document.getElementById('total-tagihan-tambah');
    const oldItemsTambah = JSON.parse(document.getElementById('penerimaan-old-items')?.textContent || '[]');
    const serverErrorsTambah = JSON.parse(document.getElementById('penerimaan-server-errors')?.textContent || '[]');

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function updateTotalTambah() {
        if (!tbodyTambah) return;
        let total = 0;
        tbodyTambah.querySelectorAll('tr').forEach(row => {
            const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
            const jumlah = parseInt(row.querySelector('.jumlah-diterima-field')?.value, 10) || 0;
            const subtotal = harga * jumlah;
            total += subtotal;
            const subtotalEl = row.querySelector('.subtotal-field');
            if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
        });
        if (totalFakturTambah) totalFakturTambah.textContent = formatRupiah(total);
        const nilaiPpn = total * 0.11;
        if (ppnTambah) ppnTambah.value = formatRupiah(nilaiPpn);
        if (totalTagihanTambah) totalTagihanTambah.textContent = formatRupiah(total + nilaiPpn);
    }

    function tambahBarisPenerimaan() {
        if (!templateTambah || !tbodyTambah) return;
        const html = templateTambah.innerHTML.replaceAll('__i__', rowIndexTambah);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;
        tbodyTambah.appendChild(tempTr.firstElementChild);

        if (oldItemsTambah && oldItemsTambah[rowIndexTambah]) {
            const item = oldItemsTambah[rowIndexTambah];
            const row = tbodyTambah.lastElementChild;

            const bId = row.querySelector('[name$="[barang_id]"]');
            if (bId) {
                bId.value = item.barang_id || '';
                const opt = bId.selectedOptions[0];
                const bcField = row.querySelector('.barcode-field');
                const satField = row.querySelector('.satuan-field');
                if (bcField) bcField.value = opt?.dataset.barcode || '';
                if (satField) satField.value = opt?.dataset.satuan || '';
            }
            const nb = row.querySelector('[name$="[no_batch]"]');
            if (nb) nb.value = item.no_batch || '';
            const ed = row.querySelector('[name$="[expired_date]"]');
            if (ed) ed.value = item.expired_date || '';
            const hb = row.querySelector('[name$="[harga_beli]"]');
            if (hb) hb.value = item.harga_beli || '';
            const hj = row.querySelector('[name$="[harga_jual]"]');
            if (hj) hj.value = item.harga_jual || '';
            const nr = row.querySelector('[name$="[no_rak]"]');
            if (nr) nr.value = item.no_rak || '';
            const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
            if (jd) jd.value = item.jumlah_dipesan || '';
            const jt = row.querySelector('[name$="[jumlah_diterima]"]');
            if (jt) jt.value = item.jumlah_diterima || '';
        }
        rowIndexTambah++;
        if (emptyHintTambah) emptyHintTambah.style.display = 'none';
        updateTotalTambah();
    }

    function resetFormTambah() {
        const formTambah = document.getElementById('form-tambah-penerimaan');
        if (!formTambah) return;
        formTambah.reset();

        const today = new Date().toISOString().split('T')[0];
        const tglFakturInput = document.getElementById('tanggal_faktur_tambah');
        const tglTerimaInput = document.getElementById('tanggal_tambah');
        if (tglFakturInput) tglFakturInput.value = today;
        if (tglTerimaInput) tglTerimaInput.value = today;

        const telEl = document.getElementById('telepon_supplier_tambah');
        if (telEl) telEl.value = '';

        if (tbodyTambah) {
            tbodyTambah.innerHTML = '';
            rowIndexTambah = 0;
            tambahBarisPenerimaan();
        }
        updateTotalTambah();
    }

    if (modalTambah) {
        if (btnBukaTambah) {
            btnBukaTambah.addEventListener('click', function () {
                tutupModalEdit();
                resetFormTambah();
                modalTambah.classList.remove('hidden');
                modalTambah.setAttribute('aria-hidden', 'false');
            });
        }

        function tutupModalTambah() {
            modalTambah.classList.add('hidden');
            modalTambah.setAttribute('aria-hidden', 'true');
        }

        if (btnTutupTambah) btnTutupTambah.addEventListener('click', tutupModalTambah);
        if (btnBatalTambah) btnBatalTambah.addEventListener('click', tutupModalTambah);

        modalTambah.addEventListener('click', function (e) {
            if (e.target === modalTambah) {
                tutupModalTambah();
            }
        });

        const btnTambahItem = document.getElementById('btn-tambah-item-tambah');
        if (btnTambahItem) {
            btnTambahItem.addEventListener('click', tambahBarisPenerimaan);
        }

        if (tbodyTambah) {
            tbodyTambah.addEventListener('click', function (e) {
                if (e.target.closest('.btn-hapus-row-tambah')) {
                    e.target.closest('tr').remove();
                    if (tbodyTambah.children.length === 0 && emptyHintTambah) {
                        emptyHintTambah.style.display = 'block';
                    }
                    updateTotalTambah();
                }
            });

            tbodyTambah.addEventListener('change', function (e) {
                if (e.target.classList.contains('barang-select')) {
                    const option = e.target.selectedOptions[0];
                    const row = e.target.closest('tr');
                    const bcField = row.querySelector('.barcode-field');
                    const satField = row.querySelector('.satuan-field');
                    if (bcField) bcField.value = option?.dataset.barcode || '';
                    if (satField) satField.value = option?.dataset.satuan || '';
                }
            });

            tbodyTambah.addEventListener('input', function (e) {
                updateTotalTambah();
                const target = e.target;
                const row = target.closest('tr');
                if (!row) return;

                if (target.name && target.name.includes('[expired_date]')) {
                    const tgl = document.getElementById('tanggal_tambah')?.value;
                    validateRowExpiredTambah(row, tgl);
                } else if (target.name && (target.name.includes('[harga_beli]') || target.name.includes('[harga_jual]'))) {
                    validateRowHargaTambah(row);
                }
            });
        }

        const supplierSelectTambah = document.getElementById('supplier_id_tambah');
        if (supplierSelectTambah) {
            supplierSelectTambah.addEventListener('change', function () {
                const telEl = document.getElementById('telepon_supplier_tambah');
                if (telEl) telEl.value = this.selectedOptions[0]?.dataset.telepon || '';
            });
            if (supplierSelectTambah.value) {
                const telEl = document.getElementById('telepon_supplier_tambah');
                if (telEl) telEl.value = supplierSelectTambah.selectedOptions[0]?.dataset.telepon || '';
            }
        }

        const formTambah = document.getElementById('form-tambah-penerimaan');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                if (tbodyTambah && tbodyTambah.children.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal 1 baris barang.');
                    return;
                }
            });
        }

        if (oldItemsTambah && oldItemsTambah.length > 0) {
            oldItemsTambah.forEach(() => {
                tambahBarisPenerimaan();
            });
        } else {
            tambahBarisPenerimaan();
        }

        // Buka otomatis + terapkan error validasi bila submit sebelumnya gagal
        if (Object.keys(serverErrorsTambah).length > 0) {
            modalTambah.classList.remove('hidden');
            modalTambah.setAttribute('aria-hidden', 'false');
            setTimeout(() => {
                applyServerErrorsTambah(serverErrorsTambah);
            }, 50);
        }
    }
});