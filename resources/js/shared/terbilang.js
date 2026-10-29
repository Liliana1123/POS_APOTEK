/**
 * shared/terbilang.js
 * Konversi angka nominal uang ke kalimat terbilang Bahasa Indonesia secara real-time.
 * Menghasilkan micro-card bantuan visual di bawah input bertanda [data-terbilang="true"].
 */

export function terbilang(nominal) {
    const angka = Math.floor(Math.abs(Number(nominal) || 0));
    if (angka === 0) return 'Nol rupiah';

    const bilangan = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima',
        'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
    ];

    function konversi(n) {
        if (n < 12) {
            return bilangan[n];
        } else if (n < 20) {
            return konversi(n - 10) + ' belas';
        } else if (n < 100) {
            return konversi(Math.floor(n / 10)) + ' puluh ' + konversi(n % 10);
        } else if (n < 200) {
            return 'seratus ' + konversi(n - 100);
        } else if (n < 1000) {
            return konversi(Math.floor(n / 100)) + ' ratus ' + konversi(n % 100);
        } else if (n < 2000) {
            return 'seribu ' + konversi(n - 1000);
        } else if (n < 1000000) {
            return konversi(Math.floor(n / 1000)) + ' ribu ' + konversi(n % 1000);
        } else if (n < 1000000000) {
            return konversi(Math.floor(n / 1000000)) + ' juta ' + konversi(n % 1000000);
        } else if (n < 1000000000000) {
            return konversi(Math.floor(n / 1000000000)) + ' milyar ' + konversi(n % 1000000000);
        } else if (n < 1000000000000000) {
            return konversi(Math.floor(n / 1000000000000)) + ' triliun ' + konversi(n % 1000000000000);
        }
        return '';
    }

    const hasil = konversi(angka).replace(/\s+/g, ' ').trim();
    if (!hasil) return '';

    // Huruf kapital di awal kata pertama + akhiran rupiah
    return hasil.charAt(0).toUpperCase() + hasil.slice(1) + ' rupiah';
}

function updateTerbilang(input) {
    const rawVal = input.value.replace(/[^0-9.]/g, '');
    const num = parseFloat(rawVal);
    let box = input.parentElement ? input.parentElement.querySelector('.terbilang-preview-box') : null;

    if (!rawVal || isNaN(num) || num <= 0) {
        if (box) {
            box.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                if (box && box.classList.contains('opacity-0')) {
                    box.remove();
                }
            }, 180);
        }
        return;
    }

    const teks = terbilang(num);

    if (!box) {
        box = document.createElement('div');
        box.className = 'terbilang-preview-box mt-1.5 flex items-start gap-1.5 px-2.5 py-1.5 bg-blue-50/70 border border-blue-200/60 rounded-lg text-blue-900 text-[11px] leading-snug transition-all duration-200 opacity-0 scale-95 origin-top';
        box.innerHTML = `
            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="terbilang-text font-medium italic break-words"></span>
        `;
        // Sisipkan setelah input atau setelah helper teks terdekat
        if (input.nextSibling) {
            input.parentNode.insertBefore(box, input.nextSibling);
        } else {
            input.parentNode.appendChild(box);
        }
        // Force reflow untuk animasi halus
        void box.offsetWidth;
        box.classList.remove('opacity-0', 'scale-95');
    }

    const span = box.querySelector('.terbilang-text');
    if (span) {
        span.textContent = teks;
    }
}

export function initTerbilang() {
    document.querySelectorAll('input[data-terbilang="true"]').forEach(input => {
        if (input.dataset.terbilangBound) return;
        input.dataset.terbilangBound = 'true';

        input.addEventListener('input', () => updateTerbilang(input));
        input.addEventListener('focus', () => updateTerbilang(input));

        // Inisialisasi awal jika sudah ada nilai
        if (input.value) {
            updateTerbilang(input);
        }
    });
}

// Jalankan otomatis saat DOM ready dan amati perubahan modal / DOM dinamis
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTerbilang);
    } else {
        initTerbilang();
    }

    // Observer untuk modal yang dibuka secara dinamis
    const observer = new MutationObserver(() => {
        initTerbilang();
    });
    observer.observe(document.body, { childList: true, subtree: true });
}

window.terbilang = terbilang;
