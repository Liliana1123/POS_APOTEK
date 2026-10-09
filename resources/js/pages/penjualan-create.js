// resources/js/pages/penjualan-create.js
// Page-specific JS extracted from penjualan/create.blade.php

const pageData = JSON.parse(document.getElementById('penjualan-data')?.textContent || '{}');
const pelanggans = pageData.pelanggans || [];
const barangs = pageData.barangs || [];

const cariInput = document.getElementById('cari-barang');
const daftarBarang = document.getElementById('daftar-barang');
const keranjangItems = document.getElementById('keranjang-items');
const keranjangKosong = document.getElementById('keranjang-kosong');
const totalDisplay = document.getElementById('total-display');
const form = document.getElementById('form-penjualan');

// Jenis Transaksi Elements
const radioNonResepKiri = document.getElementById('jenis-non-resep-kiri');
const radioResepKiri = document.getElementById('jenis-resep-kiri');
const radioNonResepKanan = document.getElementById('jenis-non-resep-kanan');
const radioResepKanan = document.getElementById('jenis-resep-kanan');
const hiddenJenisTransaksi = document.getElementById('jenis-transaksi-value');
const formDataResep = document.getElementById('form-data-resep');
const inputNamaDokter = document.getElementById('nama-dokter');
const inputIdDokter = document.getElementById('id-dokter');
const inputAlamatLembaga = document.getElementById('alamat-lembaga');

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

    if (formDataResep) formDataResep.classList.toggle('hidden', !isResep);

    if (inputNamaDokter) inputNamaDokter.required = isResep;
    if (inputIdDokter) inputIdDokter.required = isResep;
    if (inputAlamatLembaga) inputAlamatLembaga.required = isResep;

    if (!isResep) {
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }

    renderDaftarBarang(cariInput ? cariInput.value : '');
}

[radioNonResepKiri, radioResepKiri, radioNonResepKanan, radioResepKanan].forEach(radio => {
    if (radio) {
        radio.addEventListener('change', () => {
            if (radio.checked) sinkronkanJenisTransaksi(radio.value);
        });
    }
});

// Pelanggan elements
const pelangganSearchInput = document.getElementById('pencarian-pelanggan');
const pelangganSearchHasil = document.getElementById('hasil-pencarian-pelanggan');
const selectedPelangganIdInput = document.getElementById('selected-pelanggan-id');
const pelangganNamaInput = document.getElementById('pelanggan-nama');
const pelangganTeleponInput = document.getElementById('pelanggan-telepon');
const selectedPelangganNama = document.getElementById('selected-pelanggan-nama');
const selectedPelangganMemberId = document.getElementById('selected-pelanggan-member-id');
const btnResetPelanggan = document.getElementById('btn-reset-pelanggan');
const badgeDiskonMember = document.getElementById('badge-diskon-member');
const labelDiskonPercent = document.getElementById('label-diskon-percent');

// Modal elements
const modalDaftarMember = document.getElementById('modal-daftar-member');
const btnTambahMember = document.getElementById('btn-tambah-member');
const btnCancelMember = document.getElementById('btn-cancel-member');
const formDaftarMember = document.getElementById('form-daftar-member');
const memberNamaInput = document.getElementById('member-nama');
const memberTeleponInput = document.getElementById('member-telepon');
const memberStatusInput = document.getElementById('member-status');
const memberError = document.getElementById('member-error');

let cart = {};
let selectedPelanggan = null;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

const infoJumlahBarang = document.getElementById('info-jumlah-barang');

function renderDaftarBarang(filter = '') {
    const query = (typeof filter === 'string' ? filter : (cariInput ? cariInput.value : '')).trim().toLowerCase();

    const hasil = query
        ? barangs.filter(b => {
            const cariNama = b.nama.toLowerCase().includes(query);
            const cariKode = b.kode_apotek && b.kode_apotek.toLowerCase().includes(query);
            return cariNama || cariKode;
          })
        : barangs;

    if (infoJumlahBarang) {
        if (query) {
            infoJumlahBarang.textContent = hasil.length > 0 ? `${hasil.length} barang ditemukan` : '';
        } else {
            infoJumlahBarang.textContent = `${barangs.length} barang tersedia`;
        }
    }

    daftarBarang.innerHTML = hasil.map(b => `
        <button type="button" data-id="${b.id}"
            class="btn-pilih-barang w-full text-left border border-gray-150 rounded-lg px-2.5 py-2 text-[11px] hover:bg-blue-50 flex justify-between items-center transition-colors gap-2">
            <span class="min-w-0 flex-1">
                <span class="block font-medium text-gray-800 truncate">${b.nama}</span>
                ${b.kode_apotek ? `<span class="font-mono text-[9px] text-slate-400">${b.kode_apotek}</span>` : ''}
                ${b.butuh_resep ? '<span class="text-[9px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Resep</span>' : ''}
                ${b.diskon_custom_percent > 0 ? `<span class="text-[9px] bg-red-50 text-red-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Promo ${b.diskon_custom_percent}%</span>` : ''}
            </span>
            <span class="text-gray-500 font-medium shrink-0 text-right">
                <span class="block">Stok <strong class="text-gray-700">${b.stok}</strong></span>
                <strong class="text-blue-700 text-[10px]">${formatRupiah(b.harga ?? 0)}</strong>
            </span>
        </button>
    `).join('') || '<p class="text-xs text-gray-400 py-4 text-center">Barang tidak ditemukan.</p>';
}

// Init
const initialJenis = hiddenJenisTransaksi?.value || 'non_resep';
sinkronkanJenisTransaksi(initialJenis);

function renderKeranjang() {
    const ids = Object.keys(cart);
    keranjangKosong.style.display = ids.length === 0 ? 'block' : 'none';

    const diskonMemberPercent = (selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif) ? selectedPelanggan.diskon_percent : 0;

    keranjangItems.innerHTML = ids.map(id => {
        const item = cart[id];
        const diskonCustomPercent = item.diskon_custom_percent || 0;
        const totalDiskonPercent = Math.min(50, diskonMemberPercent + diskonCustomPercent);
        const nominalDiskon = totalDiskonPercent > 0 ? Math.round((item.harga * item.jumlah) * (totalDiskonPercent / 100)) : 0;
        const subtotal = (item.harga * item.jumlah) - nominalDiskon;
        return `
            <div class="border border-gray-150 rounded-lg p-3 text-xs bg-gray-50">
                <div class="flex justify-between items-start mb-1.5">
                    <span class="font-semibold text-gray-800">${item.nama}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 font-bold text-sm btn-hapus-cart" data-id="${id}">&times;</button>
                </div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-gray-500">Qty:</span>
                    <input type="number" min="1" max="${item.stok}" value="${item.jumlah}"
                        class="input-jumlah form-input !w-16 px-2 py-0.5 text-xs text-right" data-id="${id}">
                    <span class="text-gray-400 text-[10px] font-mono">(${formatRupiah(item.harga)}/item)</span>
                </div>
                ${totalDiskonPercent > 0 ? `
                    <div class="text-[10px] space-y-0.5 bg-white border border-gray-100 rounded-lg p-2 mt-1">
                        ${diskonMemberPercent > 0 ? `<div class="text-gray-500">Diskon Member: <span class="font-bold text-gray-700">${diskonMemberPercent}%</span></div>` : ''}
                        ${diskonCustomPercent > 0 ? `<div class="text-gray-500">Diskon Promo: <span class="font-bold text-gray-700">${diskonCustomPercent}%</span></div>` : ''}
                        <div class="font-bold text-green-600">Diterapkan: ${totalDiskonPercent}% (-${formatRupiah(nominalDiskon)})</div>
                    </div>
                    <div class="text-right text-gray-800 mt-2 font-bold text-xs">${formatRupiah(subtotal)}</div>
                    <input type="hidden" name="items[${id}][barang_id]" value="${id}">
                    <input type="hidden" name="items[${id}][jumlah]" value="${item.jumlah}">
                ` : ''}
            </div>
        `;
    }).join('');

    const total = ids.reduce((sum, id) => {
        const item = cart[id];
        const diskonCustomPercent = item.diskon_custom_percent || 0;
        const totalDiskonPercent = Math.min(50, diskonMemberPercent + diskonCustomPercent);
        const nominalDiskon = totalDiskonPercent > 0 ? Math.round((item.harga * item.jumlah) * (totalDiskonPercent / 100)) : 0;
        return sum + (item.harga * item.jumlah) - nominalDiskon;
    }, 0);
    totalDisplay.textContent = formatRupiah(total);
    currentTotal = total;
    updateMetodePembayaran();
}

// Payment and Change Calculator
let currentTotal = 0;
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
const labelKembalian = document.getElementById('label-kembalian');
const submitButton = form.querySelector('button[type="submit"]');
const opsiPiutang = metodePembayaran.querySelector('option[value="piutang"]');

function pelangganMemberAktif() {
    return !!(selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif);
}

function updateMetodePembayaran() {
    const metode = metodePembayaran.value;
    const adaKeranjang = Object.keys(cart).length > 0;
    const memberAktif = pelangganMemberAktif();

    if (opsiPiutang) opsiPiutang.disabled = !memberAktif;

    if (metode === 'piutang' && !memberAktif) {
        metodePembayaran.value = 'cash';
        updateMetodePembayaran();
        return;
    }

    bagianPembayaranCash.classList.toggle('hidden', metode !== 'cash');
    bagianPembayaranQris.classList.toggle('hidden', metode !== 'qris');
    bagianPembayaranDebit.classList.toggle('hidden', metode !== 'debit');
    bagianJatuhTempo.classList.toggle('hidden', metode !== 'piutang');
    infoPembayaranPiutang.classList.toggle('hidden', metode !== 'piutang');
    barisKembalian.classList.toggle('hidden', metode !== 'cash');

    if (metode !== 'piutang' && inputJatuhTempo) inputJatuhTempo.value = '';
    if (metode !== 'qris' && qrisLunas) qrisLunas.checked = false;
    if (metode !== 'debit' && debitLunas) debitLunas.checked = false;

    if (metode === 'cash') hitungKembalian();
    else if (metode === 'piutang') submitButton.disabled = !adaKeranjang || !memberAktif;
    else submitButton.disabled = !adaKeranjang;
}

function hitungKembalian() {
    if (metodePembayaran.value !== 'cash') return;

    const valStr = inputBayar.value.trim();
    if (!valStr) {
        displayKembalian.textContent = 'Masukkan jumlah pembayaran';
        displayKembalian.className = 'text-xs text-gray-400';
        labelKembalian.textContent = 'Kembalian:';
        submitButton.disabled = true;
        return;
    }

    const bayar = parseFloat(valStr) || 0;
    const selisih = bayar - currentTotal;

    if (selisih < 0) {
        displayKembalian.textContent = '- ' + formatRupiah(Math.abs(selisih));
        displayKembalian.className = 'text-xs font-bold text-red-600';
        labelKembalian.textContent = 'Kurang:';
        submitButton.disabled = true;
    } else if (selisih === 0) {
        displayKembalian.textContent = formatRupiah(0);
        displayKembalian.className = 'text-xs font-bold text-green-600';
        labelKembalian.textContent = 'Pas:';
        submitButton.disabled = Object.keys(cart).length === 0;
    } else {
        displayKembalian.textContent = formatRupiah(selisih);
        displayKembalian.className = 'text-xs font-bold text-blue-600';
        labelKembalian.textContent = 'Kembalian:';
        submitButton.disabled = Object.keys(cart).length === 0;
    }
}

inputBayar.addEventListener('input', hitungKembalian);
metodePembayaran.addEventListener('change', updateMetodePembayaran);

// Pelanggan Search Logic
pelangganSearchInput.addEventListener('input', () => {
    const nilaiAsli = pelangganSearchInput.value.trim();
    const v = nilaiAsli.toLowerCase();

    if (selectedPelangganIdInput.value === '') {
        pelangganNamaInput.value = nilaiAsli;
        pelangganTeleponInput.value = '';
    }
    if (!v) {
        pelangganSearchHasil.innerHTML = '';
        pelangganSearchHasil.classList.add('hidden');
        return;
    }

    const hasil = pelanggans.filter(p =>
        p.nama.toLowerCase().includes(v) ||
        (p.member_id && p.member_id.toLowerCase().includes(v)) ||
        (p.telepon && p.telepon.toLowerCase().includes(v))
    );

    if (hasil.length > 0) {
        pelangganSearchHasil.innerHTML = hasil.map(p => `
           <button type="button" data-id="${p.id}" class="btn-select-pelanggan w-full text-left px-3.5 py-2.5 text-xs hover:bg-blue-50 border-b border-gray-150 last:border-0 flex justify-between items-center transition-colors">
                <div>
                    <strong class="text-gray-800 font-medium">${p.nama}</strong>
                    ${p.telepon ? `<span class="text-gray-500 block text-[10px] font-mono mt-0.5">Telp: ${p.telepon}</span>` : ''}
                </div>
                <div>
                    ${p.is_member ? `<span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-mono text-[9px] font-bold">${p.member_id ?? '-'} · MEMBER</span>` : `<span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-mono text-[9px] font-bold">BELUM MEMBER</span>`}
                </div>
            </button>
        `).join('');
        pelangganSearchHasil.classList.remove('hidden');
    } else {
        pelangganSearchHasil.innerHTML = '<p class="text-xs text-gray-400 p-3 text-center">Member tidak ditemukan.</p>';
        pelangganSearchHasil.classList.remove('hidden');
    }
});

pelangganSearchHasil.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-select-pelanggan');
    if (!btn) return;
    const id = parseInt(btn.dataset.id);
    const pelanggan = pelanggans.find(p => p.id === id);
    if (pelanggan) selectPelanggan(pelanggan);
    pelangganSearchHasil.innerHTML = '';
    pelangganSearchHasil.classList.add('hidden');
    pelangganSearchInput.value = '';
});

document.addEventListener('click', (e) => {
    if (!pelangganSearchInput.contains(e.target) && !pelangganSearchHasil.contains(e.target)) {
        pelangganSearchHasil.classList.add('hidden');
    }
});

function selectPelanggan(pelanggan) {
    selectedPelanggan = pelanggan;
    selectedPelangganIdInput.value = pelanggan.id;
    pelangganNamaInput.value = pelanggan.nama || '';
    pelangganTeleponInput.value = pelanggan.telepon || '';

    if (pelanggan.is_member) {
        selectedPelangganMemberId.textContent = `(${pelanggan.member_id})`;
        badgeDiskonMember.classList.remove('hidden');
        labelDiskonPercent.textContent = pelanggan.diskon_percent;
        if (!pelanggan.member_aktif) {
            badgeDiskonMember.className = 'mt-2 badge-neutral inline-block';
            badgeDiskonMember.firstChild.textContent = 'Member TIDAK AKTIF - Diskon: ';
        } else {
            badgeDiskonMember.className = 'mt-2 badge-success inline-block';
            badgeDiskonMember.firstChild.textContent = 'Diskon Member Aktif: ';
        }
    } else {
        selectedPelangganMemberId.textContent = '';
        badgeDiskonMember.classList.add('hidden');
        labelDiskonPercent.textContent = '0';
    }

    btnResetPelanggan.classList.remove('hidden');
    renderKeranjang();
}

btnResetPelanggan.addEventListener('click', () => {
    selectedPelanggan = null;
    selectedPelangganIdInput.value = '';
    pelangganNamaInput.value = '';
    pelangganTeleponInput.value = '';
    pelangganSearchInput.value = '';
    selectedPelangganNama.textContent = 'Umum';
    selectedPelangganMemberId.textContent = '';
    badgeDiskonMember.classList.add('hidden');
    labelDiskonPercent.textContent = '0';
    btnResetPelanggan.classList.add('hidden');
    renderKeranjang();
});

// Modal Logic
btnTambahMember.addEventListener('click', () => {
    memberError.classList.add('hidden');
    memberError.textContent = '';
    const searchText = pelangganSearchInput.value.trim();
    if (searchText && isNaN(searchText) && !searchText.startsWith('MBR-')) {
        memberNamaInput.value = searchText;
    } else {
        memberNamaInput.value = '';
    }
    memberTeleponInput.value = '';
    memberStatusInput.value = '';
    if (document.getElementById('member-custom-discount')) document.getElementById('member-custom-discount').value = '';
    modalDaftarMember.classList.remove('hidden');
});

btnCancelMember.addEventListener('click', () => {
    modalDaftarMember.classList.add('hidden');
    if (document.getElementById('member-custom-discount')) document.getElementById('member-custom-discount').value = '';
});

const btnCloseMemberX = document.getElementById('btn-close-member-x');
if (btnCloseMemberX) {
    btnCloseMemberX.addEventListener('click', () => {
        modalDaftarMember.classList.add('hidden');
        if (document.getElementById('member-custom-discount')) document.getElementById('member-custom-discount').value = '';
    });
}

const memberCustomDiscountInput = document.getElementById('member-custom-discount');

formDaftarMember.addEventListener('submit', (e) => {
    e.preventDefault();
    memberError.classList.add('hidden');
    memberError.textContent = '';

    const nama = memberNamaInput.value.trim();
    const telepon = memberTeleponInput.value.trim();
    const statusMember = memberStatusInput.value;

    if (!statusMember) {
        memberError.classList.remove('hidden');
        memberError.textContent = 'Silakan pilih jenis member.';
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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
        body: JSON.stringify(payload),
    }).then(r => r.json()).then(response => {
        if (response.success) {
            const memberObj = response.member;
            const idx = pelanggans.findIndex(p => p.id === memberObj.id);
            if (idx !== -1) pelanggans[idx] = memberObj;
            else pelanggans.push(memberObj);
            selectPelanggan(memberObj);
            modalDaftarMember.classList.add('hidden');
            if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
        }
    }).catch(error => {
        memberError.classList.remove('hidden');
        if (error.response && error.response.data && error.response.data.message) {
            memberError.textContent = error.response.data.message;
        } else if (error.response && error.response.data && error.response.data.errors) {
            const firstError = Object.values(error.response.data.errors)[0];
            memberError.textContent = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
            memberError.textContent = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    });
});

cariInput.addEventListener('input', () => renderDaftarBarang(cariInput.value));

daftarBarang.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-pilih-barang');
    if (!btn) return;
    const id = btn.dataset.id;
    const barang = barangs.find(b => String(b.id) === id);
    if (cart[id]) cart[id].jumlah = cart[id].jumlah < barang.stok ? cart[id].jumlah + 1 : cart[id].jumlah;
    else cart[id] = { nama: barang.nama, harga: barang.harga ?? 0, stok: barang.stok, jumlah: 1, diskon_custom_percent: barang.diskon_custom_percent ?? 0 };
    renderKeranjang();
});

keranjangItems.addEventListener('input', (e) => {
    const id = e.target.dataset.id;
    if (!id || !cart[id]) return;
    if (e.target.classList.contains('input-jumlah')) {
        let v = parseInt(e.target.value) || 1;
        cart[id].jumlah = Math.min(Math.max(v, 1), cart[id].stok);
    }
    renderKeranjang();
});

keranjangItems.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-hapus-cart')) {
        delete cart[e.target.dataset.id];
        renderKeranjang();
    }
});

form.addEventListener('submit', (e) => {
    if (Object.keys(cart).length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong.');
        return;
    }

    const jenisAktif = getJenisTransaksiAktif();

    if (jenisAktif === 'resep') {
        if (!inputNamaDokter || !inputNamaDokter.value.trim()) {
            e.preventDefault(); alert('Nama Dokter wajib diisi untuk transaksi Resep.'); if (inputNamaDokter) inputNamaDokter.focus(); return;
        }
        if (!inputIdDokter || !inputIdDokter.value.trim()) {
            e.preventDefault(); alert('ID Dokter wajib diisi untuk transaksi Resep.'); if (inputIdDokter) inputIdDokter.focus(); return;
        }
        if (!inputAlamatLembaga || !inputAlamatLembaga.value.trim()) {
            e.preventDefault(); alert('Alamat Lembaga / Klinik wajib diisi untuk transaksi Resep.'); if (inputAlamatLembaga) inputAlamatLembaga.focus(); return;
        }
    } else {
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }

    const metode = metodePembayaran.value;

    if (metode === 'cash') {
        const valStr = inputBayar.value.trim();
        if (!valStr) {
            e.preventDefault(); alert('Masukkan nominal pembayaran terlebih dahulu.'); return;
        }
        const bayar = parseFloat(valStr) || 0;
        if (bayar < currentTotal) {
            e.preventDefault(); alert('Pembayaran masih kurang.'); return;
        }
    }

    if (metode === 'piutang') {
        if (!pelangganMemberAktif()) {
            e.preventDefault(); alert('Metode pembayaran Piutang hanya dapat digunakan oleh Member.'); return;
        }
        if (inputJatuhTempo && inputJatuhTempo.value) {
            const inputTgl = form.querySelector('input[name="tanggal"]')?.value;
            if (inputTgl && inputJatuhTempo.value < inputTgl) {
                e.preventDefault(); alert('Tanggal jatuh tempo tidak boleh lebih awal dari tanggal transaksi.'); return;
            }
        }
    }

    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.innerHTML = 'Menyimpan Transaksi...'; }
});

// Keyboard Shortcuts
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') {
        e.preventDefault(); const input = document.getElementById('cari-barang'); if (input) { input.focus(); input.select(); }
    } else if (e.key === 'F4') {
        e.preventDefault(); const input = document.getElementById('pencarian-pelanggan'); if (input) { input.focus(); input.select(); }
    } else if (e.key === 'F8') {
        e.preventDefault(); const input = document.getElementById('input-bayar'); if (input) { input.focus(); input.select(); }
    } else if (e.key === 'Escape') {
        if (modalDaftarMember && !modalDaftarMember.classList.contains('hidden')) modalDaftarMember.classList.add('hidden');
    }
});

function initPenjualanCreate() {
    renderDaftarBarang();
    renderKeranjang();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPenjualanCreate);
} else {
    initPenjualanCreate();
}