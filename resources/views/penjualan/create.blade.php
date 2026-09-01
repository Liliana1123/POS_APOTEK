@extends('layouts.app')
@section('title', 'Transaksi Baru')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Transaksi Penjualan Baru</h1>
    <p class="text-xs text-gray-500 mt-0.5">Catat penjualan obat kasir baru, terapkan diskon member dan FEFO batch otomatis.</p>
</div>

@if ($errors->any())
    <div class="bg-red-50 text-red-700 text-xs p-3 rounded-lg mb-4 border border-red-200">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom kiri: cari & pilih barang -->
    <div class="lg:col-span-2 card-base flex flex-col justify-between">
        <div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Pencarian Cepat Obat / Barang <span class="text-[10px] text-blue-600 font-bold ml-1 font-mono">[F2]</span></label>
                <input type="text" id="cari-barang" placeholder="Cari nama barang..."
                    class="form-input">
            </div>

            <div id="daftar-barang" class="space-y-1.5 max-h-[30rem] overflow-y-auto pr-1"></div>
        </div>
    </div>

    <!-- Kolom kanan: keranjang -->
    <div class="card-base">
        <div class="border-b pb-2 mb-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Keranjang Transaksi</h2>
        </div>

        <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">No. Faktur</label>
                <input type="text" name="no_faktur" value="{{ old('no_faktur', 'INV-' . now()->format('Ymd-His')) }}" required
                    class="form-input font-mono font-semibold">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tanggal Transaksi</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                    class="form-input">
            </div>

            <!-- Pencarian & Pemilihan Pelanggan/Member -->
            <div class="mb-4 relative">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Cari Pelanggan / Member <span class="text-[10px] text-blue-600 font-bold ml-1 font-mono">[F4]</span></label>
                <div class="flex gap-2">
                    <input type="text" id="pencarian-pelanggan" placeholder="Cari Member ID, Nama, atau HP..."
                        class="form-input">
                    <button type="button" id="btn-tambah-member" class="btn-secondary whitespace-nowrap">
                        + Member
                    </button>
                </div>
                <!-- Dropdown hasil pencarian pelanggan -->
                <div id="hasil-pencarian-pelanggan" class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto z-10 hidden"></div>
                <input type="hidden" name="pelanggan_id" id="selected-pelanggan-id" value="">
            </div>

            <!-- Info Pelanggan Terpilih -->
            <div id="info-pelanggan-terpilih" class="mb-4 bg-gray-50 border border-gray-150 rounded-lg p-3 text-xs">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-400">Pelanggan:</span>
                        <strong id="selected-pelanggan-nama" class="text-gray-800 ml-1">Umum</strong>
                        <span id="selected-pelanggan-member-id" class="font-mono text-blue-700 font-semibold ml-1"></span>
                    </div>
                    <button type="button" id="btn-reset-pelanggan" class="text-red-500 hover:text-red-700 font-bold hidden">&times; Batal</button>
                </div>
                <div id="badge-diskon-member" class="mt-2 badge-success inline-block hidden">
                    Diskon Member Aktif: <span id="label-diskon-percent" class="font-bold">0</span>%
                </div>
            </div>

            <div id="keranjang-items" class="space-y-2.5 mb-4 max-h-64 overflow-y-auto pr-1"></div>
            <p id="keranjang-kosong" class="text-xs text-gray-400 mb-4 text-center py-2">Keranjang masih kosong.</p>

            <div class="border-t pt-3 flex justify-between text-xs font-semibold mb-3 text-gray-700">
                <span>Total Tagihan:</span>
                <span id="total-display" class="text-sm font-bold text-gray-800">Rp 0</span>
            </div>

            <!-- Payment & Change Calculator Section -->
            <div class="border-t pt-3 space-y-3 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Bayar (Uang Tunai) <span class="text-[10px] text-blue-600 font-bold ml-1 font-mono">[F8]</span></label>
                    <input type="number" id="input-bayar" placeholder="Masukkan nominal pembayaran..."
                        class="form-input font-mono font-semibold text-right">
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span id="label-kembalian" class="font-semibold text-gray-500">Kembalian:</span>
                    <strong id="display-kembalian" class="text-xs text-gray-400">Masukkan jumlah pembayaran</strong>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-2.5 text-center uppercase tracking-wider">
                Simpan Transaksi
            </button>
        </form>
    </div>
</div>

<!-- Modal Daftar Member Baru (Phase A Restructured) -->
<div id="modal-daftar-member" class="modal-backdrop-custom hidden">
    <div class="modal-container-custom max-w-sm w-full mx-4">
        <div class="modal-header-custom">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Daftar Member Baru / Upgrade</h3>
            <button type="button" id="btn-close-member-x" class="text-gray-400 hover:text-gray-600 font-bold text-base" aria-label="Tutup modal">&times;</button>
        </div>
        
        <div id="member-error" class="bg-red-50 text-red-700 text-xs p-2 rounded-lg mb-3 hidden border border-red-200"></div>

        <form id="form-daftar-member" class="modal-body-custom space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Pelanggan</label>
                <input type="text" id="member-nama" required class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nomor HP</label>
                <input type="text" id="member-telepon" class="form-input">
            </div>
            <div class="modal-footer-custom">
                <button type="button" id="btn-cancel-member" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Daftar & Gunakan</button>
            </div>
        </form>
    </div>
</div>

<script>
let pelanggans = @json($pelanggans);
const barangs = @json($barangs);

const cariInput = document.getElementById('cari-barang');
const daftarBarang = document.getElementById('daftar-barang');
const keranjangItems = document.getElementById('keranjang-items');
const keranjangKosong = document.getElementById('keranjang-kosong');
const totalDisplay = document.getElementById('total-display');
const form = document.getElementById('form-penjualan');

// Pelanggan elements
const pelangganSearchInput = document.getElementById('pencarian-pelanggan');
const pelangganSearchHasil = document.getElementById('hasil-pencarian-pelanggan');
const selectedPelangganIdInput = document.getElementById('selected-pelanggan-id');
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
const memberError = document.getElementById('member-error');

let cart = {}; // { barang_id: { nama, harga, stok, jumlah } }
let selectedPelanggan = null;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

function renderDaftarBarang(filter = '') {
    const hasil = barangs.filter(b => b.nama.toLowerCase().includes(filter.toLowerCase()));
    daftarBarang.innerHTML = hasil.map(b => `
        <button type="button" data-id="${b.id}"
            class="btn-pilih-barang w-full text-left border border-gray-150 rounded-lg px-3.5 py-2.5 text-xs hover:bg-blue-50 flex justify-between items-center transition-colors">
            <span>
                ${b.nama}
                ${b.butuh_resep ? '<span class="text-[9px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Resep</span>' : ''}
                ${b.diskon_custom_percent > 0 ? `<span class="text-[9px] bg-red-50 text-red-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Promo ${b.diskon_custom_percent}%</span>` : ''}
            </span>
            <span class="text-gray-500 font-medium">Stok ${b.stok} &middot; <strong class="text-gray-700">${formatRupiah(b.harga ?? 0)}</strong></span>
        </button>
    `).join('') || '<p class="text-xs text-gray-400 py-4 text-center">Barang tidak ditemukan.</p>';
}

function renderKeranjang() {
    const ids = Object.keys(cart);
    keranjangKosong.style.display = ids.length === 0 ? 'block' : 'none';

    const diskonMemberPercent = (selectedPelanggan && selectedPelanggan.is_member) ? selectedPelanggan.diskon_percent : 0;

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
                ` : ''}
                <div class="text-right text-gray-800 mt-2 font-bold text-xs">${formatRupiah(subtotal)}</div>
                <input type="hidden" name="items[${id}][barang_id]" value="${id}">
                <input type="hidden" name="items[${id}][jumlah]" value="${item.jumlah}">
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
    hitungKembalian();
}

// Payment and Change Calculator JavaScript
let currentTotal = 0;
const inputBayar = document.getElementById('input-bayar');
const displayKembalian = document.getElementById('display-kembalian');
const labelKembalian = document.getElementById('label-kembalian');
const submitButton = form.querySelector('button[type="submit"]');

function hitungKembalian() {
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

// Pelanggan Search Logic
pelangganSearchInput.addEventListener('input', () => {
    const v = pelangganSearchInput.value.trim().toLowerCase();
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
                    ${p.is_member ? `<span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-mono text-[9px] font-bold">${p.member_id}</span>` : '<span class="text-gray-400 text-[10px]">Non-Member</span>'}
                </div>
            </button>
        `).join('');
        pelangganSearchHasil.classList.remove('hidden');
    } else {
        pelangganSearchHasil.innerHTML = '<p class="text-xs text-gray-400 p-3 text-center">Pelanggan tidak ditemukan.</p>';
        pelangganSearchHasil.classList.remove('hidden');
    }
});

// Click result pelanggan
pelangganSearchHasil.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-select-pelanggan');
    if (!btn) return;

    const id = parseInt(btn.dataset.id);
    const pelanggan = pelanggans.find(p => p.id === id);
    if (pelanggan) {
        selectPelanggan(pelanggan);
    }
    pelangganSearchHasil.innerHTML = '';
    pelangganSearchHasil.classList.add('hidden');
    pelangganSearchInput.value = '';
});

// Close dropdown if click outside
document.addEventListener('click', (e) => {
    if (!pelangganSearchInput.contains(e.target) && !pelangganSearchHasil.contains(e.target)) {
        pelangganSearchHasil.classList.add('hidden');
    }
});

function selectPelanggan(pelanggan) {
    selectedPelanggan = pelanggan;
    selectedPelangganIdInput.value = pelanggan.id;
    selectedPelangganNama.textContent = pelanggan.nama;
    
    if (pelanggan.is_member) {
        selectedPelangganMemberId.textContent = `(${pelanggan.member_id})`;
        badgeDiskonMember.classList.remove('hidden');
        labelDiskonPercent.textContent = pelanggan.diskon_percent;
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
    // prefill name if some custom search was typed and it's not a phone/member id
    const searchText = pelangganSearchInput.value.trim();
    if (searchText && isNaN(searchText) && !searchText.startsWith('MBR-')) {
        memberNamaInput.value = searchText;
    } else {
        memberNamaInput.value = '';
    }
    memberTeleponInput.value = '';
    modalDaftarMember.classList.remove('hidden');
});

btnCancelMember.addEventListener('click', () => {
    modalDaftarMember.classList.add('hidden');
});

const btnCloseMemberX = document.getElementById('btn-close-member-x');
if (btnCloseMemberX) {
    btnCloseMemberX.addEventListener('click', () => {
        modalDaftarMember.classList.add('hidden');
    });
}

formDaftarMember.addEventListener('submit', (e) => {
    e.preventDefault();
    memberError.classList.add('hidden');
    memberError.textContent = '';

    const nama = memberNamaInput.value.trim();
    const telepon = memberTeleponInput.value.trim();

    axios.post('{{ route("pelanggan.register-member") }}', {
        nama: nama,
        telepon: telepon
    })
    .then(response => {
        if (response.data.success) {
            const memberObj = response.data.member;
            
            // Check if member already exists in local list, if so update it, otherwise insert new
            const idx = pelanggans.findIndex(p => p.id === memberObj.id);
            if (idx !== -1) {
                pelanggans[idx] = memberObj;
            } else {
                pelanggans.push(memberObj);
            }

            selectPelanggan(memberObj);
            modalDaftarMember.classList.add('hidden');
        }
    })
    .catch(error => {
        memberError.classList.remove('hidden');
        if (error.response && error.response.data && error.response.data.message) {
            memberError.textContent = error.response.data.message;
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

    if (cart[id]) {
        if (cart[id].jumlah < barang.stok) cart[id].jumlah++;
    } else {
        cart[id] = { 
            nama: barang.nama, 
            harga: barang.harga ?? 0, 
            stok: barang.stok, 
            jumlah: 1,
            diskon_custom_percent: barang.diskon_custom_percent ?? 0
        };
    }
    renderKeranjang();
});

keranjangItems.addEventListener('input', (e) => {
    const id = e.target.dataset.id;
    if (!id) return;

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
    
    // Safety check for payment < total on frontend
    const valStr = inputBayar.value.trim();
    if (!valStr) {
        e.preventDefault();
        alert('Masukkan nominal pembayaran terlebih dahulu.');
        return;
    }
    const bayar = parseFloat(valStr) || 0;
    if (bayar < currentTotal) {
        e.preventDefault();
        alert('Pembayaran masih kurang.');
        return;
    }

    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan Transaksi...';
    }
});

// Keyboard Shortcuts Listener (F2, F4, F8, Escape)
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') {
        e.preventDefault();
        const input = document.getElementById('cari-barang');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'F4') {
        e.preventDefault();
        const input = document.getElementById('pencarian-pelanggan');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'F8') {
        e.preventDefault();
        const input = document.getElementById('input-bayar');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'Escape') {
        if (modalDaftarMember && !modalDaftarMember.classList.contains('hidden')) {
            modalDaftarMember.classList.add('hidden');
        }
    }
});

renderDaftarBarang();
renderKeranjang();
</script>
@endsection
