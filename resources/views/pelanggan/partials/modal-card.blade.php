@php
    $apotek = \Illuminate\Support\Facades\Cache::remember('info_apotek', now()->addHours(6), fn () => \App\Models\InfoApotek::first()) ?? \App\Models\InfoApotek::singleton();
    $logoUrl = ($apotek && $apotek->logo) ? asset('storage/' . $apotek->logo) : null;
    $namaApotek = $apotek?->nama_apotek ?: (config('app.name') ?: 'POS Apotek');
    $teleponApotek = $apotek?->telepon ?: '';
    $emailApotek = $apotek?->email ?: '';
    $alamatApotek = $apotek?->alamat_lengkap ?: ($apotek?->alamat ?: '');
@endphp

<!-- Modal Kartu Member -->
<div id="modal-card" class="modal-card-backdrop hidden">
    <div class="modal-card-box">
        
        <!-- Modal Header -->
        <div class="modal-card-header no-print">
            <div>
                <h3 class="modal-card-title flex items-center gap-2">
                    <x-heroicon-o-identification class="w-5 h-5 text-blue-600 inline-block" />
                    <span>Kartu Member Pelanggan</span>
                </h3>
                <p class="modal-card-subtitle">
                    Format fisik standar kartu ATM / ID Card (86 mm × 54 mm)
                </p>
            </div>
            
            <div class="modal-card-header-actions">
                <!-- Filter Tampilan: Kedua Sisi, Depan Saja, Belakang Saja -->
                <div class="card-print-scope-toggle">
                    <button type="button" onclick="setPrintSide('both')" id="btn-side-both" class="scope-btn active">
                        Kedua Sisi
                    </button>
                    <button type="button" onclick="setPrintSide('front')" id="btn-side-front" class="scope-btn">
                        Depan Saja
                    </button>
                    <button type="button" onclick="setPrintSide('back')" id="btn-side-back" class="scope-btn">
                        Belakang Saja
                    </button>
                </div>

                <!-- Close Button -->
                <button type="button" onclick="closeCardModal()" class="btn-modal-icon-close" title="Tutup">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Modal Body / Print Container -->
        <div class="modal-card-body" id="card-print-container">
            
            <!-- 1. KARTU SISI DEPAN -->
            <div class="card-column" id="card-column-front">
                <div class="pos-card-wrapper pos-card-front" id="card-wrapper-front">
                    
                    <!-- Watermark Logo Palang Farmasi + Daun (Subtle Pastel Blue) -->
                    <div class="card-watermark">
                        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M38 10 C38 6 42 2 46 2 L54 2 C58 2 62 6 62 10 L62 38 L90 38 C94 38 98 42 98 46 L98 54 C98 58 94 62 90 62 L62 62 L62 90 C62 94 58 98 54 98 L46 98 C42 98 38 94 38 90 L38 62 L10 62 C6 62 2 58 2 54 L2 46 C2 42 6 38 10 38 L38 38 Z" fill="#dbeafe"/>
                            <path d="M62 62 C78 48 88 64 98 64 C98 78 82 96 62 90 Z" fill="#93c5fd" opacity="0.8"/>
                            <path d="M62 62 Q78 72 88 80" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                        </svg>
                    </div>

                    <!-- Header: Logo, Nama Apotek & Badge Member -->
                    <div class="card-front-header">
                        <div class="card-brand">
                            <div class="card-brand-logo">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" 
                                         alt="Logo {{ $namaApotek }}" 
                                         onerror="this.style.display='none'; document.getElementById('card-logo-fallback').style.display='flex';">
                                    <div id="card-logo-fallback" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; color: #2563eb;">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 20px; height: 20px;">
                                            <path d="M10.5 4.5a1.5 1.5 0 0 1 3 0v4.5h4.5a1.5 1.5 0 0 1 0 3h-4.5v4.5a1.5 1.5 0 0 1-3 0v-4.5H6a1.5 1.5 0 0 1 0-3h4.5V4.5z"/>
                                        </svg>
                                    </div>
                                @else
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M10.5 4.5a1.5 1.5 0 0 1 3 0v4.5h4.5a1.5 1.5 0 0 1 0 3h-4.5v4.5a1.5 1.5 0 0 1-3 0v-4.5H6a1.5 1.5 0 0 1 0-3h4.5V4.5z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="card-brand-name" title="{{ $namaApotek }}">{{ $namaApotek }}</div>
                        </div>

                        <!-- Badge Jenis Member (Biru Selaras) -->
                        <div id="card-status-badge" class="card-member-badge badge-blue">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                            </svg>
                            <span id="card-status-member">MEMBER PELANGGAN TETAP</span>
                        </div>
                    </div>

                    <!-- Body: 3 Baris Informasi Tersusun Rapi (Dinamis) -->
                    <div class="card-front-content">
                        <!-- 1. Nama Pemegang -->
                        <div class="card-info-item with-divider">
                            <div class="card-info-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="card-info-texts">
                                <span class="card-info-label">Nama Pemegang</span>
                                <span id="card-name" class="card-info-value">-</span>
                            </div>
                        </div>

                        <!-- 2. Nomor Kartu / ID -->
                        <div class="card-info-item with-divider">
                            <div class="card-info-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div class="card-info-texts">
                                <span class="card-info-label">Nomor Kartu / ID</span>
                                <span id="card-id" class="card-info-value val-mono">-</span>
                            </div>
                        </div>

                        <!-- 3. Benefit Diskon -->
                        <div class="card-info-item">
                            <div class="card-info-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm7 7a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="card-info-texts">
                                <span class="card-info-label">Benefit Diskon</span>
                                <span id="card-discount" class="card-info-value val-discount">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Wave SVG (Layered Blue Curve) -->
                    <div class="card-bottom-waves">
                        <svg viewBox="0 0 400 52" preserveAspectRatio="none" fill="none">
                            <path d="M0 26 C90 12 180 38 290 22 C340 14 375 22 400 18 L400 52 L0 52 Z" fill="#93c5fd" opacity="0.6"/>
                            <path d="M0 32 C70 18 160 42 270 28 C325 20 370 30 400 24 L400 52 L0 52 Z" fill="#1d4ed8"/>
                        </svg>
                    </div>
                </div>

                <!-- Label Depan -->
                <div class="card-preview-pill">Depan</div>
            </div>

            <!-- 2. KARTU SISI BELAKANG -->
            <div class="card-column" id="card-column-back">
                <div class="pos-card-wrapper pos-card-back" id="card-wrapper-back">
                    
                    <!-- Top Barcode Container Box -->
                    <div class="card-back-barcode-box">
                        <div class="card-back-barcode-svg" id="card-barcode-svg"></div>
                        <span id="card-barcode-text" style="display: none;"></span>
                    </div>

                    <!-- Ketentuan Penggunaan (Nomor Otomatis 1 - 5) -->
                    <div class="card-back-terms">
                        <div class="card-terms-header">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="card-terms-title">Ketentuan Penggunaan</span>
                        </div>
                        <ol class="card-terms-ol">
                            <li><span>Kartu ini hanya berlaku di <strong style="color: #0f172a;">{{ $namaApotek }}</strong>.</span></li>
                            <li><span>Diskon berlaku untuk pembelian obat dan produk tertentu.</span></li>
                            <li><span>Kartu tidak dapat dipindah tangankan.</span></li>
                            <li><span>Harap tunjukkan kartu ini saat melakukan transaksi.</span></li>
                            <li><span>Informasi lebih lanjut hubungi kasir kami.</span></li>
                        </ol>
                    </div>

                    <!-- Divider -->
                    <div class="card-back-divider"></div>

                    <!-- Contact Info in Blue -->
                    <div class="card-back-contacts">
                        <div class="card-contact-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $teleponApotek ?: '(021) 1234 5678' }}</span>
                        </div>
                        <div class="card-contact-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span title="{{ $alamatApotek }}">{{ $alamatApotek ?: 'Jl. Sehat No. 10, Jakarta' }}</span>
                        </div>
                        <div class="card-contact-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $emailApotek ?: 'info@posapotek.com' }}</span>
                        </div>
                    </div>

                    <!-- Bottom Wave SVG (Matching Inspiration Image) -->
                    <div class="card-bottom-waves">
                        <svg viewBox="0 0 400 52" preserveAspectRatio="none" fill="none">
                            <path d="M0 24 C90 10 180 34 290 20 C340 12 375 20 400 16 L400 52 L0 52 Z" fill="#93c5fd" opacity="0.6"/>
                            <path d="M0 30 C70 16 160 38 270 24 C325 18 370 26 400 20 L400 52 L0 52 Z" fill="#1d4ed8"/>
                        </svg>
                    </div>
                </div>

                <!-- Label Belakang -->
                <div class="card-preview-pill">Belakang</div>
            </div>

        </div>

        <!-- Error State Container (Non-printable) -->
        <div id="card-qr-status-container" class="no-print hidden" style="padding: 0 28px 12px 28px; text-align: center;">
            <p id="card-qr-error" style="font-size: 12px; color: #d97706; font-weight: 600;"></p>
        </div>

        <!-- Modal Footer Actions -->
        <div class="modal-card-footer no-print">
            <span style="font-size: 12px; color: #64748b;">Siap dicetak ukuran kartu fisik 86 mm × 54 mm</span>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeCardModal()" class="btn-modal-close">
                    Tutup
                </button>
                <button type="button" id="btn-print-card" onclick="printCard()" class="btn-modal-print">
                    <x-heroicon-o-printer class="w-4 h-4 inline-block" />
                    <span>Cetak Kartu</span>
                </button>
            </div>
        </div>

    </div>
</div>

<style>
/* -------------------------------------------------------------
   SCOPED CARD MODAL STYLES (IMMUNE TO CSS CONFLICTS)
------------------------------------------------------------- */
.modal-card-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    padding: 20px;
    overflow-y: auto;
}

/* KUNCI: Jangan tampilkan modal saat ada class hidden */
.modal-card-backdrop.hidden,
#modal-card.hidden {
    display: none !important;
}

.modal-card-box {
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid #e2e8f0;
    width: 100%;
    max-width: 960px;
    overflow: hidden;
    margin: auto;
    display: flex;
    flex-direction: column;
}

.modal-card-header {
    padding: 16px 28px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fafafa;
}

.modal-card-title {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}

.modal-card-subtitle {
    font-size: 12px;
    color: #64748b;
    margin-top: 3px;
    line-height: 1.2;
}

.modal-card-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* FILTER TAMPILAN (TOGGLE) */
.card-print-scope-toggle {
    display: inline-flex;
    align-items: center;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 12px;
}

.scope-btn {
    border: none;
    background: transparent;
    padding: 6px 14px;
    border-radius: 9px;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}

.scope-btn:hover {
    color: #0f172a;
}

.scope-btn.active {
    background: #ffffff !important;
    color: #1d4ed8 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
}

/* Visibility classes for Filter (Preview & Print) */
.card-column.preview-hidden {
    display: none !important;
}

.btn-modal-icon-close {
    border: none;
    background: transparent;
    color: #94a3b8;
    padding: 6px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.btn-modal-icon-close:hover {
    background: #f1f5f9;
    color: #334155;
}

.modal-card-body {
    padding: 30px 24px;
    background: #f8fafc;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    align-items: flex-start;
    gap: 36px;
}

.card-column {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* -------------------------------------------------------------
   CARD WRAPPER: EXACT 86mm x 54mm RATIO (1.5926)
   Preview size: 400px x 251px
------------------------------------------------------------- */
.pos-card-wrapper {
    width: 400px;
    height: 251px;
    border-radius: 18px;
    background: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    user-select: none;
}

/* WATERMARK BACKGROUND (FRONT) */
.card-watermark {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 140px;
    height: 140px;
    pointer-events: none;
    z-index: 1;
    opacity: 0.9;
}

/* BOTTOM DECORATIVE WAVES */
.card-bottom-waves {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 52px;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.card-bottom-waves svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* FRONT CARD HEADER */
.card-front-header {
    position: relative;
    z-index: 3;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 8px 20px;
}

.card-brand {
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-brand-logo {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.card-brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 2px;
}

.card-brand-logo svg {
    width: 22px;
    height: 22px;
    color: #2563eb;
}

.card-brand-name {
    font-size: 16px;
    font-weight: 800;
    color: #1e3a8a;
    line-height: 1.15;
    letter-spacing: -0.02em;
    max-width: 175px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* MEMBER BADGE */
.card-member-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(29, 78, 216, 0.2);
    color: #ffffff;
}

.card-member-badge.badge-blue {
    background: #1d4ed8;
}

.card-member-badge.badge-indigo {
    background: #4338ca;
}

.card-member-badge.badge-sky {
    background: #0284c7;
}

.card-member-badge.badge-gray {
    background: #64748b;
}

.card-member-badge svg {
    width: 12px;
    height: 12px;
    fill: currentColor;
    color: #93c5fd;
}

/* FRONT CARD DETAILS (3 ROWS) */
.card-front-content {
    position: relative;
    z-index: 3;
    padding: 6px 20px 0 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.card-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 220px;
}

.card-info-item.with-divider {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 5px;
}

.card-info-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    flex-shrink: 0;
}

.card-info-icon svg {
    width: 14px;
    height: 14px;
}

.card-info-texts {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.card-info-label {
    font-size: 8px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    line-height: 1.2;
}

.card-info-value {
    font-size: 15px;
    font-weight: 800;
    color: #1d4ed8;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-info-value.val-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 14px;
    letter-spacing: 0.04em;
}

.card-info-value.val-discount {
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 0.02em;
}

/* -------------------------------------------------------------
   BACK CARD STYLING
------------------------------------------------------------- */
.card-back-barcode-box {
    position: relative;
    z-index: 3;
    margin: 12px 18px 6px 18px;
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    height: 44px;
}

.card-back-barcode-svg {
    width: 100%;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.card-back-barcode-svg svg {
    width: 100%;
    height: 100%;
    display: block;
}

.card-back-terms {
    position: relative;
    z-index: 3;
    padding: 4px 20px 2px 20px;
}

.card-terms-header {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
}

.card-terms-header svg {
    width: 14px;
    height: 14px;
    color: #2563eb;
    flex-shrink: 0;
}

.card-terms-title {
    font-size: 9.5px;
    font-weight: 800;
    color: #1d4ed8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* KETENTUAN PENGGUNAAN: PENOMORAN OTOMATIS RESILIEN 1 - 5 */
.card-terms-ol {
    counter-reset: card-term-count;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
}

.card-terms-ol li {
    counter-increment: card-term-count;
    display: flex !important;
    align-items: flex-start !important;
    gap: 5px !important;
    font-size: 8px !important;
    line-height: 1.35 !important;
    color: #334155 !important;
    font-weight: 500 !important;
}

.card-terms-ol li::before {
    content: counter(card-term-count) ".";
    font-weight: 800 !important;
    color: #1e3a8a !important;
    min-width: 13px !important;
    flex-shrink: 0 !important;
}

.card-back-divider {
    position: relative;
    z-index: 3;
    margin: 6px 20px;
    border-top: 1px solid #e2e8f0;
}

.card-back-contacts {
    position: relative;
    z-index: 3;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.card-contact-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 8.5px;
    font-weight: 600;
    color: #1d4ed8;
    line-height: 1.2;
}

.card-contact-row svg {
    width: 13px;
    height: 13px;
    color: #2563eb;
    flex-shrink: 0;
}

.card-contact-row span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
}

/* PILL PREVIEW LABELS */
.card-preview-pill {
    margin-top: 14px;
    padding: 5px 22px;
    background: #e2e8f0;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    border-radius: 9999px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

/* MODAL FOOTER */
.modal-card-footer {
    padding: 16px 28px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.btn-modal-close {
    padding: 9px 20px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}

.btn-modal-close:hover {
    background: #f8fafc;
}

.btn-modal-print {
    padding: 9px 24px;
    border-radius: 12px;
    border: none;
    background: #2563eb;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.15s;
}

.btn-modal-print:hover {
    background: #1d4ed8;
}

/* -------------------------------------------------------------
   PRINT SPECIFICATION: PHYSICAL 86mm x 54mm (STANDARD ATM/CR80)
   STRATEGI: @page = ukuran kartu itu sendiri, setiap sisi pada
   halaman tersendiri. Tidak ada scaling/shrinking browser.
------------------------------------------------------------- */
@media print {
    /*
     * KUNCI UTAMA: ukuran halaman = ukuran kartu fisik.
     * Dengan ini browser tidak perlu muat dua kartu di satu
     * halaman A4, sehingga tidak ada shrink/scale-to-fit.
     */
    @page {
        size: 86mm 54mm landscape;
        margin: 0;
    }

    /* Sembunyikan semua konten halaman kecuali kartu */
    html, body {
        width: 86mm !important;
        height: 54mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: hidden !important;
    }

    body * {
        visibility: hidden !important;
    }

    /* Tampilkan hanya elemen kartu */
    .modal-card-backdrop,
    .modal-card-box,
    .modal-card-body,
    #card-print-container,
    #card-print-container * {
        visibility: visible !important;
    }

    /* Backdrop & box: pakai position fixed agar tidak ikut scroll */
    .modal-card-backdrop {
        position: fixed !important;
        inset: 0 !important;
        display: block !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 86mm !important;
        height: 54mm !important;
        overflow: visible !important;
    }

    .modal-card-box {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 86mm !important;
        max-width: 86mm !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* Sembunyikan elemen non-cetak */
    .no-print,
    .modal-card-header,
    .modal-card-footer,
    .card-preview-pill {
        display: none !important;
    }

    /*
     * Setiap card-column jadi "halaman" 86x54mm tersendiri.
     * Gunakan display:block + break-after:page agar setiap
     * kartu cetak pada halaman baru.
     */
    .modal-card-body {
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        width: 86mm !important;
    }

    /* Tiap card-column = 1 halaman 86x54mm */
    .card-column {
        display: block !important;
        width: 86mm !important;
        height: 54mm !important;
        padding: 0 !important;
        margin: 0 !important;
        /* Setiap kolom yang terlihat mendapat halaman baru */
        break-after: page;
        page-break-after: always;
    }

    /* Kartu terakhir yang tampil: tidak perlu page break */
    .card-column:last-child {
        break-after: avoid !important;
        page-break-after: avoid !important;
    }

    /* Sembunyikan kartu yang di-filter */
    .card-column.print-hidden {
        display: none !important;
    }

    /* Saat hanya Depan: front adalah satu-satunya → no break */
    .card-column.print-hidden + .card-column {
        break-after: avoid !important;
        page-break-after: avoid !important;
    }

    /* -------------------------------------------------------
       CARD WRAPPER: Mengisi tepat 86mm x 54mm halaman cetak
    ------------------------------------------------------- */
    .pos-card-wrapper {
        width: 86mm !important;
        height: 54mm !important;
        min-width: 86mm !important;
        max-width: 86mm !important;
        min-height: 54mm !important;
        max-height: 54mm !important;
        box-sizing: border-box !important;
        border-radius: 3.5mm !important;
        border: 0.4px solid #cbd5e1 !important;
        box-shadow: none !important;
        overflow: hidden !important;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        break-inside: avoid !important;
        page-break-inside: avoid !important;
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
    }

    /* -------------------------------------------------------
       KARTU DEPAN – skala proporsional terhadap 54mm tinggi
    ------------------------------------------------------- */
    .card-front-header {
        padding: 3.5mm 4.5mm 1.5mm 4.5mm !important;
    }
    .card-brand-logo {
        width: 8mm !important;
        height: 8mm !important;
    }
    .card-brand-name {
        font-size: 10pt !important;
    }
    .card-member-badge {
        padding: 1mm 2.5mm !important;
        font-size: 5.5pt !important;
        gap: 1.5mm !important;
    }
    .card-member-badge svg {
        width: 3mm !important;
        height: 3mm !important;
    }
    .card-front-content {
        padding: 2mm 4.5mm 0 4.5mm !important;
        gap: 2mm !important;
    }
    .card-info-item {
        width: 52mm !important;
        gap: 2.5mm !important;
    }
    .card-info-item.with-divider {
        padding-bottom: 1.5mm !important;
    }
    .card-info-icon {
        width: 5.5mm !important;
        height: 5.5mm !important;
    }
    .card-info-icon svg {
        width: 3.5mm !important;
        height: 3.5mm !important;
    }
    .card-info-label {
        font-size: 5.5pt !important;
        line-height: 1.2 !important;
    }
    .card-info-value {
        font-size: 9pt !important;
        line-height: 1.2 !important;
    }
    .card-info-value.val-mono {
        font-size: 8.5pt !important;
    }
    .card-info-value.val-discount {
        font-size: 9pt !important;
    }
    .card-watermark {
        width: 30mm !important;
        height: 30mm !important;
        right: 3.5mm !important;
    }

    /* -------------------------------------------------------
       KARTU BELAKANG – budget tinggi:
       54mm total
       - barcode area: 2mm top margin + 10mm box + 1.5mm = 13.5mm
       - terms: 0.5mm pad + ~12mm content + 0mm pad  = ~12.5mm
       - divider: 1.5mm margin × 2 = 3mm
       - contacts: 3×2.5mm + 2×0.8mm gap + 2mm pad-btm = ~11.1mm
       - waves: 10mm (absolute, overlaps bottom)
       Total konten berhenti ≈ 40mm, waves mulai 44mm → aman
    ------------------------------------------------------- */
    .card-bottom-waves {
        height: 10mm !important;
    }

    /* Barcode */
    .card-back-barcode-box {
        margin: 2mm 4mm 1.5mm 4mm !important;
        padding: 1mm 3mm !important;
        height: 10mm !important;
        border-radius: 2mm !important;
    }
    .card-back-barcode-svg {
        height: 8mm !important;
    }

    /* Ketentuan Penggunaan */
    .card-back-terms {
        padding: 0.5mm 4.5mm 0 4.5mm !important;
    }
    .card-terms-header {
        margin-bottom: 1.5px !important;
    }
    .card-terms-header svg {
        width: 3.5mm !important;
        height: 3.5mm !important;
    }
    .card-terms-title {
        font-size: 6.5pt !important;
        letter-spacing: 0.03em !important;
    }
    .card-terms-ol {
        gap: 1.8px !important;
    }
    .card-terms-ol li {
        font-size: 5pt !important;
        line-height: 1.3 !important;
    }
    .card-terms-ol li::before {
        font-size: 5pt !important;
    }

    /* Divider */
    .card-back-divider {
        margin: 1.5mm 4.5mm !important;
    }

    /* Informasi Kontak */
    .card-back-contacts {
        /* padding-bottom memastikan Gmail tidak menabrak wave (10mm dari bawah) */
        padding: 0 4.5mm 2.5mm 4.5mm !important;
        gap: 1mm !important;
    }
    .card-contact-row {
        font-size: 5.5pt !important;
        line-height: 1.3 !important;
        gap: 1.5mm !important;
    }
    .card-contact-row svg {
        width: 3mm !important;
        height: 3mm !important;
    }
    .card-contact-row span {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 74mm !important;
    }
}
</style>

<script>
let currentCardRequestId = 0;
let isCardBarcodeReady = false;
let currentMemberId = '';
let activePrintSide = 'both';

// ISO/IEC 15417 Code 128 Pattern Dictionary
const CODE128_PATTERNS = [
  '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213',
  '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132',
  '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211',
  '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313',
  '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331',
  '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111',
  '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214',
  '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111',
  '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141',
  '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141',
  '114131','311141','411131','211412','211214','211232','2331112'
];

/**
 * Generates genuine ISO/IEC 15417 Code 128-B SVG barcode
 */
function generateCode128Svg(text, height = 36) {
    if (!text || typeof text !== 'string') return '';
    const cleanText = text.trim();
    if (!cleanText) return '';

    const codes = [104]; // Start Code B
    let checksum = 104;

    for (let i = 0; i < cleanText.length; i++) {
        const code = cleanText.charCodeAt(i) - 32;
        if (code < 0 || code > 95) continue;
        codes.push(code);
        checksum += code * (i + 1);
    }

    codes.push(checksum % 103);
    codes.push(106); // Stop Code

    let patternStr = '';
    for (let j = 0; j < codes.length; j++) {
        const c = codes[j];
        if (c >= 0 && c < CODE128_PATTERNS.length) {
            patternStr += CODE128_PATTERNS[c];
        }
    }

    const quietZone = 10;
    let x = quietZone;
    let rects = [];

    for (let k = 0; k < patternStr.length; k++) {
        const width = parseInt(patternStr[k], 10);
        if (k % 2 === 0) {
            rects.push(`<rect x="${x}" y="0" width="${width}" height="${height}" fill="#0f172a" />`);
        }
        x += width;
    }

    const totalWidth = x + quietZone;
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${totalWidth} ${height}" preserveAspectRatio="none" style="width: 100%; height: 100%; display: block;"><rect width="100%" height="100%" fill="#ffffff"/><g>${rects.join('')}</g></svg>`;
}

function setPrintButtonState(enabled, text) {
    const btn = document.getElementById('btn-print-card');
    if (!btn) return;
    btn.disabled = !enabled;
    if (enabled) {
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    if (text) {
        const span = btn.querySelector('span');
        if (span) span.textContent = text;
        else btn.textContent = text;
    }
}

/**
 * Ubah filter tampilan (Kedua Sisi, Depan Saja, Belakang Saja)
 * Mengubah preview layar DAN hasil cetak sekaligus.
 */
function setPrintSide(side) {
    activePrintSide = side;
    const btnBoth = document.getElementById('btn-side-both');
    const btnFront = document.getElementById('btn-side-front');
    const btnBack = document.getElementById('btn-side-back');
    const colFront = document.getElementById('card-column-front');
    const colBack = document.getElementById('card-column-back');

    [btnBoth, btnFront, btnBack].forEach(b => {
        if (b) b.classList.remove('active');
    });

    if (colFront) {
        colFront.classList.remove('preview-hidden', 'print-hidden');
    }
    if (colBack) {
        colBack.classList.remove('preview-hidden', 'print-hidden');
    }

    if (side === 'front') {
        if (btnFront) btnFront.classList.add('active');
        if (colBack) colBack.classList.add('preview-hidden', 'print-hidden');
    } else if (side === 'back') {
        if (btnBack) btnBack.classList.add('active');
        if (colFront) colFront.classList.add('preview-hidden', 'print-hidden');
    } else {
        if (btnBoth) btnBoth.classList.add('active');
    }
}

/**
 * Membuka modal dan mengisi data kartu member secara dinamis
 */
window.openCardModal = function(nama, memberId, totalTransaksi, statusMember, customDiscount, isActiveMember = true, diskonPercent = null) {
    currentMemberId = (memberId && memberId !== 'null' && memberId !== 'undefined') ? String(memberId).trim() : '';
    const requestId = ++currentCardRequestId;

    // Reset filter tampilan ke kedua sisi
    setPrintSide('both');

    // 1. Data Pemegang & ID Dinamis
    const nameEl = document.getElementById('card-name');
    const idEl = document.getElementById('card-id');
    if (nameEl) nameEl.textContent = nama || '-';
    if (idEl) idEl.textContent = currentMemberId || '-';

    // 2. Diskon Calculation (Aturan Bisnis Dinamis)
    let discountVal = 10;
    if (diskonPercent !== null && diskonPercent !== undefined && diskonPercent !== '' && diskonPercent !== 'null') {
        discountVal = parseFloat(diskonPercent);
    } else if (customDiscount !== null && customDiscount !== undefined && customDiscount !== '' && customDiscount !== 'null') {
        discountVal = parseFloat(customDiscount);
    } else if (isActiveMember) {
        discountVal = {{ config('pos.diskon_member', 10) }};
    }

    const discountEl = document.getElementById('card-discount');
    if (discountEl) {
        if (!isActiveMember) {
            discountEl.textContent = 'DISKON 0%';
        } else {
            discountEl.textContent = `DISKON ${discountVal}%`;
        }
    }

    // 3. Status Badge & Colors
    const statusTextEl = document.getElementById('card-status-member');
    const badgeContainer = document.getElementById('card-status-badge');

    if (badgeContainer && statusTextEl) {
        badgeContainer.className = 'card-member-badge';

        if (!isActiveMember) {
            statusTextEl.textContent = 'NON-MEMBER / INAKTIF';
            badgeContainer.classList.add('badge-gray');
        } else {
            const resolvedStatus = statusMember || 'Member Pelanggan Tetap';
            statusTextEl.textContent = resolvedStatus;

            if (resolvedStatus === 'Member Keluarga Nakes') {
                badgeContainer.classList.add('badge-indigo');
            } else if (resolvedStatus === 'Member Only') {
                badgeContainer.classList.add('badge-sky');
            } else {
                badgeContainer.classList.add('badge-blue');
            }
        }
    }

    // 4. Render Barcode Sisi Belakang (Code 128)
    const barcodeSvgContainer = document.getElementById('card-barcode-svg');
    const barcodeTextEl = document.getElementById('card-barcode-text');
    const errorContainer = document.getElementById('card-qr-status-container');
    const errorText = document.getElementById('card-qr-error');

    if (errorContainer) errorContainer.classList.add('hidden');

    // Pastikan barcode selalu dirender
    const barcodeCode = currentMemberId || 'MBR-000000';
    try {
        const svgContent = generateCode128Svg(barcodeCode, 36);
        if (barcodeSvgContainer) barcodeSvgContainer.innerHTML = svgContent;
        if (barcodeTextEl) barcodeTextEl.textContent = currentMemberId || '-';
        isCardBarcodeReady = true;
        setPrintButtonState(true, 'Cetak Kartu');
    } catch (err) {
        console.error('Barcode render error:', err);
        if (barcodeSvgContainer) barcodeSvgContainer.innerHTML = '';
        isCardBarcodeReady = false;
        if (errorContainer && errorText) {
            errorText.textContent = 'Gagal membuat barcode kartu.';
            errorContainer.classList.remove('hidden');
        }
        setPrintButtonState(false, 'Cetak Kartu');
    }

    // 5. Tampilkan Modal
    const modal = document.getElementById('modal-card');
    if (modal) {
        modal.classList.remove('hidden');
    }
};

/**
 * Helper untuk membuka modal langsung dari elemen tombol yang memiliki data-* attributes
 */
window.openCardModalFromBtn = function(btn) {
    if (!btn) return;
    const nama = btn.getAttribute('data-nama') || '';
    const memberId = btn.getAttribute('data-member-id') || '';
    const totalBelanja = btn.getAttribute('data-total-belanja') || 0;
    const statusMember = btn.getAttribute('data-status-member') || '';
    const customDiscount = btn.getAttribute('data-custom-discount');
    const isActive = btn.getAttribute('data-is-active') === '1';
    const discount = btn.getAttribute('data-discount');

    window.openCardModal(nama, memberId, totalBelanja, statusMember, customDiscount, isActive, discount);
};

function closeCardModal() {
    const modal = document.getElementById('modal-card');
    if (modal) modal.classList.add('hidden');
}

function printCard() {
    if (!isCardBarcodeReady) {
        return;
    }
    window.print();
}

// Close modal on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modal-card');
        if (modal && !modal.classList.contains('hidden')) {
            closeCardModal();
        }
    }
});
</script>
