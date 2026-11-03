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
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-detail-penerimaan');
        if (!button) return;
        const url = button.dataset.url;
        if (!url) return;

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
                } catch (e) { }
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
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-payment-penerimaan');
        if (!button) return;
        const id = button.dataset.id;
        if (!id) return;

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

    // =========================
    // MODAL SUSULAN
    // =========================
    // MODAL SUSULAN
    // =========================
    const susulanModal = document.getElementById('modal-susulan-penerimaan');
    const susulanContent = document.getElementById('susulan-penerimaan-content');
    const closeSusulanButton = document.getElementById('close-susulan-penerimaan');
    let lastActiveElementSusulan = null;

    function tutupModalSusulan() {
        if (susulanModal) {
            if (document.activeElement && susulanModal.contains(document.activeElement)) {
                document.activeElement.blur();
            }
            susulanModal.classList.add('hidden');
            susulanModal.setAttribute('aria-hidden', 'true');
        }
        if (susulanContent) susulanContent.innerHTML = '';
        if (lastActiveElementSusulan && typeof lastActiveElementSusulan.focus === 'function') {
            lastActiveElementSusulan.focus();
            lastActiveElementSusulan = null;
        }
    }

    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-susulan-penerimaan');
        if (!button) return;
        const id = button.dataset.id;
        const url = button.dataset.url || (id ? `/penerimaan/${id}/susulan-form` : null);
        if (!url) return;

        lastActiveElementSusulan = button;

        if (susulanModal) {
            susulanModal.classList.remove('hidden');
            susulanModal.setAttribute('aria-hidden', 'false');
        }

        if (susulanContent) {
            susulanContent.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat formulir penerimaan susulan...
                </div>
            `;

            fetch(url)
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

    document.addEventListener('click', function (e) {
        if (e.target.closest('#close-susulan-penerimaan') || e.target.closest('#btn-batal-susulan')) {
            tutupModalSusulan();
        }
    });

    if (susulanModal) {
        susulanModal.addEventListener('click', function (e) {
            if (e.target === susulanModal) {
                tutupModalSusulan();
            }
        });
    }

    // SIMPAN PENERIMAAN SUSULAN
    document.addEventListener('submit', function (event) {
        if (event.target.id !== 'form-susulan-penerimaan') return;

        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('#btn-simpan-susulan');
        const errorBanner = form.querySelector('#susulan-error-banner');
        const errorText = form.querySelector('#susulan-error-text');

        function showError(msg) {
            if (errorBanner && errorText) {
                errorText.textContent = msg;
                errorBanner.classList.remove('hidden');
                errorBanner.classList.add('flex');
                errorBanner.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert(msg);
            }
        }

        if (errorBanner) {
            errorBanner.classList.add('hidden');
            errorBanner.classList.remove('flex');
        }

        if (form.dataset.submitting === 'true' || !button || button.disabled) return;

        // Validasi Frontend cepat
        let totalSusulan = 0;
        let totalBatal = 0;
        let missingDetail = false;
        let expiredInvalid = false;
        let hargaInvalid = false;
        const tglTerima = form.querySelector('[name="tanggal_terima"]')?.value || '';

        form.querySelectorAll('tbody tr').forEach(row => {
            const susulanInput = row.querySelector('[name^="jumlah_susulan"]');
            const batalInput = row.querySelector('[name^="jumlah_pembatalan"]');
            const batchInput = row.querySelector('[name*="[no_batch]"]');
            const expiredInput = row.querySelector('[name*="[expired_date]"]');
            const hargaBeliInput = row.querySelector('[name*="[harga_beli]"]');
            const hargaJualInput = row.querySelector('[name*="[harga_jual]"]');

            const sVal = parseInt(susulanInput?.value, 10) || 0;
            const bVal = parseInt(batalInput?.value, 10) || 0;
            totalSusulan += sVal;
            totalBatal += bVal;

            if (sVal > 0) {
                const missingBatch = !batchInput?.value?.trim();
                const missingExpired = !expiredInput?.value;
                const missingHargaBeli = !hargaBeliInput?.value;
                const missingHargaJual = !hargaJualInput?.value;

                if (missingBatch || missingExpired || missingHargaBeli || missingHargaJual) {
                    missingDetail = true;
                    if (missingBatch) batchInput?.classList.add('border-red-500');
                    if (missingExpired) expiredInput?.classList.add('border-red-500');
                    if (missingHargaBeli) hargaBeliInput?.classList.add('border-red-500');
                    if (missingHargaJual) hargaJualInput?.classList.add('border-red-500');
                } else {
                    batchInput?.classList.remove('border-red-500');
                    expiredInput?.classList.remove('border-red-500');
                    hargaBeliInput?.classList.remove('border-red-500');
                    hargaJualInput?.classList.remove('border-red-500');
                }

                if (expiredInput?.value && tglTerima && expiredInput.value < tglTerima) {
                    expiredInvalid = true;
                }

                const hb = parseFloat(hargaBeliInput?.value) || 0;
                const hj = parseFloat(hargaJualInput?.value) || 0;
                if (hj < hb) {
                    hargaInvalid = true;
                }
            }
        });

        if (totalSusulan + totalBatal <= 0) {
            showError('Minimal satu barang harus memiliki jumlah susulan atau pembatalan lebih dari 0.');
            return;
        }

        if (missingDetail) {
            showError('Nomor batch, tanggal expired, harga beli, dan harga jual wajib diisi untuk barang yang menerima susulan.');
            return;
        }

        if (expiredInvalid) {
            showError('Tanggal expired tidak boleh sebelum tanggal penerimaan susulan.');
            return;
        }

        if (hargaInvalid) {
            showError('Harga jual tidak boleh lebih kecil dari harga beli.');
            return;
        }

        form.dataset.submitting = 'true';
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Menyimpan...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('[name="_token"]')?.value;
        const requestHeaders = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };
        if (csrfToken) {
            requestHeaders['X-CSRF-TOKEN'] = csrfToken;
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: requestHeaders
        })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error('Terjadi kesalahan pada server (status ' + response.status + '). Silakan coba lagi.');
                }

                if (!response.ok) {
                    let errorMsg = 'Gagal menyimpan penerimaan susulan.';
                    if (data.errors) {
                        const firstErr = Object.values(data.errors).flat()[0];
                        if (firstErr) errorMsg = firstErr;
                    } else if (data.message) {
                        errorMsg = data.message;
                    }
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(data => {
                tutupModalSusulan();
                window.location.reload();
            })
            .catch(error => {
                console.error('Penerimaan susulan:', error);
                showError(error.message || 'Gagal menyimpan penerimaan susulan.');
                delete form.dataset.submitting;
                button.disabled = false;
                button.textContent = originalText;
            });
    });

    if (btnTutup && modal) {
        btnTutup.addEventListener('click', function () {
            modal.classList.add('hidden');
            if (content) content.innerHTML = '';
        });
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                if (content) content.innerHTML = '';
            }
        });
    }

    if (btnTutupPayment && paymentModal) {
        btnTutupPayment.addEventListener('click', function () {
            paymentModal.classList.add('hidden');
            if (paymentContent) paymentContent.innerHTML = '';
        });
    }

    if (paymentModal) {
        paymentModal.addEventListener('click', function (e) {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
                if (paymentContent) paymentContent.innerHTML = '';
            }
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
    let currentTotalTagihanTambah = 0;
    const tbodyTambah = document.getElementById('item-rows-tambah');
    const templateTambah = document.getElementById('row-template-tambah');
    const emptyHintTambah = document.getElementById('empty-hint-tambah');
    const totalFakturTambah = document.getElementById('total-faktur-tambah');
    const ppnTambah = document.getElementById('ppn_tambah');
    const totalTagihanTambah = document.getElementById('total-tagihan-tambah');
    const pembayaranPertamaTambah = document.getElementById('pembayaran_pertama_tambah');
    const btnSinkronPembayaranTambah = document.getElementById('btn-sinkron-pembayaran-tambah');
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
        currentTotalTagihanTambah = total + nilaiPpn;
        if (totalTagihanTambah) totalTagihanTambah.textContent = formatRupiah(currentTotalTagihanTambah);
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

    window.insertImportedItemTambah = function (item) {
        if (!templateTambah || !tbodyTambah) return;
        const html = templateTambah.innerHTML.replaceAll('__i__', rowIndexTambah);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;
        const row = tempTr.firstElementChild;
        tbodyTambah.appendChild(row);

        const bId = row.querySelector('[name$="[barang_id]"]');
        if (bId) {
            bId.value = item.barang_id;
            const opt = bId.selectedOptions[0];
            const bcField = row.querySelector('.barcode-field');
            const satField = row.querySelector('.satuan-field');
            if (bcField) bcField.value = opt?.dataset.barcode || item.barcode || '';
            if (satField) satField.value = opt?.dataset.satuan || item.satuan_nama || '';
        }

        const nb = row.querySelector('[name$="[no_batch]"]');
        if (nb) nb.value = item.no_batch || '';

        const ed = row.querySelector('[name$="[expired_date]"]');
        if (ed) ed.value = item.expired_date || '';

        const hb = row.querySelector('[name$="[harga_beli]"]');
        if (hb) hb.value = (item.harga_beli !== null && item.harga_beli !== undefined) ? item.harga_beli : '';

        const hj = row.querySelector('[name$="[harga_jual]"]');
        if (hj) hj.value = (item.harga_jual !== null && item.harga_jual !== undefined) ? item.harga_jual : '';

        const nr = row.querySelector('[name$="[no_rak]"]');
        if (nr) nr.value = item.no_rak || 'A-01';

        const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
        if (jd) jd.value = item.jumlah_dipesan || 1;

        const jt = row.querySelector('[name$="[jumlah_diterima]"]');
        if (jt) jt.value = item.jumlah_diterima || 1;

        rowIndexTambah++;
    };

    window.insertImportedItemsBatchTambah = function (items) {
        if (!templateTambah || !tbodyTambah || !items || items.length === 0) return;
        const fragment = document.createDocumentFragment();
        const baseHtml = templateTambah.innerHTML;

        items.forEach(item => {
            const html = baseHtml.replaceAll('__i__', rowIndexTambah);
            const tempTr = document.createElement('tbody');
            tempTr.innerHTML = html;
            const row = tempTr.firstElementChild;

            const bId = row.querySelector('[name$="[barang_id]"]');
            if (bId) {
                bId.value = item.barang_id;
                const opt = bId.selectedOptions[0];
                const bcField = row.querySelector('.barcode-field');
                const satField = row.querySelector('.satuan-field');
                if (bcField) bcField.value = opt?.dataset.barcode || item.barcode || '';
                if (satField) satField.value = opt?.dataset.satuan || item.satuan_nama || '';
            }

            const nb = row.querySelector('[name$="[no_batch]"]');
            if (nb) nb.value = item.no_batch || '';

            const ed = row.querySelector('[name$="[expired_date]"]');
            if (ed) ed.value = item.expired_date || '';

            const hb = row.querySelector('[name$="[harga_beli]"]');
            if (hb) hb.value = (item.harga_beli !== null && item.harga_beli !== undefined) ? item.harga_beli : '';

            const hj = row.querySelector('[name$="[harga_jual]"]');
            if (hj) hj.value = (item.harga_jual !== null && item.harga_jual !== undefined) ? item.harga_jual : '';

            const nr = row.querySelector('[name$="[no_rak]"]');
            if (nr) nr.value = item.no_rak || 'A-01';

            const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
            if (jd) jd.value = item.jumlah_dipesan || 1;

            const jt = row.querySelector('[name$="[jumlah_diterima]"]');
            if (jt) jt.value = item.jumlah_diterima || 1;

            fragment.appendChild(row);
            rowIndexTambah++;
        });

        tbodyTambah.appendChild(fragment);
        if (emptyHintTambah) emptyHintTambah.style.display = 'none';
        updateTotalTambah();
    };

    window.updateTotalTambah = updateTotalTambah;

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

        if (btnTutupTambah) {
            btnTutupTambah.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                tutupModalTambah();
            });
        }
        if (btnBatalTambah) {
            btnBatalTambah.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                tutupModalTambah();
            });
        }

        modalTambah.addEventListener('click', function (e) {
            if (e.target === modalTambah) {
                tutupModalTambah();
            }
        });

        const btnTambahItem = document.getElementById('btn-tambah-item-tambah');
        if (btnTambahItem) {
            btnTambahItem.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                tambahBarisPenerimaan();
            });
        }

        if (btnSinkronPembayaranTambah) {
            btnSinkronPembayaranTambah.addEventListener('click', function () {
                updateTotalTambah();
                const nominal = Number.isInteger(currentTotalTagihanTambah)
                    ? currentTotalTagihanTambah
                    : Math.round(currentTotalTagihanTambah * 100) / 100;

                if (pembayaranPertamaTambah) {
                    pembayaranPertamaTambah.value = nominal;
                    pembayaranPertamaTambah.dispatchEvent(new Event('input', { bubbles: true }));
                    pembayaranPertamaTambah.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
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

        const tglTambahInput = document.getElementById('tanggal_tambah');
        if (tglTambahInput && tbodyTambah) {
            tglTambahInput.addEventListener('change', function () {
                const tgl = this.value;
                tbodyTambah.querySelectorAll('tr').forEach(row => {
                    validateRowExpiredTambah(row, tgl);
                });
            });
        }

        function setFieldErrorTambah(input, message) {
            if (!input) return;
            input.classList.remove('border-gray-300');
            input.classList.add('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');

            const container = input.closest('td') || input.parentElement;
            if (!container) return;

            let errEl = container.querySelector('.field-error-msg');
            if (!errEl) {
                errEl = document.createElement('span');
                errEl.className = 'field-error-msg text-[11px] text-red-600 font-semibold block mt-1 leading-tight whitespace-normal';
                container.appendChild(errEl);
            }
            errEl.textContent = message;
        }

        function clearFieldErrorTambah(input) {
            if (!input) return;
            input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
            input.classList.add('border-gray-300');

            const container = input.closest('td') || input.parentElement;
            if (!container) return;

            const errEl = container.querySelector('.field-error-msg');
            if (errEl) {
                errEl.remove();
            }
        }

        function clearAllErrorsTambah() {
            if (!formTambah) return;
            formTambah.querySelectorAll('.field-error-msg').forEach(el => el.remove());
            formTambah.querySelectorAll('.\\!border-red-500').forEach(input => {
                input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
                input.classList.add('border-gray-300');
            });
        }

        function validateRowExpiredTambah(row, tanggalPenerimaan) {
            if (!row) return true;
            const input = row.querySelector('[name$="[expired_date]"]');
            if (!input) return true;

            if (input.value && tanggalPenerimaan) {
                if (input.value < tanggalPenerimaan) {
                    setFieldErrorTambah(input, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                    return false;
                }
            }
            clearFieldErrorTambah(input);
            return true;
        }

        function validateRowHargaTambah(row) {
            if (!row) return true;
            const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
            const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
            if (!hbInput || !hjInput) return true;

            const hb = parseFloat(hbInput.value);
            const hj = parseFloat(hjInput.value);

            if (!isNaN(hb) && !isNaN(hj)) {
                if (hj < hb) {
                    setFieldErrorTambah(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                    return false;
                }
            }
            clearFieldErrorTambah(hjInput);
            return true;
        }

        function validateFormTambah() {
            clearAllErrorsTambah();
            let isValid = true;
            let firstErrorField = null;

            const tglPenerimaan = document.getElementById('tanggal_tambah')?.value || '';

            if (tbodyTambah) {
                tbodyTambah.querySelectorAll('tr').forEach(row => {
                    const expInput = row.querySelector('[name$="[expired_date]"]');
                    if (expInput) {
                        if (!expInput.value) {
                            setFieldErrorTambah(expInput, 'Tanggal expired wajib diisi.');
                            isValid = false;
                            if (!firstErrorField) firstErrorField = expInput;
                        } else if (tglPenerimaan && expInput.value < tglPenerimaan) {
                            setFieldErrorTambah(expInput, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                            isValid = false;
                            if (!firstErrorField) firstErrorField = expInput;
                        }
                    }

                    const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
                    const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
                    if (hbInput && hjInput) {
                        const hb = parseFloat(hbInput.value);
                        const hj = parseFloat(hjInput.value);

                        if (isNaN(hj) || hj < 0) {
                            setFieldErrorTambah(hjInput, 'Harga jual wajib diisi.');
                            isValid = false;
                            if (!firstErrorField) firstErrorField = hjInput;
                        } else if (!isNaN(hb) && hj < hb) {
                            setFieldErrorTambah(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                            isValid = false;
                            if (!firstErrorField) firstErrorField = hjInput;
                        }
                    }
                });
            }

            if (!isValid && firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            return isValid;
        }

        function applyServerErrorsTambah(errors) {
            clearAllErrorsTambah();
            if (!formTambah) return;
            let firstErrorField = null;

            for (const key in errors) {
                const msgs = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
                const msg = msgs[0];

                const match = key.match(/^items\.(\d+)\.([a-z_]+)$/);
                if (match && tbodyTambah) {
                    const rowIndex = parseInt(match[1], 10);
                    const fieldName = match[2];
                    const rows = tbodyTambah.querySelectorAll('tr');
                    const row = rows[rowIndex];
                    if (row) {
                        const input = row.querySelector(`[name$="[${fieldName}]"]`);
                        if (input) {
                            setFieldErrorTambah(input, msg);
                            if (!firstErrorField) firstErrorField = input;
                            continue;
                        }
                    }
                }

                const topInput = formTambah.querySelector(`[name="${key}"]`);
                if (topInput) {
                    setFieldErrorTambah(topInput, msg);
                    if (!firstErrorField) firstErrorField = topInput;
                    continue;
                }
            }

            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        const formTambah = document.getElementById('form-tambah-penerimaan');
        const btnSimpanTambah = document.getElementById('btn-simpan-tambah');

        if (formTambah) {
            formTambah.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                    e.preventDefault();
                }
            });

            formTambah.addEventListener('submit', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (tbodyTambah && tbodyTambah.children.length === 0) {
                    alert('Tambahkan minimal 1 baris barang.');
                    return;
                }

                if (!validateFormTambah()) {
                    return;
                }

                if (btnSimpanTambah && btnSimpanTambah.disabled) return;

                // Ekstrak baris tabel menjadi array objek items
                const items = [];
                const rows = tbodyTambah.querySelectorAll('tr');
                rows.forEach(row => {
                    const bId = row.querySelector('[name$="[barang_id]"]')?.value;
                    const nb = row.querySelector('[name$="[no_batch]"]')?.value;
                    const ed = row.querySelector('[name$="[expired_date]"]')?.value;
                    const hb = row.querySelector('.harga-beli')?.value ?? row.querySelector('[name$="[harga_beli]"]')?.value;
                    const hj = row.querySelector('.harga-jual')?.value ?? row.querySelector('[name$="[harga_jual]"]')?.value;
                    const nr = row.querySelector('[name$="[no_rak]"]')?.value;
                    const jd = row.querySelector('.jumlah-dipesan-field')?.value ?? row.querySelector('[name$="[jumlah_dipesan]"]')?.value;
                    const jt = row.querySelector('.jumlah-diterima-field')?.value ?? row.querySelector('[name$="[jumlah_diterima]"]')?.value;

                    if (bId || nb) {
                        items.push({
                            barang_id: bId ? parseInt(bId, 10) : '',
                            no_batch: nb ? String(nb).trim() : '',
                            expired_date: ed ? String(ed).trim() : '',
                            harga_beli: hb !== '' && hb !== null && hb !== undefined ? parseFloat(hb) : 0,
                            harga_jual: hj !== '' && hj !== null && hj !== undefined ? parseFloat(hj) : 0,
                            no_rak: nr ? String(nr).trim() : 'A-01',
                            jumlah_dipesan: jd !== '' && jd !== null && jd !== undefined ? parseInt(jd, 10) : 1,
                            jumlah_diterima: jt !== '' && jt !== null && jt !== undefined ? parseInt(jt, 10) : 0,
                        });
                    }
                });

                if (items.length === 0) {
                    alert('Tambahkan minimal 1 baris barang.');
                    return;
                }

                const itemsJsonInput = document.getElementById('items_json_tambah');
                if (itemsJsonInput) {
                    itemsJsonInput.value = JSON.stringify(items);
                }

                const payload = {
                    supplier_id: document.getElementById('supplier_id_tambah')?.value,
                    tanggal: document.getElementById('tanggal_tambah')?.value,
                    tanggal_faktur: document.getElementById('tanggal_faktur_tambah')?.value,
                    no_faktur: document.getElementById('no_faktur_tambah')?.value,
                    keterangan: document.getElementById('keterangan_tambah')?.value || '',
                    jatuh_tempo: document.getElementById('jatuh_tempo_tambah')?.value || null,
                    pembayaran_pertama: document.getElementById('pembayaran_pertama_tambah')?.value || 0,
                    items: items,
                };

                const originalBtnHtml = btnSimpanTambah ? btnSimpanTambah.innerHTML : 'Simpan Faktur';
                if (btnSimpanTambah) {
                    btnSimpanTambah.disabled = true;
                    btnSimpanTambah.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Menyimpan Faktur...
                    `;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    formTambah.querySelector('input[name="_token"]')?.value || '';

                try {
                    const response = await fetch(formTambah.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            applyServerErrorsTambah(data.errors);
                            const firstErrKey = Object.keys(data.errors)[0];
                            const firstErrMsg = Array.isArray(data.errors[firstErrKey]) ? data.errors[firstErrKey][0] : data.errors[firstErrKey];
                            if (typeof window.showToast === 'function') {
                                window.showToast(firstErrMsg || 'Terdapat kesalahan validasi formulir.', 'error');
                            } else {
                                alert(firstErrMsg || 'Terdapat kesalahan validasi formulir.');
                            }
                        } else {
                            const errorMsg = data.message || 'Terjadi kesalahan saat menyimpan faktur.';
                            if (typeof window.showToast === 'function') {
                                window.showToast(errorMsg, 'error');
                            } else {
                                alert(errorMsg);
                            }
                        }
                        if (btnSimpanTambah) {
                            btnSimpanTambah.disabled = false;
                            btnSimpanTambah.innerHTML = originalBtnHtml;
                        }
                        return;
                    }

                    tutupModalTambah();
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Penerimaan barang berhasil disimpan.', 'success');
                    }
                    setTimeout(() => {
                        window.location.href = data.redirect || '/penerimaan';
                    }, 400);

                } catch (err) {
                    console.error('Error saat menyimpan faktur:', err);
                    if (btnSimpanTambah) {
                        btnSimpanTambah.disabled = false;
                        btnSimpanTambah.innerHTML = originalBtnHtml;
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast('Gagal menghubungi server. Silakan coba lagi.', 'error');
                    } else {
                        alert('Gagal menghubungi server. Silakan coba lagi.');
                    }
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

    // ==========================================
    // FITUR IMPORT EXCEL / CSV OBAT PENERIMAAN
    // ==========================================
    function initImportObatModal() {
        const modalImport = document.getElementById('modal-import-obat');
        const backdropImport = document.getElementById('modal-import-obat-backdrop');
        if (!modalImport || !backdropImport) return;

        const btnBukaTambah = document.getElementById('btn-import-excel-tambah');
        const btnBukaCreate = document.getElementById('btn-import-excel');
        const btnClose = document.getElementById('btn-close-import-obat');
        const btnCancel = document.getElementById('btn-import-cancel');
        const btnPrev = document.getElementById('btn-import-prev');
        const btnPrevLabel = document.getElementById('btn-import-prev-label');
        const btnNext = document.getElementById('btn-import-next');
        const btnNextLabel = document.getElementById('btn-import-next-label');
        const btnNextIcon = document.getElementById('btn-import-next-icon');
        const spinner = document.getElementById('btn-import-spinner');

        const dropzone = document.getElementById('dropzone-import-obat');
        const inputFile = document.getElementById('input-file-import-obat');
        const dropzonePrompt = document.getElementById('dropzone-prompt');
        const dropzoneSelected = document.getElementById('dropzone-selected-file');
        const selectedFileName = document.getElementById('selected-file-name');
        const selectedFileSize = document.getElementById('selected-file-size');
        const btnGantiFile = document.getElementById('btn-ganti-file-import');

        const step1Container = document.getElementById('import-step-1');
        const step2Container = document.getElementById('import-step-2');
        const step3Container = document.getElementById('import-step-3');

        const step1Error = document.getElementById('import-step-1-error');
        const step1ErrorText = document.getElementById('import-step-1-error-text');
        const step2Error = document.getElementById('import-step-2-error');
        const step2ErrorText = document.getElementById('import-step-2-error-text');

        let currentStep = 1;
        let selectedFile = null;
        let parsedData = null;
        let validationResult = null;
        let currentPreviewFilter = 'all';

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value
                || '';
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function showStepError(step, msg) {
            if (step === 1) {
                if (step1ErrorText) step1ErrorText.textContent = msg;
                step1Error?.classList.remove('hidden');
            } else if (step === 2) {
                if (step2ErrorText) step2ErrorText.textContent = msg;
                step2Error?.classList.remove('hidden');
            }
        }

        function hideStepError(step) {
            if (step === 1) step1Error?.classList.add('hidden');
            if (step === 2) step2Error?.classList.add('hidden');
        }

        function setBtnLoading(isLoading) {
            if (isLoading) {
                if (btnNext) btnNext.disabled = true;
                spinner?.classList.remove('hidden');
                btnNextIcon?.classList.add('hidden');
            } else {
                if (btnNext) btnNext.disabled = false;
                spinner?.classList.add('hidden');
                btnNextIcon?.classList.remove('hidden');
            }
        }

        function resetImportWizard() {
            currentStep = 1;
            selectedFile = null;
            parsedData = null;
            validationResult = null;
            currentPreviewFilter = 'all';

            if (inputFile) inputFile.value = '';
            dropzonePrompt?.classList.remove('hidden');
            dropzoneSelected?.classList.add('hidden');
            hideStepError(1);
            hideStepError(2);
            setBtnLoading(false);
            goToStep(1);
        }

        function openImportModal() {
            resetImportWizard();
            modalImport.classList.remove('hidden');
            backdropImport.classList.remove('hidden');
        }

        function closeImportModal() {
            modalImport.classList.add('hidden');
            backdropImport.classList.add('hidden');
        }

        function goToStep(step) {
            currentStep = step;
            step1Container?.classList.toggle('hidden', step !== 1);
            step2Container?.classList.toggle('hidden', step !== 2);
            step3Container?.classList.toggle('hidden', step !== 3);

            const ind1Badge = document.querySelector('#indicator-step-1 .step-badge');
            const ind1Label = document.querySelector('#indicator-step-1 .step-label');
            const ind2Badge = document.querySelector('#indicator-step-2 .step-badge');
            const ind2Label = document.querySelector('#indicator-step-2 .step-label');
            const ind3Badge = document.querySelector('#indicator-step-3 .step-badge');
            const ind3Label = document.querySelector('#indicator-step-3 .step-label');
            const line1 = document.getElementById('line-step-1-2');
            const line2 = document.getElementById('line-step-2-3');

            // Step 1
            if (ind1Badge && ind1Label) {
                ind1Badge.className = 'step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ' +
                    (step > 1 ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white');
                ind1Badge.innerHTML = step > 1 ? '&#10003;' : '1';
                ind1Label.className = 'step-label text-xs font-semibold ' + (step > 1 ? 'text-emerald-700' : 'text-blue-700');
            }
            if (line1) {
                line1.className = 'flex-1 h-0.5 mx-3 ' + (step > 1 ? 'bg-emerald-500' : 'bg-gray-200');
            }

            // Step 2
            if (ind2Badge && ind2Label) {
                if (step >= 2) {
                    ind2Badge.className = 'step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ' +
                        (step > 2 ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white');
                    ind2Badge.innerHTML = step > 2 ? '&#10003;' : '2';
                    ind2Label.className = 'step-label text-xs font-semibold ' + (step > 2 ? 'text-emerald-700' : 'text-blue-700');
                } else {
                    ind2Badge.className = 'step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500';
                    ind2Badge.textContent = '2';
                    ind2Label.className = 'step-label text-xs font-medium text-gray-400';
                }
            }
            if (line2) {
                line2.className = 'flex-1 h-0.5 mx-3 ' + (step > 2 ? 'bg-emerald-500' : 'bg-gray-200');
            }

            // Step 3
            if (ind3Badge && ind3Label) {
                if (step === 3) {
                    ind3Badge.className = 'step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-blue-600 text-white';
                    ind3Badge.textContent = '3';
                    ind3Label.className = 'step-label text-xs font-semibold text-blue-700';
                } else {
                    ind3Badge.className = 'step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500';
                    ind3Badge.textContent = '3';
                    ind3Label.className = 'step-label text-xs font-medium text-gray-400';
                }
            }

            // Update footer labels
            if (step === 1) {
                btnPrev?.classList.add('hidden');
                if (btnNextLabel) btnNextLabel.textContent = 'Lanjutkan ke Pemetaan';
            } else if (step === 2) {
                btnPrev?.classList.remove('hidden');
                if (btnPrevLabel) btnPrevLabel.textContent = 'Kembali ke Unggah';
                if (btnNextLabel) btnNextLabel.textContent = 'Validasi & Preview Data';
            } else if (step === 3) {
                btnPrev?.classList.remove('hidden');
                if (btnPrevLabel) btnPrevLabel.textContent = 'Kembali ke Pemetaan';
                if (btnNextLabel) btnNextLabel.textContent = 'Masukkan ke Form';
            }
        }

        function handleFile(file) {
            hideStepError(1);
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                showStepError(1, `Format file .${ext} tidak didukung. Harap pilih file .xlsx, .xls, atau .csv.`);
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                showStepError(1, `Ukuran file (${formatFileSize(file.size)}) melebihi batas maksimal 10MB.`);
                return;
            }

            selectedFile = file;
            if (selectedFileName) selectedFileName.textContent = file.name;
            if (selectedFileSize) selectedFileSize.textContent = formatFileSize(file.size);
            dropzonePrompt?.classList.add('hidden');
            dropzoneSelected?.classList.remove('hidden');
        }

        async function processStep1() {
            if (!selectedFile) {
                showStepError(1, 'Silakan pilih file Excel atau CSV terlebih dahulu.');
                return;
            }

            setBtnLoading(true);
            hideStepError(1);

            const formData = new FormData();
            formData.append('file', selectedFile);

            try {
                const res = await fetch('/penerimaan/import-parse', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: formData,
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memproses file spreadsheet.');
                }

                parsedData = data;

                // Populate Step 2 mapping dropdowns
                const mappingFields = [
                    { id: 'map-nama-barang', key: 'nama_barang' },
                    { id: 'map-barcode', key: 'barcode' },
                    { id: 'map-no-batch', key: 'no_batch' },
                    { id: 'map-expired-date', key: 'expired_date' },
                    { id: 'map-harga-beli', key: 'harga_beli' },
                    { id: 'map-harga-jual', key: 'harga_jual' },
                    { id: 'map-jumlah-dipesan', key: 'jumlah_dipesan' },
                    { id: 'map-jumlah-diterima', key: 'jumlah_diterima' },
                    { id: 'map-no-rak', key: 'no_rak' },
                ];

                mappingFields.forEach(f => {
                    const sel = document.getElementById(f.id);
                    if (!sel) return;
                    sel.innerHTML = '<option value="">-- Tidak Dipetakan --</option>';

                    const suggestedHeader = parsedData.suggested_mapping ? parsedData.suggested_mapping[f.key] : null;

                    parsedData.headers.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = h;
                        if (suggestedHeader && suggestedHeader.toLowerCase() === h.toLowerCase()) {
                            opt.selected = true;
                        }
                        sel.appendChild(opt);
                    });
                });

                goToStep(2);
            } catch (err) {
                showStepError(1, err.message || 'Terjadi kesalahan saat membaca file.');
            } finally {
                setBtnLoading(false);
            }
        }

        async function processStep2() {
            hideStepError(2);

            const mapping = {
                nama_barang: document.getElementById('map-nama-barang')?.value || '',
                barcode: document.getElementById('map-barcode')?.value || '',
                no_batch: document.getElementById('map-no-batch')?.value || '',
                expired_date: document.getElementById('map-expired-date')?.value || '',
                harga_beli: document.getElementById('map-harga-beli')?.value || '',
                harga_jual: document.getElementById('map-harga-jual')?.value || '',
                jumlah_dipesan: document.getElementById('map-jumlah-dipesan')?.value || '',
                jumlah_diterima: document.getElementById('map-jumlah-diterima')?.value || '',
                no_rak: document.getElementById('map-no-rak')?.value || '',
            };

            if (!mapping.nama_barang && !mapping.barcode) {
                showStepError(2, 'Kolom "Nama Barang" atau "Barcode" wajib dipetakan untuk mengidentifikasi obat.');
                return;
            }
            if (!mapping.no_batch) {
                showStepError(2, 'Kolom "No. Batch" wajib dipetakan.');
                return;
            }
            if (!mapping.expired_date) {
                showStepError(2, 'Kolom "Expired Date" wajib dipetakan.');
                return;
            }
            if (!mapping.harga_beli) {
                showStepError(2, 'Kolom "Harga Beli" wajib dipetakan.');
                return;
            }
            if (!mapping.jumlah_diterima && !mapping.jumlah_dipesan) {
                showStepError(2, 'Kolom "Jumlah Diterima" atau "Jumlah Dipesan" wajib dipetakan.');
                return;
            }

            setBtnLoading(true);

            const tanggalPenerimaan = document.getElementById('tanggal_tambah')?.value
                || document.getElementById('tanggal')?.value
                || '';

            try {
                const res = await fetch('/penerimaan/import-validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({
                        file_cache_id: parsedData.file_cache_id,
                        mapping: mapping,
                        tanggal_penerimaan: tanggalPenerimaan,
                    }),
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memvalidasi data obat.');
                }

                validationResult = data;

                // Update Step 3 summary counters
                const elTotal = document.getElementById('summary-total-rows');
                const elValid = document.getElementById('summary-valid-rows');
                const elWarning = document.getElementById('summary-warning-rows');
                const elInvalid = document.getElementById('summary-invalid-rows');

                if (elTotal) elTotal.textContent = data.summary.total_rows || 0;
                if (elValid) elValid.textContent = data.summary.valid_count || 0;
                if (elWarning) elWarning.textContent = data.summary.warning_count || 0;
                if (elInvalid) elInvalid.textContent = data.summary.invalid_count || 0;

                currentPreviewFilter = 'all';
                updatePreviewFilterButtons();
                renderPreviewTable(data.rows, currentPreviewFilter);

                goToStep(3);
            } catch (err) {
                showStepError(2, err.message || 'Terjadi kesalahan saat memvalidasi data.');
            } finally {
                setBtnLoading(false);
            }
        }

        function renderPreviewTable(rows, filter) {
            const tbody = document.getElementById('import-preview-rows');
            if (!tbody) return;
            tbody.innerHTML = '';

            const filtered = rows.filter(r => {
                if (filter === 'ready') return r.status === 'valid' || r.status === 'warning';
                if (filter === 'issues') return r.status === 'warning' || r.status === 'invalid';
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center py-6 text-gray-400 italic">Tidak ada baris data yang sesuai dengan filter.</td></tr>';
                return;
            }

            filtered.forEach(r => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50/80 transition-colors ' +
                    (r.status === 'invalid' ? 'bg-red-50/30' : (r.status === 'warning' ? 'bg-amber-50/30' : ''));

                let badgeHtml = '';
                if (r.status === 'valid') {
                    badgeHtml = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Siap</span>';
                } else if (r.status === 'warning') {
                    badgeHtml = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Perlu Dilengkapi</span>';
                } else {
                    badgeHtml = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Tidak Valid</span>';
                }

                const hargaBeliStr = r.harga_beli ? 'Rp ' + Math.round(r.harga_beli).toLocaleString('id-ID') : 'Rp 0';
                const hargaJualStr = (r.harga_jual !== null && r.harga_jual !== undefined)
                    ? 'Rp ' + Math.round(r.harga_jual).toLocaleString('id-ID')
                    : '<span class="text-amber-600 italic">Kosong</span>';

                let notesHtml = '';
                if (r.errors && r.errors.length > 0) {
                    notesHtml += '<div class="text-red-600 font-medium">' + r.errors.join('<br>') + '</div>';
                }
                if (r.warnings && r.warnings.length > 0) {
                    notesHtml += '<div class="text-amber-600 font-medium">' + r.warnings.join('<br>') + '</div>';
                }
                if (!notesHtml) {
                    notesHtml = '<span class="text-emerald-600 font-medium">Obat & batch valid</span>';
                }

                tr.innerHTML = `
                    <td class="px-3 py-2 text-center text-gray-400 font-mono">${r.row_num}</td>
                    <td class="px-3 py-2 text-center">${badgeHtml}</td>
                    <td class="px-3 py-2">
                        <span class="font-bold text-gray-800">${escapeHtml(r.nama_barang)}</span>
                        ${r.pabrik_nama ? `<span class="block text-[10px] text-gray-400">Pabrik: ${escapeHtml(r.pabrik_nama)}</span>` : ''}
                    </td>
                    <td class="px-3 py-2 text-center font-mono text-[11px]">${escapeHtml(r.no_batch || '—')}</td>
                    <td class="px-3 py-2 text-center font-mono text-[11px]">${escapeHtml(r.expired_date || '—')}</td>
                    <td class="px-3 py-2 text-right font-mono">${hargaBeliStr}</td>
                    <td class="px-3 py-2 text-right font-mono">${hargaJualStr}</td>
                    <td class="px-3 py-2 text-center font-mono">${r.jumlah_diterima}</td>
                    <td class="px-3 py-2 text-center text-gray-500">${escapeHtml(r.satuan_nama || '—')}</td>
                    <td class="px-3 py-2 text-[11px]">${notesHtml}</td>
                `;

                tbody.appendChild(tr);
            });
        }

        function updatePreviewFilterButtons() {
            document.querySelectorAll('.preview-filter-btn').forEach(btn => {
                const isMatch = (btn.dataset.filter === currentPreviewFilter);
                if (isMatch) {
                    btn.className = 'preview-filter-btn px-2.5 py-1 text-xs font-semibold rounded-md transition-colors bg-blue-600 text-white';
                } else {
                    btn.className = 'preview-filter-btn px-2.5 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 rounded-md transition-colors';
                }
            });
        }

        function processStep3() {
            if (!validationResult || !validationResult.rows) return;

            const onlyReady = document.getElementById('chk-only-ready-import')?.checked ?? true;
            const rowsToImport = validationResult.rows.filter(r => {
                if (onlyReady) {
                    return (r.status === 'valid' || r.status === 'warning') && r.barang_id;
                }
                return r.can_import && r.barang_id;
            });

            if (rowsToImport.length === 0) {
                alert('Tidak ada baris obat yang siap dimasukkan ke formulir. Harap periksa validasi atau kecocokan master obat Anda.');
                return;
            }

            // Target 1: Modal Tambah (penerimaan.index)
            const tbodyTambahEl = document.getElementById('item-rows-tambah');
            const emptyHintTambahEl = document.getElementById('empty-hint-tambah');

            // Target 2: Standalone Form (penerimaan.create)
            const tbodyCreateEl = document.getElementById('item-rows');

            if (typeof window.insertImportedItemsBatchTambah === 'function' && tbodyTambahEl) {
                // Hapus baris awal kosong jika belum diisi barang dan batch
                const existingRows = tbodyTambahEl.querySelectorAll('tr');
                if (existingRows.length === 1) {
                    const bVal = existingRows[0].querySelector('[name$="[barang_id]"]')?.value;
                    const batchVal = existingRows[0].querySelector('[name$="[no_batch]"]')?.value;
                    if (!bVal && !batchVal) {
                        existingRows[0].remove();
                    }
                }

                window.insertImportedItemsBatchTambah(rowsToImport);

                if (emptyHintTambahEl) emptyHintTambahEl.style.display = 'none';
                if (typeof window.updateTotalTambah === 'function') {
                    window.updateTotalTambah();
                }

            } else if (typeof window.insertImportedItemTambah === 'function' && tbodyTambahEl) {
                // Hapus baris awal kosong jika belum diisi barang dan batch
                const existingRows = tbodyTambahEl.querySelectorAll('tr');
                if (existingRows.length === 1) {
                    const bVal = existingRows[0].querySelector('[name$="[barang_id]"]')?.value;
                    const batchVal = existingRows[0].querySelector('[name$="[no_batch]"]')?.value;
                    if (!bVal && !batchVal) {
                        existingRows[0].remove();
                    }
                }

                rowsToImport.forEach(item => window.insertImportedItemTambah(item));

                if (emptyHintTambahEl) emptyHintTambahEl.style.display = 'none';
                if (typeof window.updateTotalTambah === 'function') {
                    window.updateTotalTambah();
                }

            } else if (typeof window.insertImportedItemsBatchCreate === 'function' && tbodyCreateEl) {
                const existingRows = tbodyCreateEl.querySelectorAll('tr');
                if (existingRows.length === 1) {
                    const bVal = existingRows[0].querySelector('[name$="[barang_id]"]')?.value;
                    const batchVal = existingRows[0].querySelector('[name$="[no_batch]"]')?.value;
                    if (!bVal && !batchVal) {
                        existingRows[0].remove();
                    }
                }

                window.insertImportedItemsBatchCreate(rowsToImport);

                const emptyHintEl = document.getElementById('empty-hint');
                if (emptyHintEl) emptyHintEl.style.display = 'none';
                if (typeof window.updateTotalCreate === 'function') {
                    window.updateTotalCreate();
                }

            } else if (typeof window.insertImportedItemCreate === 'function' && tbodyCreateEl) {
                const existingRows = tbodyCreateEl.querySelectorAll('tr');
                if (existingRows.length === 1) {
                    const bVal = existingRows[0].querySelector('[name$="[barang_id]"]')?.value;
                    const batchVal = existingRows[0].querySelector('[name$="[no_batch]"]')?.value;
                    if (!bVal && !batchVal) {
                        existingRows[0].remove();
                    }
                }

                rowsToImport.forEach(item => window.insertImportedItemCreate(item));

                const emptyHintEl = document.getElementById('empty-hint');
                if (emptyHintEl) emptyHintEl.style.display = 'none';
                if (typeof window.updateTotalCreate === 'function') {
                    window.updateTotalCreate();
                }
            }

            closeImportModal();

            const countImported = rowsToImport.length;
            const countWarning = rowsToImport.filter(r => r.status === 'warning').length;

            if (typeof window.showToast === 'function') {
                window.showToast(`Berhasil memasukkan ${countImported} item obat ke tabel penerimaan.`, 'success');
                if (countWarning > 0) {
                    setTimeout(() => {
                        window.showToast(`Terdapat ${countWarning} item yang perlu dicek atau dilengkapi harga jualnya.`, 'warning', 4500);
                    }, 400);
                }
            } else {
                alert(`Berhasil memasukkan ${countImported} item obat ke tabel penerimaan.`);
            }
        }

        // Event Listeners Modal Import
        btnNext?.addEventListener('click', function () {
            if (currentStep === 1) {
                processStep1();
            } else if (currentStep === 2) {
                processStep2();
            } else if (currentStep === 3) {
                processStep3();
            }
        });

        btnPrev?.addEventListener('click', function () {
            if (currentStep === 2) {
                goToStep(1);
            } else if (currentStep === 3) {
                goToStep(2);
            }
        });

        btnClose?.addEventListener('click', closeImportModal);
        btnCancel?.addEventListener('click', closeImportModal);
        backdropImport?.addEventListener('click', closeImportModal);

        btnBukaTambah?.addEventListener('click', openImportModal);
        btnBukaCreate?.addEventListener('click', openImportModal);

        // Dropzone interactions
        dropzone?.addEventListener('click', () => inputFile?.click());

        btnGantiFile?.addEventListener('click', (e) => {
            e.stopPropagation();
            selectedFile = null;
            if (inputFile) inputFile.value = '';
            dropzonePrompt?.classList.remove('hidden');
            dropzoneSelected?.classList.add('hidden');
            hideStepError(1);
        });

        ['dragenter', 'dragover'].forEach(name => {
            dropzone?.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(name => {
            dropzone?.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone?.addEventListener('drop', (e) => {
            const files = e.dataTransfer?.files;
            if (files && files.length > 0) {
                handleFile(files[0]);
            }
        });

        inputFile?.addEventListener('change', () => {
            if (inputFile.files && inputFile.files.length > 0) {
                handleFile(inputFile.files[0]);
            }
        });

        // Filter preview buttons
        document.querySelectorAll('.preview-filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                currentPreviewFilter = this.dataset.filter || 'all';
                updatePreviewFilterButtons();
                if (validationResult && validationResult.rows) {
                    renderPreviewTable(validationResult.rows, currentPreviewFilter);
                }
            });
        });
    }

    // Jalankan inisialisasi fitur import obat
    initImportObatModal();
});