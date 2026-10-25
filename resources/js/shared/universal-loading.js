/**
 * Universal Loading System
 * 1. Global Top Progress Bar (YouTube/GitHub style)
 * 2. Auto Button Loading Spinner & Interaction Lock
 */

(function () {
    'use strict';

    let progressBarWrapper = null;
    let progressBarEl = null;
    let progressTimer = null;
    let currentProgress = 0;
    let activeRequests = 0;

    /*
    |--------------------------------------------------------------------------
    | 1. GLOBAL TOP PROGRESS BAR
    |--------------------------------------------------------------------------
    */
    function initProgressBar() {
        if (progressBarWrapper) return;

        progressBarWrapper = document.createElement('div');
        progressBarWrapper.id = 'global-top-progress';
        progressBarWrapper.className = 'fixed top-0 left-0 right-0 h-[3px] pointer-events-none z-[999999] opacity-0 transition-opacity duration-200';

        progressBarEl = document.createElement('div');
        progressBarEl.id = 'global-top-progress-bar';
        progressBarEl.className = 'h-full bg-gradient-to-r from-blue-600 via-indigo-500 to-sky-400 shadow-[0_0_10px_rgba(59,130,246,0.6)] transition-all duration-300 ease-out';
        progressBarEl.style.width = '0%';

        progressBarWrapper.appendChild(progressBarEl);
        document.body.appendChild(progressBarWrapper);
    }

    const topProgress = {
        start: function () {
            initProgressBar();
            if (progressTimer) clearInterval(progressTimer);

            progressBarWrapper.classList.remove('opacity-0');
            progressBarWrapper.classList.add('opacity-100');

            currentProgress = Math.max(currentProgress, 15);
            progressBarEl.style.width = currentProgress + '%';

            progressTimer = setInterval(function () {
                if (currentProgress < 65) {
                    currentProgress += Math.random() * 12 + 4;
                } else if (currentProgress < 85) {
                    currentProgress += Math.random() * 4 + 1;
                } else if (currentProgress < 95) {
                    currentProgress += 0.5;
                }
                if (progressBarEl) {
                    progressBarEl.style.width = Math.min(currentProgress, 95) + '%';
                }
            }, 250);
        },

        set: function (percent) {
            initProgressBar();
            currentProgress = Math.min(Math.max(percent, 0), 100);
            if (progressBarEl) progressBarEl.style.width = currentProgress + '%';
        },

        done: function () {
            if (progressTimer) {
                clearInterval(progressTimer);
                progressTimer = null;
            }
            if (!progressBarWrapper || !progressBarEl) return;

            currentProgress = 100;
            progressBarEl.style.width = '100%';

            setTimeout(function () {
                if (progressBarWrapper) {
                    progressBarWrapper.classList.remove('opacity-100');
                    progressBarWrapper.classList.add('opacity-0');
                }
                setTimeout(function () {
                    if (progressBarEl) progressBarEl.style.width = '0%';
                    currentProgress = 0;
                }, 250);
            }, 200);
        }
    };

    window.topProgress = topProgress;

    /*
    |--------------------------------------------------------------------------
    | 2. AUTO BUTTON LOADING & FORM SUBMIT INTERCEPTOR
    |--------------------------------------------------------------------------
    */
    function lockSubmitButton(btn, customText = null) {
        if (!btn || btn.disabled || btn.dataset.loadingActive === 'true') return;

        // Jangan proses jika form atau tombol menandai no-loading
        if (btn.hasAttribute('data-no-loading') || btn.closest('[data-no-loading]')) {
            return;
        }

        // Kunci lebar agar tidak melompat / layout-shift
        const originalWidth = btn.offsetWidth;
        if (originalWidth > 0) {
            btn.style.width = originalWidth + 'px';
        }

        btn.dataset.loadingActive = 'true';
        btn.dataset.originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');

        function getSmartLoadingText(btn, customText = null) {
            if (customText) return customText;
            const attrText = btn.getAttribute('data-loading-text');
            if (attrText) return attrText;

            const original = (btn.textContent || '').trim().toLowerCase();
            if (original.includes('hapus') || btn.classList.contains('btn-destructive')) return 'Menghapus...';
            if (original.includes('simpan')) return 'Menyimpan...';
            if (original.includes('reset')) return 'Mereset...';
            if (original.includes('unggah') || original.includes('upload')) return 'Mengunggah...';
            if (original.includes('buat') || original.includes('tambah')) return 'Membuat...';
            if (original.includes('restore') || original.includes('pulihkan')) return 'Memulihkan...';
            return 'Memproses...';
        }

        const isDelete = btn.classList.contains('btn-destructive') || btn.classList.contains('bg-red-600');
        const loadingText = getSmartLoadingText(btn, customText);

        btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0 mr-2 align-middle"></span><span class="align-middle">' + loadingText + '</span>';
    }

    function unlockSubmitButton(btn) {
        if (!btn || !btn.dataset.originalHtml) return;

        btn.disabled = false;
        btn.classList.remove('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
        btn.innerHTML = btn.dataset.originalHtml;
        btn.style.width = '';
        delete btn.dataset.loadingActive;
        delete btn.dataset.originalHtml;
    }

    window.lockButton = lockSubmitButton;
    window.unlockButton = unlockSubmitButton;

    /*
    |--------------------------------------------------------------------------
    | 3. GLOBAL LISTENERS
    |--------------------------------------------------------------------------
    */
    document.addEventListener('DOMContentLoaded', function () {
        initProgressBar();

        // Tangkap klik tombol submit sebelum form submit terpanggil
        let lastClickedSubmitBtn = null;
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('button[type="submit"], input[type="submit"]');
            if (btn) {
                lastClickedSubmitBtn = btn;
            }
        });

        // Intercept form submission biasa (hanya untuk form mutasi data: POST / PUT / PATCH / DELETE)
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const method = (form.getAttribute('method') || 'GET').toUpperCase();

            // 1. Lewati form GET (Pencarian, Filter, Card-Filter, dll.)
            // Form pencarian tidak boleh diubah menjadi "Menyimpan..."
            if (
                method === 'GET' ||
                form.hasAttribute('data-live-filter') ||
                form.closest('[data-live-filter]') ||
                form.classList.contains('card-filter') ||
                form.closest('.filter-card-web')
            ) {
                return;
            }

            // 2. Lewati jika form berada di dalam modal (modal-confirm atau modal-form)
            // Modal sudah memiliki progress bar terpadu sendiri pada kartu modalnya
            if (
                form.id === 'modal-confirm-form' ||
                form.closest('#modal-confirm') ||
                form.closest('[id$="-backdrop"]') ||
                form.closest('.modal-confirm-content') ||
                form.closest('[role="dialog"]')
            ) {
                return;
            }

            // 3. Lewati jika form meminta no-loading
            if (form.hasAttribute('data-no-loading')) return;

            // Cari tombol submit
            const submitBtn = lastClickedSubmitBtn && form.contains(lastClickedSubmitBtn)
                ? lastClickedSubmitBtn
                : form.querySelector('button[type="submit"], input[type="submit"]');

            if (submitBtn) {
                lockSubmitButton(submitBtn);
            }

            // Nyalakan top progress bar
            topProgress.start();

            // 4. Pengaman jika ada script lain yang memanggil e.preventDefault() (AJAX atau validasi gagal)
            setTimeout(function () {
                if (e.defaultPrevented) {
                    if (submitBtn) unlockSubmitButton(submitBtn);
                    topProgress.done();
                }
            }, 0);
        });

        // Tangkap perpindahan halaman via link internal (GET navigation)
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                return;
            }

            // Jangan intercept jika buka di tab baru (target="_blank") atau download
            if (link.target === '_blank' || link.hasAttribute('download') || e.ctrlKey || e.metaKey || e.shiftKey) {
                return;
            }

            // Cek apakah URL sama dengan origin saat ini (internal link)
            try {
                const targetUrl = new URL(href, window.location.origin);
                if (targetUrl.origin === window.location.origin) {
                    // Jika halaman tujuan berbeda dengan URL saat ini
                    if (targetUrl.href !== window.location.href) {
                        topProgress.start();
                    }
                }
            } catch (err) {}
        });
    });

    // Reset jika navigasi halaman kembali dari back-forward cache (bfcache)
    window.addEventListener('pageshow', function (event) {
        topProgress.done();
        document.querySelectorAll('[data-loading-active="true"]').forEach(unlockSubmitButton);
    });

    // Pemicu saat browser mulai membongkar halaman (hanya jika TIDAK ada modal yang sedang aktif)
    window.addEventListener('beforeunload', function () {
        const isModalVisible = document.querySelector(
            '#modal-confirm:not(.hidden), [id$="-backdrop"]:not(.hidden), .modal-confirm-content:not(.hidden)'
        );
        if (!isModalVisible) {
            topProgress.start();
        }
    });

})();
