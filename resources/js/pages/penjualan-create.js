// resources/js/pages/penjualan-create.js
// Redesigned cashier POS logic for penjualan/create with complete barcode scanning flow

const pageData = JSON.parse(document.getElementById('penjualan-data')?.textContent || '{}');
const pelanggans = pageData.pelanggans || [];
const barangs = pageData.barangs || [];



const tabBtnCari = document.getElementById('tab-btn-cari');
const tabBtnScan = document.getElementById('tab-btn-scan');
const panelCariContent = document.getElementById('panel-cari-content');
const panelScanContent = document.getElementById('panel-scan-content');

// Elements - Search & Filter
const cariInput = document.getElementById('cari-barang');
const btnClearCari = document.getElementById('btn-clear-cari');
const daftarBarang = document.getElementById('daftar-barang');
const infoJumlahBarang = document.getElementById('info-jumlah-barang');
const btnRefreshBarang = document.getElementById('btn-refresh-barang');
const kategoriButtons = document.querySelectorAll('.btn-kategori');
let activeKategori = 'semua';

// Elements - Scan Barcode
const barcodeInput = document.getElementById('barcode-input');
const btnClearBarcode = document.getElementById('btn-clear-barcode');
const btnSubmitBarcode = document.getElementById('btn-submit-barcode');
const barcodeSpinner = document.getElementById('barcode-spinner');
const scanStatusContainer = document.getElementById('scan-status-container');
const scanViewfinderCard = document.getElementById('scan-viewfinder-card');
let isProcessingScan = false;
let scanStatusTimeout = null;

// Elements - Jenis Transaksi
const radioNonResepKiri = document.getElementById('jenis-non-resep-kiri');
const radioResepKiri = document.getElementById('jenis-resep-kiri');
const labelJenisNonResep = document.getElementById('label-jenis-non-resep');
const labelJenisResep = document.getElementById('label-jenis-resep');
const radioNonResepKanan = document.getElementById('jenis-non-resep-kanan');
const radioResepKanan = document.getElementById('jenis-resep-kanan');
const hiddenJenisTransaksi = document.getElementById('jenis-transaksi-value');
const formDataResep = document.getElementById('form-data-resep');
const inputNamaDokter = document.getElementById('nama-dokter');
const inputIdDokter = document.getElementById('id-dokter');
const inputAlamatLembaga = document.getElementById('alamat-lembaga');

// Elements - Keranjang & Form
const form = document.getElementById('form-penjualan');
const keranjangItems = document.getElementById('keranjang-items');
const keranjangKosong = document.getElementById('keranjang-kosong');
const btnHapusSemua = document.getElementById('btn-hapus-semua');
const displaySubtotal = document.getElementById('display-subtotal');
const displayDiskon = document.getElementById('display-diskon');
const totalDisplay = document.getElementById('total-display');

// Elements - Member Selection
const btnPilihMember = document.getElementById('btn-pilih-member');
const btnResetPelanggan = document.getElementById('btn-reset-pelanggan');
const memberInfoDisplay = document.getElementById('member-info-display');
const modalPilihMember = document.getElementById('modal-pilih-member');
const btnCloseModalPilihMember = document.getElementById('btn-close-modal-pilih-member');
const pelangganSearchInput = document.getElementById('pencarian-pelanggan');
const pelangganSearchHasil = document.getElementById('hasil-pencarian-pelanggan');
const memberQuickList = document.getElementById('member-quick-list');

// Elements - Hidden & Compat
const selectedPelangganIdInput = document.getElementById('selected-pelanggan-id');
const pelangganNamaInput = document.getElementById('pelanggan-nama');
const pelangganTeleponInput = document.getElementById('pelanggan-telepon');
const selectedPelangganNama = document.getElementById('selected-pelanggan-nama');
const selectedPelangganMemberId = document.getElementById('selected-pelanggan-member-id');
const badgeDiskonMember = document.getElementById('badge-diskon-member');
const labelDiskonPercent = document.getElementById('label-diskon-percent');

// Elements - Modal Tambah Member Baru
const modalDaftarMember = document.getElementById('modal-daftar-member');
const btnTambahMember = document.getElementById('btn-tambah-member');
const btnCancelMember = document.getElementById('btn-cancel-member');
const btnCloseMemberX = document.getElementById('btn-close-member-x');
const formDaftarMember = document.getElementById('form-daftar-member');
const memberNamaInput = document.getElementById('member-nama');
const memberTeleponInput = document.getElementById('member-telepon');
const memberStatusInput = document.getElementById('member-status');
const memberCustomDiscountInput = document.getElementById('member-custom-discount');
const memberError = document.getElementById('member-error');

// Elements - Pembayaran
const metodePembayaran = document.getElementById('metode-pembayaran');
const bagianPembayaranCash = document.getElementById('bagian-pembayaran-cash');
const bagianJatuhTempo = document.getElementById('bagian-jatuh-tempo');
const inputJatuhTempo = document.getElementById('input-jatuh-tempo');
const infoPembayaranPiutang = document.getElementById('info-pembayaran-piutang');
const bagianPembayaranQris = document.getElementById('bagian-pembayaran-qris');
const bagianPembayaranDebit = document.getElementById('bagian-pembayaran-debit');
const qrisLunas = document.getElementById('qris-lunas');
const debitLunas = document.getElementById('debit-lunas');
const inputBayar = document.getElementById('input-bayar');
const barisKembalian = document.getElementById('baris-kembalian');
const displayKembalian = document.getElementById('display-kembalian');
const displayKembalianBox = document.getElementById('display-kembalian-box');
const labelKembalian = document.getElementById('label-kembalian');
const btnProsesTransaksi = document.getElementById('btn-proses-transaksi');

let cart = {};
let selectedPelanggan = null;
let currentTotal = 0;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

// =========================================================================
// TAB SWITCHING (Cari Obat vs Scan Barcode)
// =========================================================================
function switchTab(tab) {
    const iconCari = tabBtnCari?.querySelector('svg');
    const iconScan = tabBtnScan?.querySelector('svg');

    if (tab === 'scan') {
        // Tab Scan Active (light blue pill, matches Tab Cari when active)
        if (tabBtnScan) {
            tabBtnScan.className = 'px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all bg-blue-100/80 text-blue-700 cursor-pointer';
        }
        if (iconScan) {
            iconScan.setAttribute('class', 'w-4 h-4 text-blue-600');
        }

        // Tab Cari Inactive
        if (tabBtnCari) {
            tabBtnCari.className = 'px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all text-slate-600 hover:text-slate-800 hover:bg-slate-100 cursor-pointer';
        }
        if (iconCari) {
            iconCari.setAttribute('class', 'w-4 h-4 text-slate-400');
        }

        panelCariContent?.classList.add('hidden');
        panelScanContent?.classList.remove('hidden');

        if (barcodeInput) {
            barcodeInput.value = '';
            barcodeInput.focus();
            barcodeInput.select();
        }
    } else {
        // Tab Cari Active (light blue pill)
        if (tabBtnCari) {
            tabBtnCari.className = 'px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all bg-blue-100/80 text-blue-700 cursor-pointer';
        }
        if (iconCari) {
            iconCari.setAttribute('class', 'w-4 h-4 text-blue-600');
        }

        // Tab Scan Inactive
        if (tabBtnScan) {
            tabBtnScan.className = 'px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all text-slate-600 hover:text-slate-800 hover:bg-slate-100 cursor-pointer';
        }
        if (iconScan) {
            iconScan.setAttribute('class', 'w-4 h-4 text-slate-400');
        }

        panelScanContent?.classList.add('hidden');
        panelCariContent?.classList.remove('hidden');

        if (cariInput) {
            cariInput.focus();
        }
    }
}

tabBtnCari?.addEventListener('click', () => switchTab('cari'));
tabBtnScan?.addEventListener('click', () => switchTab('scan'));

// Klik area scanner langsung memfokuskan kursor ke input barcode
scanViewfinderCard?.addEventListener('click', () => {
    if (barcodeInput) {
        barcodeInput.focus();
    }
});

// =========================================================================
// SCAN STATUS NOTIFICATION COMPONENT
// =========================================================================
function showScanStatus(type, title, message) {
    if (!scanStatusContainer) return;
    if (scanStatusTimeout) clearTimeout(scanStatusTimeout);

    let borderClass = 'border-emerald-200 bg-emerald-50/90 text-emerald-900';
    let iconSvg = `
        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;

    if (type === 'error') {
        borderClass = 'border-red-200 bg-red-50/90 text-red-900';
        iconSvg = `
            <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;
    } else if (type === 'warning') {
        borderClass = 'border-amber-200 bg-amber-50/90 text-amber-900';
        iconSvg = `
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        `;
    }

    scanStatusContainer.innerHTML = `
        <div class="p-3 rounded-xl border ${borderClass} shadow-2xs flex items-start gap-2.5 transition-all text-xs">
            ${iconSvg}
            <div class="min-w-0 flex-1 leading-snug">
                <div class="font-bold text-xs">${title}</div>
                ${message ? `<div class="text-[11px] opacity-90 mt-0.5">${message}</div>` : ''}
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-700 font-bold text-sm ml-1" onclick="document.getElementById('scan-status-container').classList.add('hidden')">&times;</button>
        </div>
    `;
    scanStatusContainer.classList.remove('hidden');

    scanStatusTimeout = setTimeout(() => {
        scanStatusContainer.classList.add('hidden');
    }, 6000);
}

// =========================================================================
// SCAN BARCODE PROCESSING (USB Scanner & Manual Input)
// =========================================================================
function processBarcodeScan(code) {
    if (isProcessingScan) return;

    const raw = (code || '').trim();
    if (!raw) {
        if (barcodeInput) {
            barcodeInput.value = '';
            barcodeInput.focus();
        }
        return;
    }

    isProcessingScan = true;
    if (barcodeSpinner) barcodeSpinner.classList.remove('hidden');
    if (btnSubmitBarcode) btnSubmitBarcode.disabled = true;

    const lower = raw.toLowerCase();

    // 1. Cari exact match pada barcode
    let found = barangs.find(b => b.barcode && b.barcode.trim().toLowerCase() === lower);

    // 2. Cari exact match pada kode apotek
    if (!found) {
        found = barangs.find(b => b.kode_apotek && b.kode_apotek.trim().toLowerCase() === lower);
    }

    // 3. Cari exact match pada nama barang
    if (!found) {
        found = barangs.find(b => b.nama && b.nama.trim().toLowerCase() === lower);
    }

    // 4. Cari partial barcode match (starts/ends with atau contains)
    if (!found) {
        found = barangs.find(b => b.barcode && (
            b.barcode.trim().toLowerCase().startsWith(lower) ||
            b.barcode.trim().toLowerCase().endsWith(lower) ||
            b.barcode.trim().toLowerCase().includes(lower)
        ));
    }

    // Validasi Hasil Pencarian
    if (!found) {
        // Barcode tidak ditemukan
        showScanStatus('error', 'Barcode tidak ditemukan', `Kode barcode "${raw}" tidak terdaftar pada data obat.`);
    } else if (found.stok <= 0) {
        // Stok habis
        showScanStatus('error', `Stok ${found.nama} Habis`, `Stok obat saat ini 0 ${found.satuan || 'Pcs'}. Barang tidak dapat ditambahkan ke keranjang.`);
    } else {
        const currentQty = cart[found.id] ? cart[found.id].jumlah : 0;

        if (currentQty >= found.stok) {
            // Jumlah melebihi stok yang tersedia
            showScanStatus('warning', `Batas Stok Tercapai: ${found.nama}`, `Jumlah di keranjang (${currentQty} ${found.satuan || 'Pcs'}) sudah mencapai batas total stok tersedia (${found.stok} ${found.satuan || 'Pcs'}).`);
        } else {
            // Validasi & Penambahan Berhasil
            if (cart[found.id]) {
                cart[found.id].jumlah += 1;
            } else {
                cart[found.id] = {
                    id: found.id,
                    nama: found.nama,
                    kode_apotek: found.kode_apotek,
                    no_batch: found.no_batch || (found.kode_apotek ? 'B-' + found.kode_apotek : 'B-001'),
                    satuan: found.satuan || 'Pcs',
                    harga: found.harga ?? 0,
                    stok: found.stok,
                    jumlah: 1,
                    diskon_custom_percent: found.diskon_custom_percent ?? 0
                };
            }

            renderKeranjang();

            const totalItemQty = cart[found.id].jumlah;
            showScanStatus(
                'success',
                `Berhasil Ditambahkan: ${found.nama}`,
                `No. Batch: ${found.no_batch || '-'} &nbsp;·&nbsp; ${formatRupiah(found.harga ?? 0)} &nbsp;·&nbsp; Jumlah di keranjang: ${totalItemQty} ${found.satuan || 'Pcs'}`
            );
        }
    }

    // Bersihkan input & kembalikan fokus kursor otomatis untuk pemindaian berikutnya
    if (barcodeInput) {
        barcodeInput.value = '';
    }

    setTimeout(() => {
        isProcessingScan = false;
        if (barcodeSpinner) barcodeSpinner.classList.add('hidden');
        if (btnSubmitBarcode) btnSubmitBarcode.disabled = false;
        if (barcodeInput) {
            barcodeInput.focus();
        }
    }, 150);
}

// Event Listeners Scanner: Keydown (Enter) & Tombol Manual
barcodeInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        // Cegah submit form utama saat scanner USB mengirim Enter
        e.preventDefault();
        e.stopPropagation();
        processBarcodeScan(barcodeInput.value);
    }
});

btnSubmitBarcode?.addEventListener('click', (e) => {
    e.preventDefault();
    if (barcodeInput) {
        processBarcodeScan(barcodeInput.value);
    }
});

btnClearBarcode?.addEventListener('click', () => {
    if (barcodeInput) {
        barcodeInput.value = '';
        barcodeInput.focus();
    }
});

// =========================================================================
// JENIS TRANSAKSI (Non Resep vs Resep)
// =========================================================================
function getJenisTransaksiAktif() {
    return hiddenJenisTransaksi?.value || 'non_resep';
}

function sinkronkanJenisTransaksi(nilai) {
    const isResep = nilai === 'resep';

    if (hiddenJenisTransaksi) hiddenJenisTransaksi.value = isResep ? 'resep' : 'non_resep';
    if (radioNonResepKiri) radioNonResepKiri.checked = !isResep;
    if (radioResepKiri) radioResepKiri.checked = isResep;
    if (radioNonResepKanan) radioNonResepKanan.checked = !isResep;
    if (radioResepKanan) radioResepKanan.checked = isResep;

    // Toggle styling of pill labels
    if (labelJenisNonResep && labelJenisResep) {
        if (!isResep) {
            labelJenisNonResep.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-blue-500 bg-blue-50/70 text-blue-700 text-xs font-semibold cursor-pointer transition-all';
            labelJenisResep.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold cursor-pointer hover:border-slate-300 transition-all';
        } else {
            labelJenisResep.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-blue-500 bg-blue-50/70 text-blue-700 text-xs font-semibold cursor-pointer transition-all';
            labelJenisNonResep.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold cursor-pointer hover:border-slate-300 transition-all';
        }
    }

    if (formDataResep) formDataResep.classList.toggle('hidden', !isResep);

    if (inputNamaDokter) inputNamaDokter.required = isResep;
    if (inputIdDokter) inputIdDokter.required = isResep;
    if (inputAlamatLembaga) inputAlamatLembaga.required = isResep;

    if (!isResep) {
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }
}

[radioNonResepKiri, radioResepKiri, radioNonResepKanan, radioResepKanan].forEach(radio => {
    if (radio) {
        radio.addEventListener('change', () => {
            if (radio.checked) sinkronkanJenisTransaksi(radio.value);
        });
    }
});

// =========================================================================
// RENDER DAFTAR BARANG (Kartu Sederhana tanpa ikon/gambar)
// =========================================================================
function renderDaftarBarang(filter = '') {
    const query = (typeof filter === 'string' ? filter : (cariInput ? cariInput.value : '')).trim().toLowerCase();

    // Hanya tampilkan barang yang stok > 0 di kartu pemilihan obat
    let hasil = barangs.filter(b => b.stok > 0);

    // Filter by Kategori
    if (activeKategori !== 'semua') {
        hasil = hasil.filter(b => {
            const kat = (b.kategori || '').toLowerCase();
            const nama = b.nama.toLowerCase();
            if (activeKategori === 'obat') {
                return kat.includes('obat') || (!kat.includes('alat') && !kat.includes('vitamin'));
            } else if (activeKategori === 'alat_kesehatan') {
                return kat.includes('alat');
            } else if (activeKategori === 'vitamin') {
                return kat.includes('vitamin') || kat.includes('suplemen') || nama.includes('vitamin');
            } else if (activeKategori === 'kecantikan') {
                return kat.includes('kecantikan') || kat.includes('perawatan')
                    || nama.includes('kecantikan') || nama.includes('perawatan');
            }
            return true;
        });
    }

    // Filter by Text Query
    if (query) {
        hasil = hasil.filter(b => {
            const cariNama = b.nama.toLowerCase().includes(query);
            const cariKode = b.kode_apotek && b.kode_apotek.toLowerCase().includes(query);
            const cariBarcode = b.barcode && b.barcode.toLowerCase().includes(query);
            const cariSatuan = b.satuan && b.satuan.toLowerCase().includes(query);
            const cariBatch = b.no_batch && b.no_batch.toLowerCase().includes(query);
            return cariNama || cariKode || cariBarcode || cariSatuan || cariBatch;
        });
    }

    if (infoJumlahBarang) {
        if (query || activeKategori !== 'semua') {
            infoJumlahBarang.textContent = `${hasil.length} barang ditemukan`;
            infoJumlahBarang.classList.remove('hidden');
        } else {
            infoJumlahBarang.classList.add('hidden');
        }
    }

    if (btnClearCari) {
        btnClearCari.classList.toggle('hidden', !query);
    }

    daftarBarang.innerHTML = hasil.map(b => {
        const batchDisplay = b.no_batch || (b.kode_apotek ? 'B-' + b.kode_apotek : 'B-001');
        return `
            
                <div class="border border-slate-200/90 rounded-xl p-3 bg-white hover:border-blue-300 hover:shadow-xs transition-all flex flex-col group min-w-0">
                <div class="min-w-0">
                    <div class="font-bold text-slate-800 text-xs truncate" title="${b.nama}">${b.nama}</div>
                    <div class="text-xs text-slate-400 mt-0.5 truncate">
                        Stok ${b.stok} &nbsp;·&nbsp; No Batch ${batchDisplay} &nbsp;·&nbsp; ${b.satuan || 'Pcs'}
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2 min-w-0">
                    <div class="font-bold text-blue-600 text-xs truncate mr-1">${formatRupiah(b.harga ?? 0)}</div>                            
                        <button type="button" data-id="${b.id}" class="btn-tambah-obat w-6 h-6 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs transition-transform active:scale-90 cursor-pointer shrink-0" title="Tambah ke keranjang">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="width: 14px; height: 14px; display: block; flex-shrink: 0;">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                </div>
            </div>
        `;
    }).join('') || '<p class="text-xs text-slate-400 py-8 text-center col-span-2">Barang tidak ditemukan.</p>';
}

// Search & Clear Event Listeners
cariInput?.addEventListener('input', () => renderDaftarBarang(cariInput.value));
btnClearCari?.addEventListener('click', () => {
    if (cariInput) {
        cariInput.value = '';
        renderDaftarBarang('');
        cariInput.focus();
    }
});

// Category Filter Buttons
kategoriButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        kategoriButtons.forEach(b => {
            b.className = 'btn-kategori px-3 py-1 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 hover:border-slate-300 transition-colors cursor-pointer';
        });
        btn.className = 'btn-kategori active px-3.5 py-1 rounded-full text-xs font-semibold transition-colors bg-blue-600 text-white cursor-pointer';
        activeKategori = btn.dataset.kategori;
        renderDaftarBarang(cariInput?.value || '');
    });
});

// Refresh Button
btnRefreshBarang?.addEventListener('click', () => {
    activeKategori = 'semua';
    kategoriButtons.forEach(b => {
        if (b.dataset.kategori === 'semua') {
            b.className = 'btn-kategori active px-3.5 py-1 rounded-full text-xs font-semibold transition-colors bg-blue-600 text-white cursor-pointer';
        } else {
            b.className = 'btn-kategori px-3 py-1 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 hover:border-slate-300 transition-colors cursor-pointer';
        }
    });
    if (cariInput) cariInput.value = '';
    renderDaftarBarang('');
});

// =========================================================================
// ADD TO CART (Dari Kartu Panel Kiri)
// =========================================================================
function tambahKeKeranjang(id) {
    const barang = barangs.find(b => String(b.id) === String(id));
    if (!barang) return;

    if (cart[id]) {
        if (cart[id].jumlah < barang.stok) {
            cart[id].jumlah += 1;
        } else {
            alert(`Stok ${barang.nama} sudah mencapai batas maksimum (${barang.stok} ${barang.satuan || 'Pcs'}).`);
            return;
        }
    } else {
        if (barang.stok <= 0) {
            alert(`Stok ${barang.nama} habis.`);
            return;
        }
        cart[id] = {
            id: barang.id,
            nama: barang.nama,
            kode_apotek: barang.kode_apotek,
            no_batch: barang.no_batch || (barang.kode_apotek ? 'B-' + barang.kode_apotek : 'B-001'),
            satuan: barang.satuan || 'Pcs',
            harga: barang.harga ?? 0,
            stok: barang.stok,
            jumlah: 1,
            diskon_custom_percent: barang.diskon_custom_percent ?? 0
        };
    }
    renderKeranjang();
}

daftarBarang?.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-tambah-obat');
    if (!btn) return;
    tambahKeKeranjang(btn.dataset.id);
});

// =========================================================================
// RENDER KERANJANG TRANSAKSI (Tabel Keranjang)
// =========================================================================
function renderKeranjang() {
    const ids = Object.keys(cart);
    const adaItem = ids.length > 0;

    if (keranjangKosong) {
        keranjangKosong.style.display = adaItem ? 'none' : 'block';
    }

    const diskonMemberPercent = (selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif)
        ? selectedPelanggan.diskon_percent
        : 0;

    let subtotalKotor = 0;
    let totalNominalDiskon = 0;

    keranjangItems.innerHTML = ids.map((id, index) => {
        const item = cart[id];
        const diskonCustomPercent = item.diskon_custom_percent || 0;
        const totalDiskonPercent = Math.min(50, diskonMemberPercent + diskonCustomPercent);
        const kotorItem = item.harga * item.jumlah;
        const nominalDiskonItem = totalDiskonPercent > 0 ? Math.round(kotorItem * (totalDiskonPercent / 100)) : 0;
        const subtotalItem = kotorItem - nominalDiskonItem;

        subtotalKotor += kotorItem;
        totalNominalDiskon += nominalDiskonItem;

        return `
            <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="py-2.5 pl-1 text-center font-medium text-slate-500">${index + 1}</td>
                <td class="py-2.5 px-2">
                    <div class="font-bold text-slate-800 text-xs">${item.nama}</div>
                    <div class="font-mono text-[10px] text-slate-400 mt-0.5">${item.no_batch} &nbsp;·&nbsp; Stok ${item.stok}</div>
                </td>
                <td class="py-2.5 px-2">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white shadow-2xs">
                        <span>${item.satuan || 'Pcs'}</span>
                        <svg class="w-3 h-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </td>
                <td class="py-2.5 px-2 font-semibold text-slate-800 whitespace-nowrap">
                    ${formatRupiah(item.harga)}
                </td>
                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                    <div class="inline-flex items-center border border-slate-200 rounded-lg p-0.5 bg-white shadow-2xs">
                        <button type="button" class="btn-qty-minus w-5 h-5 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded font-bold cursor-pointer transition-colors" data-id="${id}">&minus;</button>
                        <span class="w-7 text-center font-semibold text-xs text-slate-800">${item.jumlah}</span>
                        <button type="button" class="btn-qty-plus w-5 h-5 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded font-bold cursor-pointer transition-colors" data-id="${id}">&plus;</button>
                    </div>
                </td>
                <td class="py-2.5 px-2 font-semibold text-slate-800 text-right whitespace-nowrap">
                    ${formatRupiah(subtotalItem)}
                </td>
                <td class="py-2.5 pr-1 text-center">
                    <button type="button" class="btn-hapus-cart p-1.5 text-red-500 hover:text-red-600 hover:bg-red-50 rounded-lg border border-red-100 cursor-pointer transition-colors" data-id="${id}" title="Hapus dari keranjang">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <input type="hidden" name="items[${id}][barang_id]" value="${id}">
                    <input type="hidden" name="items[${id}][jumlah]" value="${item.jumlah}">
                </td>
            </tr>
        `;
    }).join('');

    const grandTotal = subtotalKotor - totalNominalDiskon;
    currentTotal = grandTotal;

    if (displaySubtotal) displaySubtotal.textContent = formatRupiah(subtotalKotor);
    if (displayDiskon) displayDiskon.textContent = formatRupiah(totalNominalDiskon);
    if (totalDisplay) totalDisplay.textContent = formatRupiah(grandTotal);

    updateMetodePembayaran();
}

// Keranjang Quantity & Hapus Event Delegation
keranjangItems?.addEventListener('click', (e) => {
    const btnMinus = e.target.closest('.btn-qty-minus');
    if (btnMinus) {
        const id = btnMinus.dataset.id;
        if (cart[id]) {
            if (cart[id].jumlah > 1) {
                cart[id].jumlah -= 1;
            } else {
                delete cart[id];
            }
            renderKeranjang();
        }
        return;
    }

    const btnPlus = e.target.closest('.btn-qty-plus');
    if (btnPlus) {
        const id = btnPlus.dataset.id;
        if (cart[id]) {
            if (cart[id].jumlah < cart[id].stok) {
                cart[id].jumlah += 1;
                renderKeranjang();
            } else {
                alert(`Jumlah tidak boleh melebihi stok tersedia (${cart[id].stok}).`);
            }
        }
        return;
    }

    const btnHapus = e.target.closest('.btn-hapus-cart');
    if (btnHapus) {
        const id = btnHapus.dataset.id;
        delete cart[id];
        renderKeranjang();
    }
});

// Hapus Semua Button
btnHapusSemua?.addEventListener('click', () => {
    if (Object.keys(cart).length === 0) return;
    if (confirm('Kosongkan semua barang di keranjang?')) {
        cart = {};
        renderKeranjang();
    }
});

// =========================================================================
// METODE PEMBAYARAN & KEMBALIAN
// =========================================================================
function pelangganMemberAktif() {
    return !!(selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif);
}

function updateMetodePembayaran() {
    const metode = metodePembayaran?.value || 'cash';
    const adaKeranjang = Object.keys(cart).length > 0;
    const memberAktif = pelangganMemberAktif();

    const opsiPiutang = metodePembayaran?.querySelector('option[value="piutang"]');
    if (opsiPiutang) opsiPiutang.disabled = !memberAktif;

    if (metode === 'piutang' && !memberAktif) {
        if (metodePembayaran) metodePembayaran.value = 'cash';
        updateMetodePembayaran();
        return;
    }

    if (bagianPembayaranCash) bagianPembayaranCash.classList.toggle('hidden', metode !== 'cash');
    if (bagianPembayaranQris) bagianPembayaranQris.classList.toggle('hidden', metode !== 'qris');
    if (bagianPembayaranDebit) bagianPembayaranDebit.classList.toggle('hidden', metode !== 'debit');
    if (bagianJatuhTempo) bagianJatuhTempo.classList.toggle('hidden', metode !== 'piutang');
    if (infoPembayaranPiutang) infoPembayaranPiutang.classList.toggle('hidden', metode !== 'piutang');
    if (barisKembalian) barisKembalian.classList.toggle('hidden', metode !== 'cash');

    if (metode !== 'piutang' && inputJatuhTempo) inputJatuhTempo.value = '';
    if (metode !== 'qris' && qrisLunas) qrisLunas.checked = false;
    if (metode !== 'debit' && debitLunas) debitLunas.checked = false;

    if (metode === 'cash') {
        hitungKembalian();
    } else if (metode === 'piutang') {
        if (btnProsesTransaksi) btnProsesTransaksi.disabled = !adaKeranjang || !memberAktif;
    } else {
        if (btnProsesTransaksi) btnProsesTransaksi.disabled = !adaKeranjang;
    }
}

function hitungKembalian() {
    if (!metodePembayaran || metodePembayaran.value !== 'cash') return;

    const valStr = inputBayar ? inputBayar.value.trim() : '';
    if (!valStr) {
        if (displayKembalian) displayKembalian.textContent = 'Rp 0';
        if (displayKembalianBox) {
            displayKembalianBox.className = 'w-full px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-500';
        }
        if (labelKembalian) labelKembalian.textContent = 'Kembalian';
        if (btnProsesTransaksi) btnProsesTransaksi.disabled = true;
        return;
    }

    const bayar = parseFloat(valStr) || 0;
    const selisih = bayar - currentTotal;

    if (selisih < 0) {
        if (displayKembalian) displayKembalian.textContent = 'Kurang: - ' + formatRupiah(Math.abs(selisih));
        if (displayKembalianBox) {
            displayKembalianBox.className = 'w-full px-3 py-1.5 text-xs font-semibold rounded-xl border border-red-200 bg-red-50 text-red-600';
        }
        if (labelKembalian) labelKembalian.textContent = 'Kurang Pembayaran';
        if (btnProsesTransaksi) btnProsesTransaksi.disabled = true;
    } else {
        if (displayKembalian) displayKembalian.textContent = formatRupiah(selisih);
        if (displayKembalianBox) {
            displayKembalianBox.className = 'w-full px-3 py-1.5 text-xs font-semibold rounded-xl border border-emerald-200 bg-emerald-50/60 text-emerald-600';
        }
        if (labelKembalian) labelKembalian.textContent = 'Kembalian';
        if (btnProsesTransaksi) btnProsesTransaksi.disabled = Object.keys(cart).length === 0;
    }
}

inputBayar?.addEventListener('input', hitungKembalian);
metodePembayaran?.addEventListener('change', updateMetodePembayaran);

// =========================================================================
// MEMBER SELECTION & MODAL LOGIC
// =========================================================================
function renderMemberQuickList(filter = '') {
    if (!memberQuickList) return;
    const q = filter.trim().toLowerCase();
    const hasil = q
        ? pelanggans.filter(p =>
            p.nama.toLowerCase().includes(q) ||
            (p.member_id && p.member_id.toLowerCase().includes(q)) ||
            (p.telepon && p.telepon.toLowerCase().includes(q))
        )
        : pelanggans;

    if (hasil.length === 0) {
        memberQuickList.innerHTML = '<p class="text-xs text-slate-400 py-4 text-center">Member tidak ditemukan.</p>';
        return;
    }

    memberQuickList.innerHTML = hasil.map(p => `
        <div class="py-2.5 flex items-center justify-between gap-2 hover:bg-slate-50 px-2 rounded-xl transition-colors">
            <div class="min-w-0">
                <div class="font-bold text-xs text-slate-800 truncate">${p.nama}</div>
                <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                    <span class="font-mono text-blue-600 font-semibold">${p.member_id || 'NON-MEMBER'}</span>
                    ${p.telepon ? `<span>·</span><span>${p.telepon}</span>` : ''}
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${p.is_member ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'}">
                    Diskon ${p.diskon_percent}%
                </span>
                <button type="button" data-id="${p.id}" class="btn-pilih-member-item px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                    Pilih
                </button>
            </div>
        </div>
    `).join('');
}

btnPilihMember?.addEventListener('click', () => {
    if (modalPilihMember) {
        modalPilihMember.classList.remove('hidden');
        if (pelangganSearchInput) {
            pelangganSearchInput.value = '';
            pelangganSearchInput.focus();
        }
        renderMemberQuickList('');
    }
});

btnCloseModalPilihMember?.addEventListener('click', () => {
    if (modalPilihMember) modalPilihMember.classList.add('hidden');
});

pelangganSearchInput?.addEventListener('input', () => {
    renderMemberQuickList(pelangganSearchInput.value);
});

memberQuickList?.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-pilih-member-item');
    if (!btn) return;
    const id = parseInt(btn.dataset.id);
    const p = pelanggans.find(item => item.id === id);
    if (p) selectPelanggan(p);
    if (modalPilihMember) modalPilihMember.classList.add('hidden');
});

function selectPelanggan(pelanggan) {
    selectedPelanggan = pelanggan;
    if (selectedPelangganIdInput) selectedPelangganIdInput.value = pelanggan.id;
    if (pelangganNamaInput) pelangganNamaInput.value = pelanggan.nama || '';
    if (pelangganTeleponInput) pelangganTeleponInput.value = pelanggan.telepon || '';

    if (selectedPelangganNama) selectedPelangganNama.textContent = pelanggan.nama;
    if (selectedPelangganMemberId) selectedPelangganMemberId.textContent = pelanggan.member_id ? `(${pelanggan.member_id})` : '';

    if (memberInfoDisplay) {
        if (pelanggan.is_member) {
            memberInfoDisplay.innerHTML = `<span class="font-bold text-emerald-900">${pelanggan.nama}</span> <span class="font-mono text-[11px] text-emerald-700">(${pelanggan.member_id || '-'})</span> &nbsp;·&nbsp; Diskon Member: <span class="font-bold">${pelanggan.diskon_percent}%</span>`;
        } else {
            memberInfoDisplay.innerHTML = `<span class="font-bold text-slate-800">${pelanggan.nama}</span> &nbsp;·&nbsp; <span class="text-slate-500">Bukan Member</span>`;
        }
    }

    if (badgeDiskonMember) {
        badgeDiskonMember.classList.remove('hidden');
        if (labelDiskonPercent) labelDiskonPercent.textContent = pelanggan.diskon_percent;
    }

    if (btnResetPelanggan) btnResetPelanggan.classList.remove('hidden');
    renderKeranjang();
}

btnResetPelanggan?.addEventListener('click', () => {
    selectedPelanggan = null;
    if (selectedPelangganIdInput) selectedPelangganIdInput.value = '';
    if (pelangganNamaInput) pelangganNamaInput.value = '';
    if (pelangganTeleponInput) pelangganTeleponInput.value = '';
    if (pelangganSearchInput) pelangganSearchInput.value = '';

    if (selectedPelangganNama) selectedPelangganNama.textContent = 'Umum';
    if (selectedPelangganMemberId) selectedPelangganMemberId.textContent = '';
    if (badgeDiskonMember) badgeDiskonMember.classList.add('hidden');
    if (labelDiskonPercent) labelDiskonPercent.textContent = '0';

    if (memberInfoDisplay) {
        memberInfoDisplay.textContent = 'Pilih Member untuk mendapatkan diskon';
    }
    if (btnResetPelanggan) btnResetPelanggan.classList.add('hidden');
    renderKeranjang();
});

// =========================================================================
// TAMBAH MEMBER BARU MODAL
// =========================================================================
function updateMemberStatusColor() {
    if (!memberStatusInput) return;
    if (!memberStatusInput.value) {
        memberStatusInput.classList.add('text-slate-400');
        memberStatusInput.classList.remove('text-slate-800');
    } else {
        memberStatusInput.classList.remove('text-slate-400');
        memberStatusInput.classList.add('text-slate-800');
    }
}

memberStatusInput?.addEventListener('change', updateMemberStatusColor);

btnTambahMember?.addEventListener('click', () => {
    if (memberError) {
        memberError.classList.add('hidden');
        memberError.textContent = '';
    }
    if (memberNamaInput) memberNamaInput.value = '';
    if (memberTeleponInput) memberTeleponInput.value = '';
    if (memberStatusInput) {
        memberStatusInput.value = '';
        updateMemberStatusColor();
    }
    if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
    if (modalPilihMember) modalPilihMember.classList.add('hidden');
    if (modalDaftarMember) modalDaftarMember.classList.remove('hidden');
    memberNamaInput?.focus();
});

[btnCancelMember, btnCloseMemberX].forEach(btn => {
    btn?.addEventListener('click', () => {
        if (modalDaftarMember) modalDaftarMember.classList.add('hidden');
    });
});

formDaftarMember?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!memberError) return;

    memberError.classList.add('hidden');
    memberError.textContent = '';

    const nama = memberNamaInput.value.trim();
    const telepon = memberTeleponInput.value.trim();
    const statusMember = memberStatusInput.value;

    if (!nama) {
        memberError.classList.remove('hidden');
        memberError.textContent = 'Nama member wajib diisi.';
        memberNamaInput?.focus();
        return;
    }

    if (!telepon) {
        memberError.classList.remove('hidden');
        memberError.textContent = 'Nomor HP wajib diisi.';
        memberTeleponInput?.focus();
        return;
    }

    if (!statusMember) {
        memberError.classList.remove('hidden');
        memberError.textContent = 'Silakan pilih jenis member.';
        memberStatusInput?.focus();
        return;
    }

    const customDiscountRaw = memberCustomDiscountInput ? memberCustomDiscountInput.value.trim() : '';
    let customDiscountValue = null;
    if (customDiscountRaw !== '') {
        const parsed = parseFloat(customDiscountRaw);
        if (isNaN(parsed) || parsed < 0 || parsed > 100) {
            memberError.classList.remove('hidden');
            memberError.textContent = 'Custom Diskon harus berupa angka antara 0 dan 100.';
            return;
        }
        customDiscountValue = parsed;
    }

    const payload = {
        nama: nama,
        telepon: telepon,
        status_member: statusMember,
    };

    if (customDiscountValue !== null) {
        payload.custom_discount_percentage = customDiscountValue;
    }

    fetch('/pelanggan/register-member', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify(payload),
    }).then(async r => {
        const data = await r.json();
        if (!r.ok) {
            const firstErr = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstErr || data.message || 'Terjadi kesalahan saat mendaftarkan member.');
        }
        return data;
    }).then(response => {
        if (response.success) {
            const memberObj = response.member;
            const idx = pelanggans.findIndex(p => p.id === memberObj.id);
            if (idx !== -1) pelanggans[idx] = memberObj;
            else pelanggans.push(memberObj);
            selectPelanggan(memberObj);
            modalDaftarMember.classList.add('hidden');
        } else {
            memberError.classList.remove('hidden');
            memberError.textContent = response.message || 'Gagal mendaftarkan member.';
        }
    }).catch(error => {
        memberError.classList.remove('hidden');
        memberError.textContent = error.message || 'Terjadi kesalahan saat mendaftarkan member.';
    });
});

// =========================================================================
// SUBMIT FORM TRANSAKSI
// =========================================================================
form?.addEventListener('submit', (e) => {
    if (Object.keys(cart).length === 0) {
        e.preventDefault();
        alert('Keranjang transaksi masih kosong. Silakan pilih obat terlebih dahulu.');
        return;
    }

    const jenisAktif = getJenisTransaksiAktif();

    if (jenisAktif === 'resep') {
        if (!inputNamaDokter || !inputNamaDokter.value.trim()) {
            e.preventDefault();
            alert('Nama Dokter wajib diisi untuk transaksi Resep.');
            if (inputNamaDokter) inputNamaDokter.focus();
            return;
        }
        if (!inputIdDokter || !inputIdDokter.value.trim()) {
            e.preventDefault();
            alert('ID Dokter wajib diisi untuk transaksi Resep.');
            if (inputIdDokter) inputIdDokter.focus();
            return;
        }
        if (!inputAlamatLembaga || !inputAlamatLembaga.value.trim()) {
            e.preventDefault();
            alert('Alamat Lembaga / Klinik wajib diisi untuk transaksi Resep.');
            if (inputAlamatLembaga) inputAlamatLembaga.focus();
            return;
        }
    } else {
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }

    const metode = metodePembayaran ? metodePembayaran.value : 'cash';

    if (metode === 'cash') {
        const valStr = inputBayar ? inputBayar.value.trim() : '';
        if (!valStr) {
            e.preventDefault();
            alert('Masukkan nominal pembayaran uang tunai.');
            if (inputBayar) inputBayar.focus();
            return;
        }
        const bayar = parseFloat(valStr) || 0;
        if (bayar < currentTotal) {
            e.preventDefault();
            alert('Pembayaran masih kurang dari total tagihan.');
            if (inputBayar) inputBayar.focus();
            return;
        }
    }

    if (metode === 'piutang') {
        if (!pelangganMemberAktif()) {
            e.preventDefault();
            alert('Metode pembayaran Piutang hanya dapat digunakan oleh Member aktif.');
            return;
        }
        if (inputJatuhTempo && inputJatuhTempo.value) {
            const inputTgl = form.querySelector('input[name="tanggal"]')?.value;
            if (inputTgl && inputJatuhTempo.value < inputTgl) {
                e.preventDefault();
                alert('Tanggal jatuh tempo tidak boleh lebih awal dari tanggal transaksi.');
                return;
            }
        }
    }

    if (btnProsesTransaksi) {
        btnProsesTransaksi.disabled = true;
        btnProsesTransaksi.innerHTML = '<span>Memproses Transaksi...</span>';
    }
});

// =========================================================================
// SHORTCUTS (F2, F4, F8, Escape)
// =========================================================================
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') {
        e.preventDefault();
        switchTab('cari');
        cariInput?.focus();
        cariInput?.select();
    } else if (e.key === 'F4') {
        e.preventDefault();
        btnPilihMember?.click();
    } else if (e.key === 'F8') {
        e.preventDefault();
        if (metodePembayaran) metodePembayaran.value = 'cash';
        updateMetodePembayaran();
        inputBayar?.focus();
        inputBayar?.select();
    } else if (e.key === 'Escape') {
        if (userProfileDropdownMenu && !userProfileDropdownMenu.classList.contains('hidden')) {
            userProfileDropdownMenu.classList.add('hidden');
            btnUserProfileToggle?.setAttribute('aria-expanded', 'false');
            profileChevronIcon?.classList.remove('rotate-180');
        }
        if (modalPilihMember && !modalPilihMember.classList.contains('hidden')) {
            modalPilihMember.classList.add('hidden');
        }
        if (modalDaftarMember && !modalDaftarMember.classList.contains('hidden')) {
            modalDaftarMember.classList.add('hidden');
        }
    }
});

// Initial Setup
function initPenjualanCreate() {
    const initialJenis = hiddenJenisTransaksi?.value || 'non_resep';
    sinkronkanJenisTransaksi(initialJenis);
    renderDaftarBarang();
    renderKeranjang();
    updateMemberStatusColor();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPenjualanCreate);
} else {
    initPenjualanCreate();
}