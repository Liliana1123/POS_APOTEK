<!-- ======================================================== -->
<!-- MODAL 1: PRATINJAU PENGINGAT PIUTANG INDIVIDUAL          -->
<!-- ======================================================== -->
<div id="whatsappPiutangModal"
    style="z-index: 10000;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/50 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#25D366] text-white shadow-sm shrink-0">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Pengingat Piutang WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400">
                        Pratinjau tagihan member sebelum konfirmasi pengiriman
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeWhatsAppPiutangPreview()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi modal -->
        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">

            <div id="whatsappPiutangLoading"
                class="hidden rounded-lg bg-gray-50 p-4 text-center text-sm text-gray-500">
                <div class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat tagihan...</span>
                </div>
            </div>

            <div id="whatsappPiutangSuccess"
                class="hidden rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800"
                role="alert"></div>

            <div id="whatsappPiutangError"
                class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"
                role="alert"></div>

            <div id="whatsappPiutangContent" class="hidden space-y-4">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 p-3 bg-gray-50/50">
                        <p class="text-xs text-gray-400 font-medium">Nama Member</p>
                        <p id="whatsappPiutangNama" class="mt-0.5 break-words font-bold text-gray-800">-</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-3 bg-gray-50/50">
                        <p class="text-xs text-gray-400 font-medium">Nomor WhatsApp</p>
                        <p id="whatsappPiutangTelepon" class="mt-0.5 break-words font-bold text-emerald-700 font-mono">-</p>
                    </div>
                </div>

                <div>
                    <label for="whatsappPiutangInvoice"
                        class="mb-1.5 block text-xs font-bold text-gray-800">
                        Pilih Faktur Tagihan <span class="text-red-500">*</span>
                    </label>
                    <select id="whatsappPiutangInvoice" class="form-input w-full rounded-xl text-xs sm:text-sm">
                        <option value="">Pilih faktur yang akan diingatkan</option>
                    </select>
                </div>

                <div id="whatsappPiutangDetail"
                    class="hidden space-y-2 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-xs text-gray-500 font-medium">Nomor Faktur</span>
                        <span id="whatsappPiutangNoFaktur"
                            class="break-words text-right text-xs font-bold text-gray-800 font-mono">-</span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-xs text-gray-500 font-medium">Jatuh Tempo</span>
                        <span id="whatsappPiutangJatuhTempo"
                            class="text-right text-xs font-semibold text-gray-800">-</span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <span class="text-xs text-gray-500 font-medium">Sisa Piutang</span>
                        <span id="whatsappPiutangSisa"
                            class="text-right text-sm font-bold text-red-600">-</span>
                    </div>
                </div>

                <div>
                    <label for="whatsappPiutangPesan"
                        class="mb-1.5 block text-xs font-bold text-gray-800">
                        Pratinjau Pesan WhatsApp
                    </label>
                    <div class="rounded-2xl border border-gray-200 p-3 bg-white focus-within:border-emerald-500 transition">
                        <textarea id="whatsappPiutangPesan"
                            class="w-full resize-y font-sans text-xs sm:text-sm text-gray-800 focus:outline-none leading-relaxed min-h-[140px] border-0 p-0 focus:ring-0"
                            placeholder="Pesan akan tampil setelah faktur dipilih."></textarea>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400">
                        Pesan ini disusun otomatis dan dapat disesuaikan sebelum dikirim.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex shrink-0 justify-between items-center border-t border-gray-100 px-5 py-4 bg-white">
            <button type="button"
                onclick="closeWhatsAppPiutangPreview()"
                class="rounded-xl border border-gray-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                Tutup
            </button>

            <button type="button"
                onclick="bukaKonfirmasiWhatsApp()"
                id="btnKirimWhatsAppPiutang"
                disabled
                title="Pilih faktur terlebih dahulu untuk mengaktifkan pengiriman"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white opacity-50 cursor-not-allowed transition shadow-sm">
                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                <span>Kirim Pesan WhatsApp</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: PILIH JENIS PESAN WHATSAPP (POPUP 1 REFERENSI) -->
<!-- ======================================================== -->
<div id="whatsappCategoryModal"
    style="z-index: 10010;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/50 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-200">

        <!-- Header Modal  -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 leading-tight">
                        Kirim Pesan WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Pilih jenis pesan yang ingin dikirim ke member (<span id="waCategoryMemberCount" class="font-semibold text-emerald-600">0</span> dipilih)
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeWhatsAppCategoryModal()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Daftar Pilihan Kategori Vertikal -->
        <div class="min-h-0 flex-1 overflow-y-auto p-5 text-sm space-y-3">
            <div class="flex items-center justify-between pb-1">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider text-gray-600">
                    Jenis Pesan
                </h4>
                <button type="button"
                    onclick="openTemplateManageModal('category')"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50/80 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-xl transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span>Kelola Template Custom</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="waCategoryOptionsList">

                <!-- 1. Pengingat Piutang -->
                <div onclick="selectCategoryCard('pengingat_piutang')"
                    id="catCard_pengingat_piutang"
                    class="category-option-card flex items-center p-3 rounded-2xl border transition cursor-pointer bg-[#F0FDF4]/70 border-emerald-400 shadow-2xs">
                    <!-- Radio Button Concentric -->
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border-2 border-emerald-600 flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        </span>
                    </div>

                    <!-- Icon Square -->
                    <div id="catIcon_pengingat_piutang" class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>

                    <!-- Texts -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                                Pengingat Piutang
                            </h5>
                            <span id="catBadge_pengingat_piutang" class="hidden text-[10px] font-semibold text-gray-500 bg-gray-200 px-1.5 py-0.2 rounded">
                                Nonaktif
                            </span>
                        </div>
                        <p id="catDesc_pengingat_piutang" class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Untuk member dengan piutang belum lunas
                        </p>
                    </div>
                </div>

                <!-- 2. Promosi / Diskon Terbaru -->
                <div onclick="selectCategoryCard('promosi_diskon')"
                    id="catCard_promosi_diskon"
                    class="category-option-card flex items-center p-3 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 transition cursor-pointer shadow-2xs">
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-500 flex items-center justify-center shrink-0 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                            Promosi / Diskon Terbaru
                        </h5>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Informasi promo, diskon, & produk
                        </p>
                    </div>
                </div>

                <!-- 3. Ucapan -->
                <div onclick="selectCategoryCard('ucapan_apresiasi')"
                    id="catCard_ucapan_apresiasi"
                    class="category-option-card flex items-center p-3 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 transition cursor-pointer shadow-2xs">
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-500 flex items-center justify-center shrink-0 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                            Ucapan
                        </h5>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Ucapan ulang tahun & hari raya
                        </p>
                    </div>
                </div>

                <!-- 4. Informasi Produk -->
                <div onclick="selectCategoryCard('informasi_produk')"
                    id="catCard_informasi_produk"
                    class="category-option-card flex items-center p-3 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 transition cursor-pointer shadow-2xs">
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center shrink-0 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                            Informasi Produk
                        </h5>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Informasi stok & produk baru
                        </p>
                    </div>
                </div>

                <!-- 5. Informasi & Pengumuman -->
                <div onclick="selectCategoryCard('informasi_pengumuman')"
                    id="catCard_informasi_pengumuman"
                    class="category-option-card flex items-center p-3 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 transition cursor-pointer shadow-2xs">
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-500 flex items-center justify-center shrink-0 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                            Info & Pengumuman
                        </h5>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Jadwal libur & jam operasional
                        </p>
                    </div>
                </div>

                <!-- 6. Pesan Custom -->
                <div onclick="selectCategoryCard('pesan_custom')"
                    id="catCard_pesan_custom"
                    class="category-option-card flex items-center p-3 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 transition cursor-pointer shadow-2xs">
                    <div class="radio-indicator mr-3 shrink-0 flex items-center justify-center">
                        <span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-500 flex items-center justify-center shrink-0 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h5 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight">
                            Pesan Custom
                        </h5>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug truncate">
                            Tulis pesan sendiri / template custom
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Modal (Sesuai Referensi Gambar 1 & Luas Modal Piutang) -->
        <div class="flex shrink-0 items-center justify-between border-t border-gray-100 px-5 py-4 bg-white">
            <button type="button"
                onclick="closeWhatsAppCategoryModal()"
                class="rounded-xl border border-gray-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                Batal
            </button>

            <button type="button"
                onclick="lanjutKeEditorPesan()"
                class="rounded-xl bg-[#10B981] hover:bg-emerald-600 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white inline-flex items-center gap-2 shadow-sm transition">
                <span>Lanjutkan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 4: PILIH TEMPLATE & EDIT PESAN (POPUP 2 REFERENSI) -->
<!-- ======================================================== -->
<div id="whatsappEditorModal"
    style="z-index: 10020;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/50 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-200">

        <!-- Header Modal (Sesuai Referensi Gambar 2 & Luas Modal Piutang) -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 leading-tight">
                        Kirim Pesan WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Pilih template dan edit pesan sebelum dikirim
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeWhatsAppEditorModal()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Body Modal (Sesuai Referensi Gambar 2 & Luas Proporsional) -->
        <div class="min-h-0 flex-1 overflow-y-auto p-5 text-sm space-y-4">

            <!-- Alert Feedback Dinamis (Sukses / Gagal) -->
            <div id="waEditorAlertBox" class="hidden rounded-xl p-3 text-xs sm:text-sm font-semibold transition" role="alert"></div>

            <!-- Identitas Member Perorangan -->
            <div id="waEditorMemberInfoCard" class="hidden rounded-2xl border border-gray-200 bg-gray-50/70 p-3.5 space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="text-[11px] text-gray-500 font-medium block">Nama Penerima</span>
                        <p id="waEditorMemberNama" class="mt-0.5 font-bold text-gray-800 break-words">-</p>
                    </div>
                    <div>
                        <span class="text-[11px] text-gray-500 font-medium block">Nomor WhatsApp</span>
                        <p id="waEditorMemberTelepon" class="mt-0.5 font-mono font-bold text-emerald-700 break-words">-</p>
                    </div>
                </div>
                <div id="waEditorPhoneWarning" class="hidden rounded-xl bg-red-50 border border-red-200 p-2.5 text-xs text-red-700 font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Nomor WhatsApp member belum terdaftar atau format tidak valid. Lengkapi nomor telepon terlebih dahulu sebelum mengirim.</span>
                </div>
            </div>

            <!-- 1. Jenis Pesan (Banner Card Hijau Muda) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-gray-800">
                        Jenis Pesan
                    </label>
                </div>
                <div class="flex items-center justify-between bg-[#F0FDF4] border border-emerald-200 rounded-xl p-2.5 px-3">
                    <div class="flex items-center gap-2.5">
                        <div id="waEditorCatIconBadge" class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <!-- Icon dinamis via JS -->
                        </div>
                        <span id="waEditorCategoryName" class="text-xs sm:text-sm font-bold text-emerald-800">
                            Promosi / Diskon Terbaru
                        </span>
                    </div>

                    <button type="button"
                        onclick="kembaliKeKategoriModal()"
                        class="inline-flex items-center gap-1 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 rounded-lg px-2.5 py-1 text-xs font-semibold shadow-2xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Ubah</span>
                    </button>
                </div>
            </div>

            <!-- Bagian Khusus Pengingat Piutang: Pemilihan Faktur Tagihan -->
            <div id="waEditorInvoiceSection" class="hidden space-y-2.5 rounded-2xl border border-emerald-200 bg-emerald-50/40 p-4">
                <div class="flex items-center justify-between">
                    <label for="waEditorInvoiceSelect" class="block text-xs font-bold text-gray-800">
                        Pilih Faktur Tagihan Piutang <span class="text-red-500">*</span>
                    </label>
                    <span id="waEditorInvoiceBadge" class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                        0 Faktur Belum Lunas
                    </span>
                </div>

                <select id="waEditorInvoiceSelect"
                    onchange="handleEditorInvoiceChange()"
                    class="form-input w-full rounded-xl text-xs sm:text-sm bg-white border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 shadow-2xs">
                    <!-- Diisi dinamis via JS -->
                </select>

                <div id="waEditorNoDebtAlert" class="hidden rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Member ini tidak memiliki sisa piutang aktif yang belum lunas. Pengingat piutang tidak dapat dikirim.</span>
                </div>

                <!-- Ringkasan Faktur Terpilih -->
                <div id="waEditorInvoiceSummary" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 text-xs">
                    <div class="bg-white p-2.5 rounded-xl border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] font-medium">Faktur Terpilih</span>
                        <strong id="waEditorSummaryNoFaktur" class="font-mono text-gray-800 truncate block mt-0.5">-</strong>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] font-medium">Sisa Piutang yang Diingatkan</span>
                        <strong id="waEditorSummarySisa" class="font-bold text-red-600 block mt-0.5">-</strong>
                    </div>
                </div>
            </div>

            <!-- 2. Template Pesan (Dropdown) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-gray-800">
                        Template Pesan
                    </label>
                </div>
                <select id="waTemplateSelect"
                    onchange="applySelectedTemplate()"
                    class="w-full rounded-xl border border-gray-200 bg-white text-xs sm:text-sm py-2 px-3 text-gray-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                    <option value="">-- Memuat template... --</option>
                </select>
            </div>

            <!-- 3. Isi Pesan (Textarea Card & Counter) -->
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1.5">
                    Isi Pesan
                </label>
                <div class="rounded-2xl border border-gray-200 p-3.5 bg-white focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 transition shadow-2xs">
                    <textarea id="waEditorPesanText"
                        rows="6"
                        oninput="updateCharCount()"
                        class="w-full resize-y font-sans text-xs sm:text-sm text-gray-800 focus:outline-none leading-relaxed border-0 p-0 focus:ring-0"
                        placeholder="Ketik isi pesan WhatsApp di sini..."></textarea>
                    
                    <div class="flex justify-end pt-1 border-t border-gray-50 mt-1">
                        <span id="waCharCountText" class="text-[11px] text-gray-400 font-mono">
                            0/500
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Gunakan Placeholder (Accordion Card Sesuai Referensi Gambar 2) -->
            <div class="rounded-xl border border-gray-200 overflow-hidden bg-white shadow-2xs">
                <button type="button"
                    onclick="togglePlaceholderAccordion()"
                    class="w-full p-3 flex items-center justify-between text-left hover:bg-gray-50/80 transition cursor-pointer">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">
                            Gunakan Placeholder
                        </span>
                    </div>
                    <svg id="placeholderChevronIcon" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div id="placeholderAccordionContent" class="hidden border-t border-gray-100 p-3 bg-gray-50/50 space-y-2">
                    <p class="text-[11px] text-gray-500">
                        Klik placeholder di bawah untuk menyisipkan otomatis ke pesan Anda pada posisi kursor:
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button"
                            onclick="insertPlaceholder('{nama_member}')"
                            class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-emerald-500 hover:text-emerald-700 transition shadow-2xs">
                            + {nama_member}
                        </button>
                        <button type="button"
                            onclick="insertPlaceholder('{nama_apotek}')"
                            class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-emerald-500 hover:text-emerald-700 transition shadow-2xs">
                            + {nama_apotek}
                        </button>

                        <span id="waPiutangPlaceholderChips" class="inline-flex flex-wrap gap-1.5">
                            <button type="button"
                                onclick="insertPlaceholder('{total_piutang}')"
                                class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-red-200 text-red-700 rounded-lg hover:bg-red-50 transition shadow-2xs">
                                + {total_piutang}
                            </button>
                            <button type="button"
                                onclick="insertPlaceholder('{nomor_faktur}')"
                                class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-amber-200 text-amber-700 rounded-lg hover:bg-amber-50 transition shadow-2xs">
                                + {nomor_faktur}
                            </button>
                            <button type="button"
                                onclick="insertPlaceholder('{tanggal_jatuh_tempo}')"
                                class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-purple-200 text-purple-700 rounded-lg hover:bg-purple-50 transition shadow-2xs">
                                + {tanggal_jatuh_tempo}
                            </button>
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 pt-0.5">
                        * Placeholder akan diisi otomatis dengan data aktual masing-masing member saat pengiriman.
                    </p>
                </div>
            </div>

        </div>

        <!-- Footer Modal (Sesuai Referensi Gambar 2 & Luas Modal Piutang) -->
        <div class="flex shrink-0 items-center justify-between border-t border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-2">
                <button type="button"
                    onclick="closeWhatsAppEditorModal()"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs sm:text-sm font-semibold transition">
                    Batal
                </button>
                <button type="button"
                    onclick="kembaliKeKategoriModal()"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs sm:text-sm font-semibold transition">
                    Ubah Kategori
                </button>
            </div>

            <button type="button"
                id="btnEditorActionSubmit"
                onclick="handleEditorSubmit()"
                class="px-6 py-2.5 rounded-xl bg-[#10B981] hover:bg-emerald-600 text-white text-xs sm:text-sm font-semibold inline-flex items-center gap-2 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span id="btnEditorActionSubmitText">Kirim WhatsApp</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: KONFIRMASI PENGIRIMAN INDIVIDUAL (PALING DEPAN)   -->
<!-- ======================================================== -->
<div id="whatsappPiutangConfirmModal"
    style="z-index: 99999;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Konfirmasi -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600 shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Konfirmasi Pengiriman WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400">
                        Periksa kembali data sebelum pesan dikirim ke gateway
                    </p>
                </div>
            </div>

            <button type="button"
                id="btnBatalKirimWhatsAppX"
                onclick="closeWhatsAppPiutangConfirm()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Batal">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi Konfirmasi -->
        <div class="min-h-0 flex-1 space-y-3.5 overflow-y-auto p-5 text-sm">

            <div id="waConfirmError"
                class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-xs sm:text-sm text-red-700 font-medium"
                role="alert"></div>

            <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3.5 space-y-2">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Nama Member:</span>
                    <span id="waConfirmNama" class="font-bold text-gray-800 text-right">-</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">WhatsApp Tujuan:</span>
                    <span id="waConfirmTelepon" class="font-mono font-bold text-emerald-700 text-right">-</span>
                </div>

                <!-- Baris Khusus Pengingat Piutang -->
                <div id="waConfirmPiutangRows" class="space-y-2 pt-1 border-t border-gray-200/60">
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-gray-500">Nomor Faktur:</span>
                        <span id="waConfirmNoFaktur" class="font-mono font-semibold text-gray-800 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-gray-500">Sisa Piutang:</span>
                        <span id="waConfirmSisa" class="font-bold text-red-600 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-gray-500">Jatuh Tempo:</span>
                        <span id="waConfirmJatuhTempo" class="font-medium text-gray-700 text-right">-</span>
                    </div>
                </div>

                <!-- Baris Khusus Non-Piutang -->
                <div id="waConfirmNonPiutangRows" class="hidden space-y-2 pt-1 border-t border-gray-200/60">
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-gray-500">Jenis Pesan:</span>
                        <span id="waConfirmJenisPesan" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-xs bg-emerald-100 text-emerald-800">-</span>
                    </div>
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-gray-500">Nama Template:</span>
                        <span id="waConfirmNamaTemplate" class="font-semibold text-gray-800 text-right">-</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    Isi Pesan Final yang Akan Dikirim:
                </label>
                <div id="waConfirmPesan"
                    class="rounded-xl border border-gray-200 bg-white p-3 text-xs text-gray-700 whitespace-pre-line max-h-36 overflow-y-auto leading-relaxed font-sans shadow-2xs">
                </div>
                <p class="mt-1 text-[11px] text-gray-400">
                    * Seluruh placeholder telah diganti otomatis dengan data aktual penerima.
                </p>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-200 p-2.5 text-xs text-blue-800">
                Pesan akan dikirim langsung ke nomor WhatsApp member melalui WhatsApp Gateway resmi.
            </div>
        </div>

        <!-- Footer Konfirmasi -->
        <div class="flex shrink-0 justify-between items-center border-t border-gray-100 px-5 py-4 bg-white">
            <button type="button"
                id="btnBatalKirimWhatsApp"
                onclick="closeWhatsAppPiutangConfirm()"
                class="rounded-xl border border-gray-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                Batal
            </button>

            <button type="button"
                id="btnEksekusiKirimWhatsApp"
                onclick="handleConfirmSendClick()"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white transition shadow-sm">
                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                <span id="btnEksekusiKirimWhatsAppText">Ya, Kirim Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 5: KELOLA TEMPLATE PESAN (POPUP 3 REFERENSI)      -->
<!-- ======================================================== -->
<div id="whatsappTemplateManageModal"
    style="z-index: 99990;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-3xl max-h-[92vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Modal -->
        <div class="flex shrink-0 items-center justify-between p-5 pb-3 bg-white">
            <div>
                <h3 class="text-base font-bold text-gray-800 leading-tight">
                    Template Pesan
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Kelola template pesan WhatsApp
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button"
                    onclick="openTemplateFormModal(false)"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-xs px-3.5 py-2 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Template</span>
                </button>

                <button type="button"
                    onclick="closeTemplateManageModal()"
                    class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                    title="Tutup">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>

        <!-- Filter Kategori Sederhana -->
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-2.5 bg-gray-50/70 gap-3">
            <div class="flex items-center gap-2">
                <label for="tmplFilterKategori" class="text-xs font-semibold text-gray-600 shrink-0">Filter Jenis:</label>
                <select id="tmplFilterKategori"
                    onchange="filterTemplatesTable()"
                    class="rounded-xl border border-gray-200 bg-white text-xs py-1.5 px-3 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs">
                    <option value="all">Semua Jenis Pesan</option>
                    <option value="pengingat_piutang">Pengingat Piutang</option>
                    <option value="promosi_diskon">Promosi / Diskon</option>
                    <option value="ucapan_apresiasi">Ucapan & Apresiasi</option>
                    <option value="informasi_produk">Informasi Produk</option>
                    <option value="informasi_pengumuman">Info & Pengumuman</option>
                    <option value="pesan_custom">Pesan Custom</option>
                </select>
            </div>
            <span id="tmplTotalCountBadge" class="text-[11px] font-semibold text-gray-500 bg-gray-200/80 px-2.5 py-0.5 rounded-full">
                0 Template
            </span>
        </div>

        <!-- Body Modal dengan Horizontal Scroll untuk Tabel -->
        <div class="min-h-0 flex-1 overflow-y-auto p-5 text-sm">
            <!-- Tabel Daftar Template Satu Kesatuan (Bawaan & Custom dalam 1 Tabel) -->
            <div class="rounded-xl border border-gray-200 overflow-x-auto shadow-2xs bg-white">
                <table class="w-full text-xs text-left min-w-[760px] table-fixed border-collapse">
                    <colgroup>
                        <col style="width: 220px;">
                        <col style="width: 125px;">
                        <col style="width: 95px;">
                        <col style="width: 200px;">
                        <col style="width: 120px;">
                    </colgroup>
                    <thead class="bg-gray-50/90 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-3.5 whitespace-nowrap">Nama Template</th>
                            <th class="py-3 px-3 text-center whitespace-nowrap">Jenis Pesan</th>
                            <th class="py-3 px-3 text-center whitespace-nowrap">Sumber</th>
                            <th class="py-3 px-3 whitespace-nowrap">Isi Pesan</th>
                            <th class="py-3 px-3.5 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tmplTableBody" class="divide-y divide-gray-150 bg-white">
                        <!-- Diisi via JS gabungan bawaan dan custom -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex shrink-0 justify-end border-t border-gray-100 px-5 py-3.5 bg-white">
            <button type="button"
                onclick="closeTemplateManageModal()"
                class="rounded-xl border border-gray-200 px-5 py-2 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                <span id="tmplFooterBtnText">Kembali</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 5B: POPUP TAMBAH / EDIT TEMPLATE WHATSAPP         -->
<!-- ======================================================== -->
<div id="whatsappTemplateFormModal"
    style="z-index: 99995;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-lg max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-200">

        <!-- Header Form Modal -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="tmplFormTitle" class="text-base font-bold text-gray-800">
                        Tambah Template WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400">
                        Buat template pesan kustom untuk mempermudah pengiriman
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeTemplateFormModal()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Batal / Tutup">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Body Form Modal -->
        <div class="min-h-0 flex-1 space-y-3.5 overflow-y-auto p-5 text-sm">

            <div id="tmplFormError"
                class="hidden rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700 font-medium"
                role="alert"></div>

            <input type="hidden" id="tmplEditId" value="">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    Nama Template <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="tmplInputNama"
                    class="w-full rounded-xl border border-gray-200 bg-white text-xs sm:text-sm py-2 px-3 text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs"
                    placeholder="Contoh: Promo Diskon Member Akhir Pekan">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    Jenis Pesan (Kategori) <span class="text-red-500">*</span>
                </label>
                <select id="tmplInputKategori"
                    class="w-full rounded-xl border border-gray-200 bg-white text-xs sm:text-sm py-2 px-3 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs">
                    <option value="pengingat_piutang">Pengingat Piutang</option>
                    <option value="promosi_diskon">Promosi / Diskon</option>
                    <option value="ucapan_apresiasi">Ucapan & Apresiasi</option>
                    <option value="informasi_produk">Informasi Produk</option>
                    <option value="informasi_pengumuman">Info & Pengumuman</option>
                    <option value="pesan_custom">Pesan Custom</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    Isi Pesan Template <span class="text-red-500">*</span>
                </label>
                <textarea id="tmplInputKonten"
                    rows="5"
                    class="w-full rounded-xl border border-gray-200 bg-white text-xs sm:text-sm py-2.5 px-3 text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs leading-relaxed"
                    placeholder="Tuliskan isi pesan template di sini..."></textarea>
            </div>

            <!-- Helper Placeholder Chips -->
            <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-3 space-y-1.5">
                <span class="block text-[11px] font-semibold text-gray-600">
                    Sisipkan Variabel / Placeholder:
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button"
                        onclick="insertPlaceholderToTemplate('{nama_member}')"
                        class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-blue-500 hover:text-blue-700 shadow-2xs transition">
                        + {nama_member}
                    </button>
                    <button type="button"
                        onclick="insertPlaceholderToTemplate('{nama_apotek}')"
                        class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-blue-500 hover:text-blue-700 shadow-2xs transition">
                        + {nama_apotek}
                    </button>
                    <button type="button"
                        onclick="insertPlaceholderToTemplate('{total_piutang}')"
                        class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-red-200 text-red-700 rounded-lg hover:bg-red-50 shadow-2xs transition">
                        + {total_piutang}
                    </button>
                    <button type="button"
                        onclick="insertPlaceholderToTemplate('{nomor_faktur}')"
                        class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-amber-200 text-amber-700 rounded-lg hover:bg-amber-50 shadow-2xs transition">
                        + {nomor_faktur}
                    </button>
                    <button type="button"
                        onclick="insertPlaceholderToTemplate('{tanggal_jatuh_tempo}')"
                        class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-purple-200 text-purple-700 rounded-lg hover:bg-purple-50 shadow-2xs transition">
                        + {tanggal_jatuh_tempo}
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 pt-0.5">
                    * Placeholder otomatis diganti dengan data penerima saat pesan dikirimkan.
                </p>
            </div>
        </div>

        <!-- Footer Form Modal -->
        <div class="flex shrink-0 justify-between items-center border-t border-gray-100 px-5 py-3.5 bg-white">
            <button type="button"
                id="btnBatalSimpanTemplate"
                onclick="closeTemplateFormModal()"
                class="rounded-xl border border-gray-200 px-5 py-2 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                Batal
            </button>

            <button type="button"
                id="btnSimpanTemplateAction"
                onclick="simpanTemplateCustom()"
                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2 text-xs sm:text-sm font-semibold text-white transition shadow-sm">
                <x-heroicon-o-check class="w-4 h-4" />
                <span id="btnSimpanTemplateText">Simpan Template</span>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 6: KONFIRMASI & RINGKASAN PENERIMA MASSAL         -->
<!-- ======================================================== -->
<div id="bulkWhatsAppConfirmModal"
    style="z-index: 99999;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Konfirmasi -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 shrink-0">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Konfirmasi Pengiriman WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400">
                        Periksa ringkasan penerima dan contoh pesan sebelum pengiriman dijalankan
                    </p>
                </div>
            </div>

            <button type="button"
                onclick="closeBulkWhatsAppConfirmModal()"
                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                title="Batal">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <!-- Isi Konfirmasi -->
        <div class="min-h-0 flex-1 space-y-3.5 overflow-y-auto p-5 text-sm">

            <!-- Kartu Statistik Ringkasan -->
            <div class="grid grid-cols-3 gap-2.5">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-2.5 text-center">
                    <span class="block text-[11px] text-gray-500 font-medium">Member Dipilih</span>
                    <strong id="bulkStatDipilih" class="text-sm sm:text-base font-bold text-gray-800">0</strong>
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-2.5 text-center">
                    <span class="block text-[11px] text-emerald-700 font-medium">Memenuhi Syarat</span>
                    <strong id="bulkStatEligible" class="text-sm sm:text-base font-bold text-emerald-700">0</strong>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-2.5 text-center">
                    <span class="block text-[11px] text-amber-700 font-medium">Dilewati</span>
                    <strong id="bulkStatDikeluarkan" class="text-sm sm:text-base font-bold text-amber-700">0</strong>
                </div>
            </div>

            <!-- Peringatan Member Dilewati (Jika ada) -->
            <div id="bulkConfirmNotice"
                class="hidden rounded-xl border border-amber-200 bg-amber-50/80 p-3 text-xs text-amber-900 space-y-1.5"
                role="alert">
                <div class="font-bold flex items-center gap-1.5 text-amber-800">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                    <span>Member Dilewati dari Pengiriman:</span>
                </div>
                <ul id="bulkSkippedList" class="list-disc list-inside space-y-0.5 text-amber-800 max-h-24 overflow-y-auto pl-1">
                    <!-- Diisi via JS -->
                </ul>
            </div>

            <!-- Tabel Daftar Penerima yang Memenuhi Syarat -->
            <div>
                <span class="block text-xs font-semibold text-gray-600 mb-1">
                    Daftar Member Penerima:
                </span>
                <div class="rounded-xl border border-gray-200 overflow-hidden shadow-2xs">
                    <div class="max-h-40 overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-100 text-gray-600 font-semibold sticky top-0 border-b border-gray-200">
                                <tr>
                                    <th class="py-2 px-3">Nama Member</th>
                                    <th class="py-2 px-3">No. WhatsApp</th>
                                    <th class="py-2 px-3 text-right">Sisa Piutang</th>
                                    <th class="py-2 px-3 text-center">Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody id="bulkConfirmTableBody" class="divide-y divide-gray-150">
                                <!-- Diisi via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pratinjau Pesan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Pratinjau Contoh Pesan (Penerima Pertama):
                </label>
                <div id="bulkConfirmPreviewText"
                    class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 whitespace-pre-line leading-relaxed font-sans max-h-32 overflow-y-auto">
                </div>
            </div>
        </div>

        <!-- Footer Konfirmasi -->
        <div class="flex shrink-0 justify-between items-center border-t border-gray-100 px-5 py-4 bg-white">
            <button type="button"
                onclick="kembaliKeEditorPesan()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 px-4 py-2.5 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
                <span>Kembali ke Editor</span>
            </button>

            <div class="flex gap-2">
                <button type="button"
                    onclick="closeBulkWhatsAppConfirmModal()"
                    class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs sm:text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                    Batal
                </button>

                <button type="button"
                    id="btnStartBulkSend"
                    onclick="startBulkWhatsAppSending()"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white transition shadow-sm">
                    <x-heroicon-o-paper-airplane class="h-4 w-4" />
                    <span>Konfirmasi dan Kirim</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 7: PROSES PENGIRIMAN MASSAL                       -->
<!-- ======================================================== -->
<div id="bulkWhatsAppProgressModal"
    style="z-index: 100020;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- Header Proses -->
        <div class="border-b border-gray-100 px-5 py-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 shrink-0">
                    <x-heroicon-o-paper-airplane class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Mengirim Pesan WhatsApp
                    </h3>
                    <p class="text-xs text-gray-400">
                        Mohon tunggu, pesan sedang diproses ke <span id="bulkProgressHeaderTotal">0</span> member...
                    </p>
                </div>
            </div>
        </div>

        <!-- Isi Proses List -->
        <div class="min-h-0 flex-1 space-y-3 overflow-y-auto p-5">
            <div id="bulkProgressList" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                <!-- Render item penerima dan statusnya -->
            </div>

            <!-- Progress Bar -->
            <div class="pt-3 border-t border-gray-150 space-y-1.5">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="bulkProgressBarFill" class="bg-emerald-600 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span id="bulkProgressDoneText">0 dari 0 selesai</span>
                    <span id="bulkProgressPercentText" class="font-semibold text-gray-700">0%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 8: HASIL PENGIRIMAN MASSAL                        -->
<!-- ======================================================== -->
<div id="bulkWhatsAppResultModal"
    style="z-index: 100030;"
    class="modal-backdrop-custom hidden fixed inset-0 items-center justify-center bg-black/60 p-4 backdrop-blur-xs">

    <div class="modal-container-custom flex w-full max-w-lg max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl p-6 text-center">

        <!-- Centered Hero Icon -->
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mx-auto mb-3">
            <x-heroicon-o-check class="h-9 w-9 stroke-[2.5]" />
        </div>

        <h3 class="text-lg font-bold text-gray-800">
            Pengiriman WhatsApp Selesai
        </h3>
        <p class="text-xs text-gray-400 mt-1 mb-5">
            Semua pesan WhatsApp telah diproses oleh WhatsApp Gateway.
        </p>

        <!-- Kartu Ringkasan -->
        <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4 space-y-2.5 text-left text-xs sm:text-sm mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Member Dipilih:</span>
                <strong id="bulkResultTotal" class="font-bold text-gray-800">0</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Berhasil Terkirim:</span>
                <strong id="bulkResultSuccess" class="font-bold text-emerald-600">0</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Gagal:</span>
                <strong id="bulkResultFailed" class="font-bold text-red-600">0</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Dilewati (Tidak Memenuhi Syarat):</span>
                <strong id="bulkResultSkipped" class="font-bold text-amber-600">0</strong>
            </div>
        </div>

        <!-- Tombol Tutup -->
        <button type="button"
            onclick="closeBulkWhatsAppResultModal()"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow transition">
            Tutup dan Perbarui Halaman
        </button>
    </div>
</div>

<script>
/* ==========================================================
 * STATE GLOBAL UNTUK WHATSAPP
 * ========================================================== */
let currentPelangganId = null;
let whatsappPiutangTagihan = [];
const defaultNamaApotek = @json(\App\Models\InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek'));
let currentNamaApotek = defaultNamaApotek;

// State untuk pengiriman multi-kategori & perorangan
let isIndividualSend = false;
let currentIndividualMember = null;
let currentIndividualTagihan = [];
let currentIndividualTotalPiutang = 0;
let selectedMemberIds = [];
let currentKategoriPesan = 'pengingat_piutang';
let availableTemplates = [];
let currentBulkRecipients = [];
let currentBulkDikeluarkan = [];
let currentFinalPesan = '';
let templateManageSource = 'category';

const categoryMeta = {
    'pengingat_piutang': {
        name: 'Pengingat Piutang',
        color: 'emerald',
        badgeClass: 'bg-[#E0F2FE] text-[#0284C7]',
        badgeText: 'Piutang',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>`
    },
    'promosi_diskon': {
        name: 'Promosi / Diskon Terbaru',
        color: 'blue',
        badgeClass: 'bg-[#DCFCE7] text-[#16A34A]',
        badgeText: 'Promosi',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>`
    },
    'ucapan_apresiasi': {
        name: 'Ucapan',
        color: 'purple',
        badgeClass: 'bg-[#F3E8FF] text-[#9333EA]',
        badgeText: 'Ucapan',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>`
    },
    'informasi_produk': {
        name: 'Informasi Produk',
        color: 'amber',
        badgeClass: 'bg-[#FFEDD5] text-[#EA580C]',
        badgeText: 'Informasi',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>`
    },
    'informasi_pengumuman': {
        name: 'Info & Pengumuman',
        color: 'indigo',
        badgeClass: 'bg-[#FFEDD5] text-[#EA580C]',
        badgeText: 'Informasi',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`
    },
    'pesan_custom': {
        name: 'Pesan Custom',
        color: 'sky',
        badgeClass: 'bg-gray-100 text-gray-700',
        badgeText: 'Custom',
        iconHtml: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>`
    }
};

/* ==========================================================
 * HELPER FORMAT RUPIAH & ESCAPE HTML
 * ========================================================== */
function formatRupiahWhatsApp(nilai) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(nilai) || 0);
}

function escapeHtmlWhatsApp(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ==========================================================
 * 1. PENGINGAT PIUTANG INDIVIDUAL (TETAP BERFUNGSI)
 * ========================================================== */
function closeWhatsAppPiutangPreview() {
    const modal = document.getElementById('whatsappPiutangModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function closeWhatsAppPiutangConfirm() {
    const modal = document.getElementById('whatsappPiutangConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    if (isIndividualSend) {
        const editorModal = document.getElementById('whatsappEditorModal');
        if (editorModal) {
            editorModal.classList.remove('hidden');
            editorModal.classList.add('flex');
        }
    }
}

function updateBtnKirimState(isEnabled) {
    const btn = document.getElementById('btnKirimWhatsAppPiutang');
    if (!btn) return;

    if (isEnabled) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.classList.add('hover:bg-emerald-700', 'cursor-pointer');
        btn.title = 'Kirim Pesan WhatsApp Pengingat Piutang';
    } else {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-emerald-700', 'cursor-pointer');
        btn.title = 'Pilih faktur terlebih dahulu untuk mengaktifkan pengiriman';
    }
}

function openWhatsAppPiutangPreview(pelangganId) {
    currentPelangganId = pelangganId;

    const modal = document.getElementById('whatsappPiutangModal');
    const loading = document.getElementById('whatsappPiutangLoading');
    const errorBox = document.getElementById('whatsappPiutangError');
    const successBox = document.getElementById('whatsappPiutangSuccess');
    const content = document.getElementById('whatsappPiutangContent');
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');

    if (!modal || !loading || !errorBox || !content || !invoiceSelect) {
        alert('Komponen modal WhatsApp belum siap.');
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    loading.classList.remove('hidden');
    errorBox.classList.add('hidden');
    if (successBox) successBox.classList.add('hidden');
    content.classList.add('hidden');

    whatsappPiutangTagihan = [];
    invoiceSelect.innerHTML = '<option value="">Pilih faktur yang akan diingatkan</option>';

    document.getElementById('whatsappPiutangDetail').classList.add('hidden');
    document.getElementById('whatsappPiutangPesan').value = '';
    updateBtnKirimState(false);

    const urlTemplate = @json(route('pelanggan.whatsapp-piutang.preview', ['pelanggan' => '__ID__']));
    const url = urlTemplate.replace('__ID__', encodeURIComponent(pelangganId));

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'Gagal mengambil pratinjau tagihan.');
        }

        return data;
    })
    .then(data => {
        if (!data.pelanggan || !Array.isArray(data.tagihan) || data.tagihan.length === 0) {
            throw new Error(data.message || 'Tidak ada tagihan yang memenuhi syarat pengingat.');
        }

        if (!data.pelanggan.telepon || !String(data.pelanggan.telepon).trim()) {
            throw new Error('Nomor telepon member belum diisi. Lengkapi data pelanggan terlebih dahulu.');
        }

        whatsappPiutangTagihan = data.tagihan;
        currentNamaApotek = data.nama_apotek || defaultNamaApotek;

        document.getElementById('whatsappPiutangNama').textContent = data.pelanggan.nama || '-';
        document.getElementById('whatsappPiutangTelepon').textContent = data.pelanggan.telepon;

        data.tagihan.forEach(tagihan => {
            const option = document.createElement('option');
            option.value = String(tagihan.penjualan_id);
            option.textContent =
                `${tagihan.no_faktur || 'Faktur #' + tagihan.penjualan_id} — ` +
                formatRupiahWhatsApp(tagihan.sisa_piutang) +
                ` — jatuh tempo ${tagihan.jatuh_tempo}`;

            invoiceSelect.appendChild(option);
        });

        content.classList.remove('hidden');
    })
    .catch(error => {
        errorBox.textContent = error.message || 'Terjadi kesalahan saat mengambil tagihan.';
        errorBox.classList.remove('hidden');
    })
    .finally(() => {
        loading.classList.add('hidden');
    });
}

document.getElementById('whatsappPiutangInvoice')?.addEventListener('change', function () {
    const detail = document.getElementById('whatsappPiutangDetail');
    const pesan = document.getElementById('whatsappPiutangPesan');

    const tagihan = whatsappPiutangTagihan.find(
        item => String(item.penjualan_id) === this.value
    );

    if (!tagihan) {
        detail.classList.add('hidden');
        pesan.value = '';
        updateBtnKirimState(false);
        return;
    }

    document.getElementById('whatsappPiutangNoFaktur').textContent = tagihan.no_faktur || '-';
    document.getElementById('whatsappPiutangJatuhTempo').textContent = tagihan.jatuh_tempo || '-';
    document.getElementById('whatsappPiutangSisa').textContent = formatRupiahWhatsApp(tagihan.sisa_piutang);

    const nama = document.getElementById('whatsappPiutangNama').textContent;
    const sisa = formatRupiahWhatsApp(tagihan.sisa_piutang);
    const jatuhTempo = tagihan.jatuh_tempo || '-';
    const apotek = currentNamaApotek || defaultNamaApotek;

    pesan.value =
        `Yth. Bapak/Ibu ${nama},\n\n` +
        `Kami dari ${apotek} ingin mengingatkan bahwa masih terdapat piutang sebesar ${sisa} dengan tanggal jatuh tempo ${jatuhTempo}.\n\n` +
        `Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n` +
        `Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n` +
        `Terima kasih atas perhatian dan kerja samanya.\n\n` +
        `Salam,\n` +
        `${apotek}`;

    detail.classList.remove('hidden');
    updateBtnKirimState(true);
});

function bukaKonfirmasiWhatsApp() {
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');
    const penjualanId = invoiceSelect ? invoiceSelect.value : '';

    const tagihan = whatsappPiutangTagihan.find(
        item => String(item.penjualan_id) === penjualanId
    );

    if (!tagihan) {
        alert('Silakan pilih faktur tagihan terlebih dahulu.');
        return;
    }

    const nama = document.getElementById('whatsappPiutangNama').textContent;
    const telepon = document.getElementById('whatsappPiutangTelepon').textContent;
    const pesan = document.getElementById('whatsappPiutangPesan').value;

    document.getElementById('waConfirmNama').textContent = nama;
    document.getElementById('waConfirmTelepon').textContent = telepon;
    document.getElementById('waConfirmNoFaktur').textContent = tagihan.no_faktur || ('#' + tagihan.penjualan_id);
    document.getElementById('waConfirmSisa').textContent = formatRupiahWhatsApp(tagihan.sisa_piutang);
    document.getElementById('waConfirmJatuhTempo').textContent = tagihan.jatuh_tempo || '-';
    document.getElementById('waConfirmPesan').textContent = pesan;

    const confirmError = document.getElementById('waConfirmError');
    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const modalConfirm = document.getElementById('whatsappPiutangConfirmModal');
    if (modalConfirm) {
        modalConfirm.classList.remove('hidden');
        modalConfirm.classList.add('flex');
    }
}

function handleConfirmSendClick() {
    if (isIndividualSend) {
        eksekusiKirimIndividualWhatsApp();
    } else {
        eksekusiKirimWhatsApp();
    }
}

function eksekusiKirimWhatsApp() {
    const invoiceSelect = document.getElementById('whatsappPiutangInvoice');
    const penjualanId = invoiceSelect ? invoiceSelect.value : '';

    if (!currentPelangganId || !penjualanId) {
        alert('Data pengiriman tidak lengkap. Silakan pilih faktur terlebih dahulu.');
        return;
    }

    const btnKirim = document.getElementById('btnEksekusiKirimWhatsApp');
    const btnBatal = document.getElementById('btnBatalKirimWhatsApp');
    const btnBatalX = document.getElementById('btnBatalKirimWhatsAppX');
    const btnText = document.getElementById('btnEksekusiKirimWhatsAppText');
    const confirmError = document.getElementById('waConfirmError');
    const successBox = document.getElementById('whatsappPiutangSuccess');
    const pesanCustom = document.getElementById('whatsappPiutangPesan')?.value;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        if (confirmError) {
            confirmError.textContent = 'Token keamanan (CSRF) tidak ditemukan. Silakan muat ulang halaman.';
            confirmError.classList.remove('hidden');
        }
        return;
    }

    btnKirim.disabled = true;
    btnBatal.disabled = true;
    if (btnBatalX) btnBatalX.disabled = true;
    btnKirim.classList.add('opacity-60', 'cursor-not-allowed');
    btnText.textContent = 'Sedang mengirim...';

    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const sendUrlTemplate = @json(route('pelanggan.whatsapp-piutang.send', ['pelanggan' => '__ID__']));
    const sendUrl = sendUrlTemplate.replace('__ID__', encodeURIComponent(currentPelangganId));

    fetch(sendUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            penjualan_id: parseInt(penjualanId, 10),
            pesan: pesanCustom
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.status) {
            throw new Error(data.message || 'Gagal mengirim pesan WhatsApp.');
        }

        return data;
    })
    .then(data => {
        closeWhatsAppPiutangConfirm();

        if (successBox) {
            successBox.textContent = data.message || 'Pesan pengingat WhatsApp berhasil dikirim ke nomor member.';
            successBox.classList.remove('hidden');
        }

        setTimeout(() => {
            openWhatsAppPiutangPreview(currentPelangganId);
        }, 1200);
    })
    .catch(error => {
        if (confirmError) {
            confirmError.textContent = error.message || 'Terjadi kesalahan saat memproses pengiriman WhatsApp.';
            confirmError.classList.remove('hidden');
        }
    })
    .finally(() => {
        btnKirim.disabled = false;
        btnBatal.disabled = false;
        if (btnBatalX) btnBatalX.disabled = false;
        btnKirim.classList.remove('opacity-60', 'cursor-not-allowed');
        btnText.textContent = 'Ya, Kirim Sekarang';
    });
}

/* ==========================================================
 * 2. SELEKSI CHECKBOX MASSAL
 * ========================================================== */
function getCheckedMemberBoxes() {
    return Array.from(document.querySelectorAll('.member-checkbox:checked'));
}

function getAllMemberBoxes() {
    return Array.from(document.querySelectorAll('.member-checkbox'));
}

function updateBulkActionBar() {
    const checkedBoxes = getCheckedMemberBoxes();
    const allBoxes = getAllMemberBoxes();
    const count = checkedBoxes.length;

    const bar = document.getElementById('bulkWhatsAppActionBar');
    const countText = document.getElementById('bulkSelectedCount');
    const totalPiutangText = document.getElementById('bulkSelectedTotalPiutang');
    const btnCountText = document.getElementById('bulkBtnCountText');
    const masterCheckbox = document.getElementById('checkAllMember');

    if (countText) countText.textContent = count;
    if (btnCountText) btnCountText.textContent = count;

    if (count > 0) {
        let total = 0;
        checkedBoxes.forEach(cb => {
            total += parseFloat(cb.getAttribute('data-sisa') || 0);
        });
        if (totalPiutangText) {
            totalPiutangText.textContent = formatRupiahWhatsApp(total);
        }

        if (bar) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        }
    } else {
        if (bar) {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    if (masterCheckbox && allBoxes.length > 0) {
        if (count === allBoxes.length) {
            masterCheckbox.checked = true;
            masterCheckbox.indeterminate = false;
        } else if (count > 0) {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = true;
        } else {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = false;
        }
    } else if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
}

function handleMemberCheckboxChange() {
    updateBulkActionBar();
}

function toggleSelectAllMember(master) {
    const allBoxes = getAllMemberBoxes();
    allBoxes.forEach(cb => {
        cb.checked = master.checked;
    });
    updateBulkActionBar();
}

function deselectAllMember() {
    const allBoxes = getAllMemberBoxes();
    allBoxes.forEach(cb => {
        cb.checked = false;
    });
    const masterCheckbox = document.getElementById('checkAllMember');
    if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
    updateBulkActionBar();
}

/* ==========================================================
 * 3. LOGIKA POPUP 1: PILIH JENIS PESAN (REFERENSI GAMBAR 1)
 * ========================================================== */
function selectCategoryCard(catKey) {
    if (isIndividualSend && catKey === 'pengingat_piutang') {
        const hasDebt = (currentIndividualTagihan && currentIndividualTagihan.length > 0 && currentIndividualTotalPiutang > 0);
        if (!hasDebt) {
            alert('Member ini tidak memiliki piutang aktif yang belum lunas. Silakan pilih jenis pesan lainnya.');
            return;
        }
    }

    currentKategoriPesan = catKey;

    const allCards = document.querySelectorAll('.category-option-card');
    allCards.forEach(card => {
        card.classList.remove('bg-[#F0FDF4]/70', 'border-emerald-400', 'border-2');
        card.classList.add('border-gray-200', 'bg-white');

        const indicator = card.querySelector('.radio-indicator');
        if (indicator) {
            indicator.innerHTML = `<span class="w-4 h-4 rounded-full border border-gray-300 bg-white"></span>`;
        }
    });

    const selectedCard = document.getElementById(`catCard_${catKey}`);
    if (selectedCard) {
        selectedCard.classList.remove('border-gray-200', 'bg-white');
        selectedCard.classList.add('bg-[#F0FDF4]/70', 'border-emerald-400');

        const indicator = selectedCard.querySelector('.radio-indicator');
        if (indicator) {
            indicator.innerHTML = `
                <span class="w-4 h-4 rounded-full border-2 border-emerald-600 flex items-center justify-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                </span>
            `;
        }
    }
}

function openWhatsAppCategoryModal() {
    isIndividualSend = false;
    currentIndividualMember = null;
    currentIndividualTagihan = [];
    currentIndividualTotalPiutang = 0;

    const piutangCard = document.getElementById('catCard_pengingat_piutang');
    const piutangDesc = document.getElementById('catDesc_pengingat_piutang');
    const piutangBadge = document.getElementById('catBadge_pengingat_piutang');
    const piutangIcon = document.getElementById('catIcon_pengingat_piutang');

    if (piutangCard) {
        piutangCard.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-gray-100/90');
        piutangCard.title = '';
    }
    if (piutangDesc) {
        piutangDesc.textContent = 'Untuk member dengan piutang belum lunas';
        piutangDesc.classList.remove('text-gray-500', 'font-medium');
    }
    if (piutangBadge) piutangBadge.classList.add('hidden');
    if (piutangIcon) piutangIcon.className = 'w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mr-3 transition-colors';

    const checkedBoxes = getCheckedMemberBoxes();
    selectedMemberIds = checkedBoxes.map(cb => parseInt(cb.getAttribute('data-id'), 10)).filter(Boolean);

    if (selectedMemberIds.length === 0) {
        alert('Silakan centang minimal satu member terlebih dahulu.');
        return;
    }

    const countEl = document.getElementById('waCategoryMemberCount');
    if (countEl) countEl.textContent = `${selectedMemberIds.length} dipilih`;

    selectCategoryCard(currentKategoriPesan || 'pengingat_piutang');

    const modal = document.getElementById('whatsappCategoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function openIndividualWhatsAppModal(pelangganId) {
    isIndividualSend = true;
    currentPelangganId = pelangganId;
    selectedMemberIds = [pelangganId];

    const urlTemplate = @json(route('pelanggan.whatsapp-piutang.preview', ['pelanggan' => '__ID__']));
    const url = urlTemplate.replace('__ID__', encodeURIComponent(pelangganId));

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok && !data.pelanggan) {
            throw new Error(data.message || 'Gagal memuat data member.');
        }
        return data;
    })
    .then(data => {
        currentIndividualMember = data.pelanggan || { id: pelangganId, nama: 'Member', telepon: '', telepon_valid: false };
        currentIndividualTagihan = data.tagihan || [];
        currentIndividualTotalPiutang = data.total_piutang || 0;
        currentNamaApotek = data.nama_apotek || defaultNamaApotek;

        const countEl = document.getElementById('waCategoryMemberCount');
        if (countEl) {
            countEl.textContent = currentIndividualMember.nama;
        }

        const piutangCard = document.getElementById('catCard_pengingat_piutang');
        const piutangDesc = document.getElementById('catDesc_pengingat_piutang');
        const piutangBadge = document.getElementById('catBadge_pengingat_piutang');
        const piutangIcon = document.getElementById('catIcon_pengingat_piutang');

        const hasDebt = (currentIndividualTagihan.length > 0 && currentIndividualTotalPiutang > 0);

        if (!hasDebt) {
            if (piutangCard) {
                piutangCard.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-100/90');
                piutangCard.title = 'Tidak memiliki piutang aktif';
            }
            if (piutangDesc) {
                piutangDesc.textContent = 'Tidak memiliki piutang aktif';
                piutangDesc.classList.add('text-gray-500', 'font-medium');
            }
            if (piutangBadge) {
                piutangBadge.classList.remove('hidden');
            }
            if (piutangIcon) {
                piutangIcon.className = 'w-10 h-10 rounded-xl bg-gray-200 text-gray-400 flex items-center justify-center shrink-0 mr-3 transition-colors';
            }
            // Member tanpa utang langsung diarahkan ke Promosi / Diskon
            selectCategoryCard('promosi_diskon');
        } else {
            if (piutangCard) {
                piutangCard.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-gray-100/90');
                piutangCard.title = '';
            }
            if (piutangDesc) {
                piutangDesc.textContent = 'Untuk member dengan piutang belum lunas';
                piutangDesc.classList.remove('text-gray-500', 'font-medium');
            }
            if (piutangBadge) {
                piutangBadge.classList.add('hidden');
            }
            if (piutangIcon) {
                piutangIcon.className = 'w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mr-3 transition-colors';
            }
            selectCategoryCard('pengingat_piutang');
        }

        const modal = document.getElementById('whatsappCategoryModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    })
    .catch(err => {
        alert('Gagal memuat data member untuk WhatsApp: ' + (err.message || 'Kesalahan sistem'));
    });
}

function openIndividualWhatsAppCategory(pelangganId) {
    openIndividualWhatsAppModal(pelangganId);
}

function closeWhatsAppCategoryModal() {
    const modal = document.getElementById('whatsappCategoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function lanjutKeEditorPesan() {
    closeWhatsAppCategoryModal();
    openWhatsAppEditorModal();
}

function kembaliKeKategoriModal() {
    closeWhatsAppEditorModal();
    const modal = document.getElementById('whatsappCategoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

/* ==========================================================
 * 4. LOGIKA POPUP 2: EDITOR & TEMPLATE (REFERENSI GAMBAR 2)
 * ========================================================== */
function openWhatsAppEditorModal() {
    const modal = document.getElementById('whatsappEditorModal');
    const catNameEl = document.getElementById('waEditorCategoryName');
    const catIconEl = document.getElementById('waEditorCatIconBadge');
    const piutangChips = document.getElementById('waPiutangPlaceholderChips');

    const memberCard = document.getElementById('waEditorMemberInfoCard');
    const memberNama = document.getElementById('waEditorMemberNama');
    const memberTelepon = document.getElementById('waEditorMemberTelepon');
    const phoneWarning = document.getElementById('waEditorPhoneWarning');

    const invoiceSection = document.getElementById('waEditorInvoiceSection');
    const btnSubmit = document.getElementById('btnEditorActionSubmit');
    const btnSubmitText = document.getElementById('btnEditorActionSubmitText');

    const alertBox = document.getElementById('waEditorAlertBox');
    if (alertBox) {
        alertBox.classList.add('hidden');
        alertBox.textContent = '';
    }

    const meta = categoryMeta[currentKategoriPesan] || categoryMeta['pengingat_piutang'];

    if (catNameEl) catNameEl.textContent = meta.name;
    if (catIconEl) catIconEl.innerHTML = meta.iconHtml;

    if (isIndividualSend && currentIndividualMember) {
        // Tampilkan Identitas Member
        if (memberCard) memberCard.classList.remove('hidden');
        if (memberNama) memberNama.textContent = currentIndividualMember.nama || '-';
        if (memberTelepon) memberTelepon.textContent = currentIndividualMember.telepon || '(Nomor belum diisi)';

        const isPhoneValid = Boolean(currentIndividualMember.telepon && currentIndividualMember.telepon_valid);
        if (phoneWarning) {
            if (!isPhoneValid) {
                phoneWarning.classList.remove('hidden');
            } else {
                phoneWarning.classList.add('hidden');
            }
        }

        // Setup bagian faktur tagihan untuk Pengingat Piutang
        if (invoiceSection) {
            if (currentKategoriPesan === 'pengingat_piutang') {
                invoiceSection.classList.remove('hidden');
                setupEditorInvoiceDropdown();
            } else {
                invoiceSection.classList.add('hidden');
            }
        }

        if (btnSubmitText) btnSubmitText.textContent = 'Kirim WhatsApp';
        if (btnSubmit) {
            const canSend = isPhoneValid && (currentKategoriPesan !== 'pengingat_piutang' || currentIndividualTagihan.length > 0);
            btnSubmit.disabled = !canSend;
            if (!canSend) {
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    } else {
        // Mode Pengiriman Massal
        if (memberCard) memberCard.classList.add('hidden');
        if (invoiceSection) invoiceSection.classList.add('hidden');
        if (btnSubmitText) btnSubmitText.textContent = 'Pratinjau & Ringkasan';
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Tampilkan chip piutang hanya untuk kategori pengingat piutang
    if (piutangChips) {
        if (currentKategoriPesan === 'pengingat_piutang') {
            piutangChips.classList.remove('hidden');
        } else {
            piutangChips.classList.add('hidden');
        }
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    loadTemplatesForCategory(currentKategoriPesan);
}

function setupEditorInvoiceDropdown() {
    const select = document.getElementById('waEditorInvoiceSelect');
    const badge = document.getElementById('waEditorInvoiceBadge');
    const noDebtAlert = document.getElementById('waEditorNoDebtAlert');
    const summary = document.getElementById('waEditorInvoiceSummary');
    const btnSubmit = document.getElementById('btnEditorActionSubmit');

    if (!select) return;

    select.innerHTML = '';

    if (!currentIndividualTagihan || currentIndividualTagihan.length === 0) {
        select.classList.add('hidden');
        if (badge) badge.textContent = '0 Faktur Belum Lunas';
        if (noDebtAlert) noDebtAlert.classList.remove('hidden');
        if (summary) summary.classList.add('hidden');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        }
        return;
    }

    select.classList.remove('hidden');
    if (noDebtAlert) noDebtAlert.classList.add('hidden');
    if (summary) summary.classList.remove('hidden');
    if (badge) badge.textContent = `${currentIndividualTagihan.length} Faktur Belum Lunas`;

    // 1. Opsi "Semua faktur belum lunas" (jika > 1 faktur)
    if (currentIndividualTagihan.length > 1) {
        const optAll = document.createElement('option');
        optAll.value = 'all';
        optAll.textContent = `★ Semua Faktur Belum Lunas (${currentIndividualTagihan.length} Faktur — Total ${formatRupiahWhatsApp(currentIndividualTotalPiutang)})`;
        select.appendChild(optAll);
    }

    // 2. Opsi masing-masing faktur
    currentIndividualTagihan.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.penjualan_id;
        opt.textContent = `Faktur #${t.no_faktur} — ${t.sisa_piutang_formatted} (Jatuh tempo: ${t.jatuh_tempo})`;
        select.appendChild(opt);
    });

    select.selectedIndex = 0;
    handleEditorInvoiceChange();
}

function handleEditorInvoiceChange() {
    const select = document.getElementById('waEditorInvoiceSelect');
    const summaryNoFaktur = document.getElementById('waEditorSummaryNoFaktur');
    const summarySisa = document.getElementById('waEditorSummarySisa');
    if (!select) return;

    const val = select.value;

    let selectedSisa = 0;
    let selectedNoFaktur = '';
    let selectedJatuhTempo = '';

    if (val === 'all') {
        selectedSisa = currentIndividualTotalPiutang;
        selectedNoFaktur = `Semua faktur (${currentIndividualTagihan.length} faktur)`;
        selectedJatuhTempo = 'Bervariasi';
    } else {
        const tagihan = currentIndividualTagihan.find(t => String(t.penjualan_id) === String(val));
        if (tagihan) {
            selectedSisa = tagihan.sisa_piutang;
            selectedNoFaktur = '#' + tagihan.no_faktur;
            selectedJatuhTempo = tagihan.jatuh_tempo;
        }
    }

    if (summaryNoFaktur) summaryNoFaktur.textContent = selectedNoFaktur;
    if (summarySisa) summarySisa.textContent = formatRupiahWhatsApp(selectedSisa);

    const textarea = document.getElementById('waEditorPesanText');
    if (textarea && currentKategoriPesan === 'pengingat_piutang') {
        if (val === 'all') {
            textarea.value = `Yth. Bapak/Ibu {nama_member},\n\nKami dari {nama_apotek} ingin menginformasikan bahwa terdapat ${currentIndividualTagihan.length} tagihan faktur belum lunas dengan total sisa piutang sebesar {total_piutang}.\n\nMohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku. Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\nTerima kasih atas perhatian dan kerja samanya.\n\nSalam,\n{nama_apotek}`;
        } else {
            textarea.value = `Yth. Bapak/Ibu {nama_member},\n\nKami dari {nama_apotek} ingin mengingatkan bahwa masih terdapat piutang sebesar {total_piutang} pada faktur {nomor_faktur} dengan tanggal jatuh tempo {tanggal_jatuh_tempo}.\n\nMohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku. Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\nTerima kasih atas perhatian dan kerja samanya.\n\nSalam,\n{nama_apotek}`;
        }
        updateCharCount();
    }
}

function handleEditorSubmit() {
    if (isIndividualSend) {
        bukaKonfirmasiPengirimanIndividual();
    } else {
        lanjutKePratinjauMassal();
    }
}

function bukaKonfirmasiPengirimanIndividual() {
    if (!currentPelangganId || !currentIndividualMember) {
        alert('Data member tidak ditemukan.');
        return;
    }

    if (!currentIndividualMember.telepon || !currentIndividualMember.telepon_valid) {
        alert('Nomor WhatsApp member tidak valid atau belum terdaftar pada data master.');
        return;
    }

    const rawPesan = document.getElementById('waEditorPesanText')?.value.trim();
    if (!rawPesan) {
        alert('Isi pesan tidak boleh kosong.');
        return;
    }

    let selectedNoFaktur = '-';
    let selectedSisaFormatted = '-';
    let selectedJatuhTempo = '-';

    if (currentKategoriPesan === 'pengingat_piutang') {
        if (!currentIndividualTagihan || currentIndividualTagihan.length === 0 || currentIndividualTotalPiutang <= 0) {
            alert('Member ini tidak memiliki sisa piutang aktif yang belum lunas.');
            return;
        }

        const select = document.getElementById('waEditorInvoiceSelect');
        const val = select ? select.value : '';
        if (!val) {
            alert('Silakan pilih faktur tagihan terlebih dahulu.');
            return;
        }

        if (val === 'all') {
            selectedNoFaktur = `Semua Faktur Belum Lunas (${currentIndividualTagihan.length} Faktur)`;
            selectedSisaFormatted = formatRupiahWhatsApp(currentIndividualTotalPiutang);
            selectedJatuhTempo = 'Bervariasi';
        } else {
            const tagihan = currentIndividualTagihan.find(t => String(t.penjualan_id) === String(val));
            if (!tagihan) {
                alert('Faktur tagihan yang dipilih tidak valid.');
                return;
            }
            selectedNoFaktur = '#' + tagihan.no_faktur;
            selectedSisaFormatted = tagihan.sisa_piutang_formatted || formatRupiahWhatsApp(tagihan.sisa_piutang);
            selectedJatuhTempo = tagihan.jatuh_tempo || '-';
        }
    }

    // Ganti placeholder dengan nilai aktual
    let finalPesan = rawPesan
        .replace(/\{nama_member\}/g, currentIndividualMember.nama || 'Member')
        .replace(/\{nama_apotek\}/g, currentNamaApotek || defaultNamaApotek);

    if (currentKategoriPesan === 'pengingat_piutang') {
        finalPesan = finalPesan
            .replace(/\{total_piutang\}/g, selectedSisaFormatted)
            .replace(/\{nomor_faktur\}/g, selectedNoFaktur)
            .replace(/\{tanggal_jatuh_tempo\}/g, selectedJatuhTempo);
    } else {
        finalPesan = finalPesan
            .replace(/\{total_piutang\}/g, '-')
            .replace(/\{nomor_faktur\}/g, '-')
            .replace(/\{tanggal_jatuh_tempo\}/g, '-');
    }

    // Populate modal konfirmasi
    document.getElementById('waConfirmNama').textContent = currentIndividualMember.nama || '-';
    document.getElementById('waConfirmTelepon').textContent = currentIndividualMember.telepon || '-';

    const piutangRows = document.getElementById('waConfirmPiutangRows');
    const nonPiutangRows = document.getElementById('waConfirmNonPiutangRows');

    if (currentKategoriPesan === 'pengingat_piutang') {
        if (piutangRows) piutangRows.classList.remove('hidden');
        if (nonPiutangRows) nonPiutangRows.classList.add('hidden');

        document.getElementById('waConfirmNoFaktur').textContent = selectedNoFaktur;
        document.getElementById('waConfirmSisa').textContent = selectedSisaFormatted;
        document.getElementById('waConfirmJatuhTempo').textContent = selectedJatuhTempo;
    } else {
        if (piutangRows) piutangRows.classList.add('hidden');
        if (nonPiutangRows) nonPiutangRows.classList.remove('hidden');

        const meta = categoryMeta[currentKategoriPesan] || { name: 'Pesan Custom', badgeClass: 'bg-gray-100 text-gray-700' };
        const jenisPesanEl = document.getElementById('waConfirmJenisPesan');
        if (jenisPesanEl) {
            jenisPesanEl.textContent = meta.name;
            jenisPesanEl.className = `inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-xs ${meta.badgeClass}`;
        }

        const templateSelect = document.getElementById('waTemplateSelect');
        const templateName = templateSelect && templateSelect.selectedOptions.length > 0 && templateSelect.value
            ? templateSelect.selectedOptions[0].textContent
            : 'Pesan Custom';
        document.getElementById('waConfirmNamaTemplate').textContent = templateName;
    }

    currentFinalPesan = finalPesan;
    document.getElementById('waConfirmPesan').textContent = finalPesan;

    const confirmError = document.getElementById('waConfirmError');
    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const modalConfirm = document.getElementById('whatsappPiutangConfirmModal');
    if (modalConfirm) {
        modalConfirm.classList.remove('hidden');
        modalConfirm.classList.add('flex');
    }
}

function eksekusiKirimIndividualWhatsApp() {
    if (!currentPelangganId) {
        alert('Data member tidak ditemukan.');
        return;
    }

    if (!currentIndividualMember || !currentIndividualMember.telepon || !currentIndividualMember.telepon_valid) {
        alert('Nomor WhatsApp member tidak valid atau belum terdaftar pada data master.');
        return;
    }

    const rawPesan = document.getElementById('waEditorPesanText')?.value.trim();
    if (!rawPesan) {
        alert('Isi pesan tidak boleh kosong.');
        return;
    }

    let penjualanId = null;
    if (currentKategoriPesan === 'pengingat_piutang') {
        if (!currentIndividualTagihan || currentIndividualTagihan.length === 0 || currentIndividualTotalPiutang <= 0) {
            alert('Member ini tidak memiliki sisa piutang aktif yang belum lunas.');
            return;
        }
        const select = document.getElementById('waEditorInvoiceSelect');
        penjualanId = select ? select.value : null;
        if (!penjualanId) {
            alert('Silakan pilih faktur tagihan terlebih dahulu.');
            return;
        }
    }

    const btnKirim = document.getElementById('btnEksekusiKirimWhatsApp');
    const btnBatal = document.getElementById('btnBatalKirimWhatsApp');
    const btnBatalX = document.getElementById('btnBatalKirimWhatsAppX');
    const btnText = document.getElementById('btnEksekusiKirimWhatsAppText');
    const confirmError = document.getElementById('waConfirmError');
    const alertBox = document.getElementById('waEditorAlertBox');

    // Pencegahan klik ganda & indikator loading
    if (btnKirim) {
        btnKirim.disabled = true;
        btnKirim.classList.add('opacity-60', 'cursor-not-allowed');
    }
    if (btnBatal) btnBatal.disabled = true;
    if (btnBatalX) btnBatalX.disabled = true;
    if (btnText) btnText.textContent = 'Sedang mengirim...';

    if (confirmError) {
        confirmError.textContent = '';
        confirmError.classList.add('hidden');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const urlTemplate = @json(route('pelanggan.whatsapp-piutang.send', ['pelanggan' => '__ID__']));
    const url = urlTemplate.replace('__ID__', encodeURIComponent(currentPelangganId));

    const tmplSelect = document.getElementById('waTemplateSelect');
    const selectedTemplateName = tmplSelect && tmplSelect.selectedIndex >= 0 && tmplSelect.value ? tmplSelect.options[tmplSelect.selectedIndex].text.trim() : null;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            kategori: currentKategoriPesan,
            penjualan_id: penjualanId,
            pesan: (currentFinalPesan || rawPesan),
            nama_template: selectedTemplateName
        })
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok || !data.status) {
            throw new Error(data.message || 'Gagal mengirim pesan WhatsApp.');
        }
        return data;
    })
    .then(data => {
        closeWhatsAppPiutangConfirm();

        if (alertBox) {
            alertBox.className = 'rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs sm:text-sm font-semibold text-emerald-800 flex items-center gap-2';
            alertBox.innerHTML = `
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>${escapeHtmlWhatsApp(data.message || 'Pesan WhatsApp berhasil dikirim!')}</span>
            `;
            alertBox.classList.remove('hidden');
        }

        setTimeout(() => {
            closeWhatsAppEditorModal();
        }, 1500);
    })
    .catch(err => {
        if (confirmError) {
            confirmError.textContent = err.message || 'Terjadi kesalahan sistem saat mengirim pesan.';
            confirmError.classList.remove('hidden');
        } else {
            alert(err.message || 'Gagal mengirim pesan WhatsApp.');
        }
    })
    .finally(() => {
        if (btnKirim) {
            btnKirim.disabled = false;
            btnKirim.classList.remove('opacity-60', 'cursor-not-allowed');
        }
        if (btnBatal) btnBatal.disabled = false;
        if (btnBatalX) btnBatalX.disabled = false;
        if (btnText) btnText.textContent = 'Ya, Kirim Sekarang';
    });
}

function closeWhatsAppEditorModal() {
    const modal = document.getElementById('whatsappEditorModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function updateCharCount() {
    const textarea = document.getElementById('waEditorPesanText');
    const countEl = document.getElementById('waCharCountText');
    if (textarea && countEl) {
        const len = textarea.value.length;
        countEl.textContent = `${len}/500`;
        if (len > 500) {
            countEl.classList.add('text-amber-600', 'font-bold');
            countEl.classList.remove('text-gray-400');
        } else {
            countEl.classList.remove('text-amber-600', 'font-bold');
            countEl.classList.add('text-gray-400');
        }
    }
}

function togglePlaceholderAccordion() {
    const content = document.getElementById('placeholderAccordionContent');
    const chevron = document.getElementById('placeholderChevronIcon');
    if (!content) return;

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-90');
    } else {
        content.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-90');
    }
}

function insertPlaceholder(placeholder) {
    const textarea = document.getElementById('waEditorPesanText');
    if (!textarea) return;

    // Sisipkan pada posisi kursor pengguna
    const startPos = textarea.selectionStart ?? textarea.value.length;
    const endPos = textarea.selectionEnd ?? textarea.value.length;
    const text = textarea.value;

    textarea.value = text.substring(0, startPos) + placeholder + text.substring(endPos, text.length);
    textarea.selectionStart = startPos + placeholder.length;
    textarea.selectionEnd = startPos + placeholder.length;
    textarea.focus();
    updateCharCount();
}

function loadTemplatesForCategory(kategori, preserveExistingText = false) {
    const select = document.getElementById('waTemplateSelect');
    if (!select) return;

    select.innerHTML = '<option value="">-- Memuat template... --</option>';

    fetch(`{{ route('whatsapp-templates.index') }}?kategori=${encodeURIComponent(kategori)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        availableTemplates = data.templates || [];
        select.innerHTML = '';

        if (availableTemplates.length === 0) {
            select.innerHTML = '<option value="">(Belum ada template untuk kategori ini)</option>';
            if (!preserveExistingText) {
                document.getElementById('waEditorPesanText').value = '';
                updateCharCount();
            }
            return;
        }

        availableTemplates.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = `${t.nama} ${t.is_default ? '' : '(Custom)'}`;
            select.appendChild(opt);
        });

        if (!preserveExistingText) {
            select.value = availableTemplates[0].id;
            applySelectedTemplate();
        }
    })
    .catch(err => {
        select.innerHTML = '<option value="">Gagal memuat template</option>';
    });
}

function applySelectedTemplate() {
    const select = document.getElementById('waTemplateSelect');
    const textarea = document.getElementById('waEditorPesanText');
    if (!select || !textarea) return;

    const tmplId = parseInt(select.value, 10);
    const tmpl = availableTemplates.find(t => t.id === tmplId);

    if (tmpl) {
        textarea.value = tmpl.konten;
    }
    updateCharCount();
}

/* ==========================================================
 * 5. LOGIKA POPUP 3: TEMPLATE PESAN (SATU TABEL TERPADU)
 * ========================================================== */
let allLoadedTemplates = [];

function openTemplateManageModal(source = 'category') {
    templateManageSource = source || 'category';
    const modal = document.getElementById('whatsappTemplateManageModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    const filterSelect = document.getElementById('tmplFilterKategori');
    if (filterSelect) {
        filterSelect.value = 'all';
    }
    const footerBtnText = document.getElementById('tmplFooterBtnText');
    if (footerBtnText) {
        footerBtnText.textContent = (templateManageSource === 'category') ? 'Kembali ke Pilihan Kategori' : 'Kembali ke Editor';
    }
    toggleTemplateForm(false);
    fetchAllTemplates();
}

function closeTemplateManageModal() {
    const modal = document.getElementById('whatsappTemplateManageModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    if (templateManageSource === 'category') {
        const catModal = document.getElementById('whatsappCategoryModal');
        if (catModal) {
            catModal.classList.remove('hidden');
            catModal.classList.add('flex');
        }
    } else {
        const editorModal = document.getElementById('whatsappEditorModal');
        if (editorModal) {
            editorModal.classList.remove('hidden');
            editorModal.classList.add('flex');
        }
        // Refresh pilihan template di dropdown editor pesan TANPA menghapus teks yang sedang diketik
        loadTemplatesForCategory(currentKategoriPesan, true);
    }
}

function filterTemplatesTable() {
    renderTemplatesTable();
}

function openTemplateFormModal(isEdit = false) {
    const errorBox = document.getElementById('tmplFormError');
    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }

    if (!isEdit) {
        document.getElementById('tmplEditId').value = '';
        document.getElementById('tmplInputNama').value = '';
        document.getElementById('tmplInputKategori').value = currentKategoriPesan || 'pengingat_piutang';
        document.getElementById('tmplInputKonten').value = '';
        document.getElementById('tmplFormTitle').textContent = 'Tambah Template WhatsApp';
        const btnText = document.getElementById('btnSimpanTemplateText');
        if (btnText) btnText.textContent = 'Simpan Template';
    }

    const modal = document.getElementById('whatsappTemplateFormModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeTemplateFormModal() {
    const modal = document.getElementById('whatsappTemplateFormModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    const errorBox = document.getElementById('tmplFormError');
    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }
}

function toggleTemplateForm(forceShow) {
    if (forceShow === false) {
        closeTemplateFormModal();
    } else {
        openTemplateFormModal(false);
    }
}

function resetTemplateForm() {
    document.getElementById('tmplEditId').value = '';
    document.getElementById('tmplInputNama').value = '';
    document.getElementById('tmplInputKategori').value = currentKategoriPesan || 'pengingat_piutang';
    document.getElementById('tmplInputKonten').value = '';
    document.getElementById('tmplFormTitle').textContent = 'Tambah Template WhatsApp';
    const btnText = document.getElementById('btnSimpanTemplateText');
    if (btnText) btnText.textContent = 'Simpan Template';
}

function insertPlaceholderToTemplate(val) {
    const textarea = document.getElementById('tmplInputKonten');
    if (!textarea) return;

    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + val + text.substring(end);
    textarea.focus();
    const newPos = start + val.length;
    textarea.setSelectionRange(newPos, newPos);
}

function fetchAllTemplates() {
    const tbody = document.getElementById('tmplTableBody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-gray-400">Memuat template pesan...</td></tr>';

    fetch(`{{ route('whatsapp-templates.index') }}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        allLoadedTemplates = data.templates || [];
        renderTemplatesTable();
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-red-500">Gagal memuat template.</td></tr>';
    });
}

function renderTemplatesTable() {
    const tbody = document.getElementById('tmplTableBody');
    const filterSelect = document.getElementById('tmplFilterKategori');
    const countBadge = document.getElementById('tmplTotalCountBadge');
    if (!tbody) return;

    const selectedFilter = filterSelect ? filterSelect.value : 'all';

    let filtered = allLoadedTemplates;
    if (selectedFilter !== 'all') {
        filtered = filtered.filter(t => t.kategori === selectedFilter);
    }

    if (countBadge) {
        countBadge.textContent = `${filtered.length} Template`;
    }

    tbody.innerHTML = '';

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-gray-400">Tidak ada template yang sesuai dengan filter.</td></tr>`;
        return;
    }

    filtered.forEach(t => {
        const meta = categoryMeta[t.kategori] || {
            badgeClass: 'bg-gray-100 text-gray-700',
            badgeText: 'Umum'
        };

        const previewText = t.konten ? t.konten.replace(/[\r\n]+/g, ' ').substring(0, 60) + (t.konten.length > 60 ? '...' : '') : '-';

        const sumberBadge = t.is_default
            ? `<span class="px-2 py-0.5 rounded-md font-semibold text-[10px] bg-blue-100 text-blue-700">Bawaan</span>`
            : `<span class="px-2 py-0.5 rounded-md font-semibold text-[10px] bg-purple-100 text-purple-700">Custom</span>`;

        let aksiHtml = '';
        if (t.is_default) {
            aksiHtml = `
                <button type="button"
                    onclick="useOrCopyTemplate(${t.id})"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-200 text-blue-600 hover:bg-blue-50 transition text-[11px] font-semibold whitespace-nowrap"
                    title="Gunakan ke Editor">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Gunakan</span>
                </button>
            `;
        } else {
            aksiHtml = `
                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <button type="button"
                        onclick="useOrCopyTemplate(${t.id})"
                        class="p-1.5 rounded-lg border border-gray-200 text-emerald-600 hover:bg-emerald-50 transition"
                        title="Gunakan ke Editor">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button type="button"
                        onclick="editTemplateItem(${t.id})"
                        class="p-1.5 rounded-lg border border-gray-200 text-blue-600 hover:bg-blue-50 transition"
                        title="Edit Template">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button type="button"
                        onclick="hapusTemplateItem(${t.id})"
                        class="p-1.5 rounded-lg border border-gray-200 text-red-600 hover:bg-red-50 transition"
                        title="Hapus Template">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            `;
        }

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/60 transition';
        tr.innerHTML = `
            <td class="py-3 px-3.5 font-semibold text-gray-800 leading-snug break-words">
                <span class="block line-clamp-2" title="${escapeHtmlWhatsApp(t.nama)}">
                    ${escapeHtmlWhatsApp(t.nama)}
                </span>
            </td>
            <td class="py-3 px-3 text-center whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] ${meta.badgeClass}">
                    ${meta.badgeText}
                </span>
            </td>
            <td class="py-3 px-3 text-center whitespace-nowrap">
                ${sumberBadge}
            </td>
            <td class="py-3 px-3 text-gray-500 font-mono text-[11px]">
                <span class="block truncate max-w-[200px]" title="${escapeHtmlWhatsApp(t.konten || '')}">
                    ${escapeHtmlWhatsApp(previewText)}
                </span>
            </td>
            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                ${aksiHtml}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function useOrCopyTemplate(id) {
    const tmpl = allLoadedTemplates.find(t => t.id === id);
    if (!tmpl) return;

    if (templateManageSource === 'category') {
        currentKategoriPesan = tmpl.kategori || 'pesan_custom';
        closeTemplateManageModal();
        lanjutKeEditorPesan();
        setTimeout(() => {
            const textarea = document.getElementById('waEditorPesanText');
            if (textarea) {
                textarea.value = tmpl.konten;
                updateCharCount();
            }
        }, 100);
    } else {
        document.getElementById('waEditorPesanText').value = tmpl.konten;
        updateCharCount();
        closeTemplateManageModal();
    }
}

function editTemplateItem(id) {
    const tmpl = allLoadedTemplates.find(t => t.id === id);
    if (!tmpl) return;

    document.getElementById('tmplEditId').value = tmpl.id;
    document.getElementById('tmplInputNama').value = tmpl.nama || '';
    document.getElementById('tmplInputKategori').value = tmpl.kategori || 'pengingat_piutang';
    document.getElementById('tmplInputKonten').value = tmpl.konten || '';
    document.getElementById('tmplFormTitle').textContent = 'Edit Template: ' + tmpl.nama;
    const btnText = document.getElementById('btnSimpanTemplateText');
    if (btnText) btnText.textContent = 'Perbarui Template';

    openTemplateFormModal(true);
}

function simpanTemplateCustom() {
    const editId = document.getElementById('tmplEditId').value;
    const nama = document.getElementById('tmplInputNama').value.trim();
    const kategori = document.getElementById('tmplInputKategori').value;
    const konten = document.getElementById('tmplInputKonten').value.trim();
    const errorBox = document.getElementById('tmplFormError');
    const btnSimpan = document.getElementById('btnSimpanTemplateAction');
    const btnSimpanText = document.getElementById('btnSimpanTemplateText');
    const btnBatal = document.getElementById('btnBatalSimpanTemplate');

    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }

    if (!nama || !konten) {
        if (errorBox) {
            errorBox.textContent = 'Nama template dan isi pesan tidak boleh kosong.';
            errorBox.classList.remove('hidden');
        } else {
            alert('Nama template dan isi pesan tidak boleh kosong.');
        }
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const isEdit = Boolean(editId);

    const url = isEdit
        ? `{{ url('whatsapp-templates') }}/${encodeURIComponent(editId)}`
        : `{{ route('whatsapp-templates.store') }}`;

    const method = isEdit ? 'PUT' : 'POST';

    if (btnSimpan) {
        btnSimpan.disabled = true;
        btnSimpan.classList.add('opacity-60', 'cursor-not-allowed');
    }
    if (btnBatal) btnBatal.disabled = true;
    if (btnSimpanText) btnSimpanText.textContent = 'Menyimpan...';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ nama, kategori, konten })
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok || !data.status) {
            throw new Error(data.message || 'Gagal menyimpan template.');
        }
        return data;
    })
    .then(data => {
        closeTemplateFormModal();
        fetchAllTemplates();
    })
    .catch(err => {
        if (errorBox) {
            errorBox.textContent = err.message || 'Terjadi kesalahan sistem saat menyimpan template.';
            errorBox.classList.remove('hidden');
        } else {
            alert(err.message || 'Terjadi kesalahan sistem.');
        }
    })
    .finally(() => {
        if (btnSimpan) {
            btnSimpan.disabled = false;
            btnSimpan.classList.remove('opacity-60', 'cursor-not-allowed');
        }
        if (btnBatal) btnBatal.disabled = false;
        if (btnSimpanText) {
            btnSimpanText.textContent = isEdit ? 'Perbarui Template' : 'Simpan Template';
        }
    });
}

function hapusTemplateItem(id) {
    if (!confirm('Yakin ingin menghapus template custom ini?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`{{ url('whatsapp-templates') }}/${encodeURIComponent(id)}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok || !data.status) {
            throw new Error(data.message || 'Gagal menghapus template.');
        }
        return data;
    })
    .then(data => {
        fetchAllTemplates();
    })
    .catch(err => {
        alert(err.message || 'Gagal menghapus template.');
    });
}

/* ==========================================================
 * 6. LANGKAH RINGKASAN & PRATINJAU PENERIMA MASSAL
 * ========================================================== */
function lanjutKePratinjauMassal() {
    const pesanTemplate = document.getElementById('waEditorPesanText')?.value.trim();

    if (!pesanTemplate) {
        alert('Isi pesan tidak boleh kosong.');
        return;
    }

    if (currentKategoriPesan !== 'pengingat_piutang') {
        const hasPiutangPlaceholder = /\{(nomor_faktur|total_piutang|tanggal_jatuh_tempo)\}/.test(pesanTemplate);
        if (hasPiutangPlaceholder) {
            const konfirmasi = confirm('Peringatan: Pesan Anda memuat placeholder tagihan ({total_piutang}, {nomor_faktur}, atau {tanggal_jatuh_tempo}) padahal kategori pesan bukan Pengingat Piutang. Placeholder ini akan bernilai strip (-) untuk member yang tidak memiliki piutang. Lanjutkan?');
            if (!konfirmasi) return;
        }
    }

    closeWhatsAppEditorModal();
    openBulkWhatsAppConfirmModal();
}

function kembaliKeEditorPesan() {
    closeBulkWhatsAppConfirmModal();
    const modal = document.getElementById('whatsappEditorModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeBulkWhatsAppConfirmModal() {
    const modal = document.getElementById('bulkWhatsAppConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openBulkWhatsAppConfirmModal() {
    if (!selectedMemberIds || selectedMemberIds.length === 0) {
        const checkedBoxes = getCheckedMemberBoxes();
        selectedMemberIds = checkedBoxes.map(cb => parseInt(cb.getAttribute('data-id'), 10)).filter(Boolean);
    }

    if (selectedMemberIds.length === 0) {
        alert('Silakan pilih minimal satu member terlebih dahulu.');
        return;
    }

    const modal = document.getElementById('bulkWhatsAppConfirmModal');
    const tableBody = document.getElementById('bulkConfirmTableBody');
    const previewText = document.getElementById('bulkConfirmPreviewText');
    const noticeBox = document.getElementById('bulkConfirmNotice');
    const skippedList = document.getElementById('bulkSkippedList');
    const btnStart = document.getElementById('btnStartBulkSend');

    const pesanTemplate = document.getElementById('waEditorPesanText')?.value || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-gray-400">Memvalidasi data penerima...</td></tr>`;
    previewText.textContent = 'Menyiapkan pratinjau pesan...';
    if (noticeBox) noticeBox.classList.add('hidden');
    if (skippedList) skippedList.innerHTML = '';
    if (btnStart) btnStart.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(@json(route('pelanggan.whatsapp-piutang.bulk-preview')), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            pelanggan_ids: selectedMemberIds,
            kategori: currentKategoriPesan,
            pesan_template: pesanTemplate
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok && !data.penerima) {
            throw new Error(data.message || 'Gagal memuat pratinjau pengiriman massal.');
        }
        return data;
    })
    .then(data => {
        currentBulkRecipients = data.penerima || [];
        currentBulkDikeluarkan = data.dikeluarkan || [];

        // Update Statistik
        document.getElementById('bulkStatDipilih').textContent = data.total_dipilih || selectedMemberIds.length;
        document.getElementById('bulkStatEligible').textContent = currentBulkRecipients.length;
        document.getElementById('bulkStatDikeluarkan').textContent = currentBulkDikeluarkan.length;

        // Render Penerima
        tableBody.innerHTML = '';
        if (currentBulkRecipients.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-red-600 font-semibold">Tidak ada member terpilih yang memenuhi syarat penerimaan pesan untuk kategori ini.</td></tr>`;
            previewText.textContent = '-';
            if (btnStart) btnStart.disabled = true;
        } else {
            currentBulkRecipients.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="py-2 px-3 font-medium text-gray-800">${escapeHtmlWhatsApp(item.nama)}</td>
                    <td class="py-2 px-3 font-mono text-emerald-700 font-semibold">${escapeHtmlWhatsApp(item.telepon || '-')}</td>
                    <td class="py-2 px-3 text-right font-bold text-red-600">${escapeHtmlWhatsApp(item.sisa_piutang_formatted)}</td>
                    <td class="py-2 px-3 text-center text-gray-600">${escapeHtmlWhatsApp(item.jatuh_tempo)}</td>
                `;
                tableBody.appendChild(tr);
            });

            previewText.textContent = currentBulkRecipients[0].pesan;
            if (btnStart) btnStart.disabled = false;
        }

        // Render Notice Member Dilewati
        if (currentBulkDikeluarkan.length > 0 && noticeBox && skippedList) {
            skippedList.innerHTML = '';
            currentBulkDikeluarkan.forEach(d => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${escapeHtmlWhatsApp(d.nama)}:</strong> ${escapeHtmlWhatsApp(d.alasan)}`;
                skippedList.appendChild(li);
            });
            noticeBox.classList.remove('hidden');
        }
    })
    .catch(error => {
        tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-red-600 font-semibold">${escapeHtmlWhatsApp(error.message)}</td></tr>`;
        previewText.textContent = '-';
        if (btnStart) btnStart.disabled = true;
    });
}

/* ==========================================================
 * 7. EKSEKUSI PENGIRIMAN MASSAL (PROGRESS & RATE LIMIT)
 * ========================================================== */
function closeBulkWhatsAppProgressModal() {
    const modal = document.getElementById('bulkWhatsAppProgressModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function closeBulkWhatsAppResultModal() {
    const modal = document.getElementById('bulkWhatsAppResultModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    // Muat ulang halaman agar data tetap sinkron
    window.location.reload();
}

async function startBulkWhatsAppSending() {
    if (!currentBulkRecipients || currentBulkRecipients.length === 0) {
        alert('Tidak ada penerima yang siap diproses.');
        return;
    }

    closeBulkWhatsAppConfirmModal();

    const progressModal = document.getElementById('bulkWhatsAppProgressModal');
    const progressList = document.getElementById('bulkProgressList');
    const headerTotal = document.getElementById('bulkProgressHeaderTotal');
    const barFill = document.getElementById('bulkProgressBarFill');
    const doneText = document.getElementById('bulkProgressDoneText');
    const percentText = document.getElementById('bulkProgressPercentText');

    if (!progressModal || !progressList) return;

    const total = currentBulkRecipients.length;
    if (headerTotal) headerTotal.textContent = total;
    if (doneText) doneText.textContent = `0 dari ${total} selesai`;
    if (percentText) percentText.textContent = '0%';
    if (barFill) barFill.style.width = '0%';

    progressList.innerHTML = '';
    currentBulkRecipients.forEach(item => {
        const row = document.createElement('div');
        row.id = `bulkProgressItem_${item.id}`;
        row.className = 'flex items-center justify-between p-2.5 rounded-xl border border-gray-150 bg-gray-50 text-xs';
        row.innerHTML = `
            <div class="flex flex-col min-w-0 pr-2">
                <span class="font-bold text-gray-800 truncate">${escapeHtmlWhatsApp(item.nama)}</span>
                <span class="text-gray-500 font-mono text-[11px]">${escapeHtmlWhatsApp(item.telepon || '-')} &bull; ${escapeHtmlWhatsApp(item.sisa_piutang_formatted)}</span>
            </div>
            <div id="bulkProgressBadge_${item.id}" class="shrink-0">
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-200 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">
                    Menunggu
                </span>
            </div>
        `;
        progressList.appendChild(row);
    });

    progressModal.classList.remove('hidden');
    progressModal.classList.add('flex');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const sendUrlTemplate = @json(route('pelanggan.whatsapp-piutang.bulk-send', ['pelanggan' => '__ID__']));

    let successCount = 0;
    let failedCount = 0;

    // Loop sekuensial dengan rate limit 1000ms
    for (let i = 0; i < total; i++) {
        const item = currentBulkRecipients[i];
        const badgeEl = document.getElementById(`bulkProgressBadge_${item.id}`);

        if (badgeEl) {
            badgeEl.innerHTML = `
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700 animate-pulse">
                    <svg class="animate-spin h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Mengirim...
                </span>
            `;
        }

        const itemRow = document.getElementById(`bulkProgressItem_${item.id}`);
        if (itemRow) {
            itemRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        const url = sendUrlTemplate.replace('__ID__', encodeURIComponent(item.id));

        const tmplSelect = document.getElementById('waTemplateSelect');
        const selectedTemplateName = tmplSelect && tmplSelect.selectedIndex >= 0 && tmplSelect.value ? tmplSelect.options[tmplSelect.selectedIndex].text.trim() : null;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    kategori: currentKategoriPesan,
                    pesan: item.pesan,
                    nama_template: selectedTemplateName
                })
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.status) {
                successCount++;
                if (badgeEl) {
                    badgeEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Berhasil
                        </span>
                    `;
                }
            } else {
                failedCount++;
                const errMsg = data.message || 'Gagal terkirim';
                if (badgeEl) {
                    badgeEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-semibold text-red-700" title="${escapeHtmlWhatsApp(errMsg)}">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Gagal
                        </span>
                    `;
                }
            }
        } catch (err) {
            failedCount++;
            if (badgeEl) {
                badgeEl.innerHTML = `
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-semibold text-red-700" title="${escapeHtmlWhatsApp(err.message || 'Koneksi error')}">
                        Gagal
                    </span>
                `;
            }
        }

        const processed = i + 1;
        const percent = Math.round((processed / total) * 100);
        if (barFill) barFill.style.width = `${percent}%`;
        if (doneText) doneText.textContent = `${processed} dari ${total} selesai`;
        if (percentText) percentText.textContent = `${percent}%`;

        // Rate limit 1 detik jeda antar request
        if (i < total - 1) {
            await new Promise(r => setTimeout(r, 1000));
        }
    }

    await new Promise(r => setTimeout(r, 600));

    closeBulkWhatsAppProgressModal();

    const resultModal = document.getElementById('bulkWhatsAppResultModal');
    const resultTotal = document.getElementById('bulkResultTotal');
    const resultSuccess = document.getElementById('bulkResultSuccess');
    const resultFailed = document.getElementById('bulkResultFailed');
    const resultSkipped = document.getElementById('bulkResultSkipped');

    const totalSelected = selectedMemberIds.length;
    if (resultTotal) resultTotal.textContent = totalSelected;
    if (resultSuccess) resultSuccess.textContent = successCount;
    if (resultFailed) resultFailed.textContent = failedCount;
    if (resultSkipped) resultSkipped.textContent = currentBulkDikeluarkan.length;

    if (resultModal) {
        resultModal.classList.remove('hidden');
        resultModal.classList.add('flex');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updateBulkActionBar();
});
</script>