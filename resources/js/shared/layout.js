// resources/js/shared/layout.js
// Shared layout shell logic extracted from layouts/app.blade.php

// Sidebar expand/collapse (desktop)
function initSidebarExpand() {
    var sidebar = document.getElementById('app-sidebar');
    if (!sidebar) return;

    var KEEP_KEY = 'sidebar-keep-expanded';
    var PIN_KEY = 'sidebar-pinned';
    var html = document.documentElement;
    var pointerInside = false;

    var pinBtn = document.getElementById('pin-sidebar-toggle');
    var iconUnpinned = document.getElementById('pin-icon-unpinned');
    var iconPinned = document.getElementById('pin-icon-pinned');

    function isDesktop() {
        return window.matchMedia('(min-width: 1024px)').matches;
    }

    function isPinned() {
        try {
            return localStorage.getItem(PIN_KEY) === '1';
        } catch (e) {
            return false;
        }
    }

    function updatePinUI(pinned) {
        if (!pinBtn) return;
        if (pinned) {
            pinBtn.title = 'Lepas kunci sidebar (Unpin - Ctrl+B)';
            pinBtn.setAttribute('aria-label', 'Lepas kunci sidebar (Ctrl+B)');
            pinBtn.classList.add('bg-blue-800', 'text-white');
            sidebar.classList.add('sidebar-pinned');
            if (iconUnpinned) iconUnpinned.classList.add('hidden');
            if (iconPinned) iconPinned.classList.remove('hidden');
        } else {
            pinBtn.title = 'Kunci sidebar tetap terbuka (Pin - Ctrl+B)';
            pinBtn.setAttribute('aria-label', 'Kunci sidebar (Ctrl+B)');
            pinBtn.classList.remove('bg-blue-800', 'text-white');
            sidebar.classList.remove('sidebar-pinned');
            if (iconUnpinned) iconUnpinned.classList.remove('hidden');
            if (iconPinned) iconPinned.classList.add('hidden');
        }
    }

    function expandSidebar() {
        if (!isDesktop()) return;
        sidebar.classList.add('sidebar-expanded');
    }

    function collapseSidebar() {
        if (isPinned()) return;
        sidebar.classList.remove('sidebar-expanded');
        html.classList.remove('sidebar-keep-expanded');
    }

    function isFocusInside() {
        return sidebar.contains(document.activeElement);
    }

    if (pinBtn) {
        pinBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var pinned = isPinned();
            if (pinned) {
                try { localStorage.removeItem(PIN_KEY); } catch (err) { }
                html.classList.remove('sidebar-keep-expanded');
                updatePinUI(false);
                if (!pointerInside && !isFocusInside()) {
                    collapseSidebar();
                }
            } else {
                try { localStorage.setItem(PIN_KEY, '1'); } catch (err) { }
                try { sessionStorage.removeItem(KEEP_KEY); } catch (err) { }
                html.classList.add('sidebar-keep-expanded');
                expandSidebar();
                updatePinUI(true);
            }
        });
    }

    sidebar.addEventListener('click', function (e) {
        var link = e.target.closest('a.sidebar-nav-link');
        if (!link || !sidebar.contains(link) || !isDesktop()) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        sessionStorage.setItem(KEEP_KEY, '1');
    });

    var initialPinned = isPinned();
    updatePinUI(initialPinned);

    if (isDesktop() && (initialPinned || html.classList.contains('sidebar-keep-expanded') || sessionStorage.getItem(KEEP_KEY) === '1')) {
        pointerInside = true;
        expandSidebar();
        if (!initialPinned) {
            sessionStorage.removeItem(KEEP_KEY);
            requestAnimationFrame(function () {
                html.classList.remove('sidebar-keep-expanded');
            });
        }

        function settlePointer(e) {
            document.removeEventListener('pointermove', settlePointer, true);
            var el = document.elementFromPoint(e.clientX, e.clientY);
            pointerInside = !!(el && sidebar.contains(el));
            if (!pointerInside && !isFocusInside()) collapseSidebar();
        }
        document.addEventListener('pointermove', settlePointer, true);
    }

    sidebar.addEventListener('pointerenter', function () {
        pointerInside = true;
        expandSidebar();
    });
    sidebar.addEventListener('pointerleave', function () {
        pointerInside = false;
        if (!isFocusInside()) collapseSidebar();
    });
    sidebar.addEventListener('focusin', expandSidebar);
    sidebar.addEventListener('focusout', function (e) {
        if (sidebar.contains(e.relatedTarget) || pointerInside) return;
        collapseSidebar();
    });
}

// Sidebar group state restore from localStorage (blocking execution)
function initSidebarGroupRestore() {
    document.querySelectorAll('.sidebar-group').forEach(function (group) {
        var name = group.dataset.group;
        var saved = localStorage.getItem('sidebar-group-' + name);
        var items = group.querySelector('.sidebar-group-items');
        if (!items) return;
        var expand = saved === '1' || items.dataset.hasActive === 'true';
        if (expand) {
            items.style.transition = 'none';
            items.style.maxHeight = items.scrollHeight ? (items.scrollHeight + 'px') : 'none';
            items.setAttribute('data-expanded', '1');
            var btn = group.querySelector('.sidebar-group-header');
            if (btn) btn.setAttribute('aria-expanded', 'true');
            var arrow = group.querySelector('.sidebar-group-header .sidebar-group-arrow');
            if (arrow) arrow.classList.add('rotate-90');
            void items.offsetHeight;
            items.style.transition = '';
        }
    });
}

// Keep sidebar nav scroll position across full-page menu navigation
function initSidebarScrollRestore() {
    var scrollArea = document.querySelector('#app-sidebar .sidebar-scroll-area');
    if (!scrollArea) return;

    var KEY = 'sidebar-scroll-top';

    function save() {
        sessionStorage.setItem(KEY, String(scrollArea.scrollTop));
    }

    var saved = parseInt(sessionStorage.getItem(KEY) || '0', 10);
    if (!isNaN(saved) && saved > 0) {
        scrollArea.scrollTop = saved;
    }

    var ticking = false;
    scrollArea.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
            save();
            ticking = false;
        });
    }, { passive: true });

    var sidebar = document.getElementById('app-sidebar');
    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            if (e.target.closest('a.sidebar-nav-link')) save();
        });
    }
}

// Toast auto-dismiss
function initToastAutoDismiss() {
    var toastSuccess = document.getElementById('toast-success');
    if (toastSuccess) {
        setTimeout(function () {
            toastSuccess.classList.add('opacity-0', 'translate-y-2');
            setTimeout(function () { toastSuccess.remove(); }, 300);
        }, 3500);
    }
    var toastError = document.getElementById('toast-error');
    if (toastError) {
        setTimeout(function () {
            toastError.classList.add('opacity-0', 'translate-y-2');
            setTimeout(function () { toastError.remove(); }, 300);
        }, 4000);
    }
}

// Main DOMContentLoaded: sidebar toggle, delete confirm, form loading, real-time clock
function initShellInteractions() {
    var sidebar = document.getElementById('app-sidebar');
    var toggle = document.getElementById('mobile-sidebar-toggle');
    var backdrop = document.getElementById('sidebar-backdrop');
    var closeBtn = document.getElementById('close-sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        });
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
    }

    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
        // Only close sidebar on Escape if mobile sidebar is actually open
        if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full') && !window.matchMedia('(min-width: 1024px)').matches) {
            closeSidebar();
        }
        // Ctrl+B / Cmd+B: toggle sidebar pin (desktop only)
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            var pinToggle = document.getElementById('pin-sidebar-toggle');
            if (pinToggle && window.matchMedia('(min-width: 1024px)').matches) pinToggle.click();
        }
    });

    // Sidebar Group Accordion
    document.querySelectorAll('.sidebar-group-header').forEach(function (header) {
        header.addEventListener('click', function () {
            var group = this.closest('.sidebar-group');
            var items = group.querySelector('.sidebar-group-items');
            var arrow = this.querySelector('.sidebar-group-arrow');
            var groupName = group.dataset.group;

            var isExpanded = items.getAttribute('data-expanded') === '1';

            if (isExpanded) {
                items.style.maxHeight = items.scrollHeight + 'px';
                void items.offsetHeight;
                items.style.maxHeight = '0px';
                items.setAttribute('data-expanded', '0');
                this.setAttribute('aria-expanded', 'false');
                if (arrow) arrow.classList.remove('rotate-90');
                localStorage.setItem('sidebar-group-' + groupName, '0');
            } else {
                items.style.maxHeight = items.scrollHeight + 'px';
                items.setAttribute('data-expanded', '1');
                this.setAttribute('aria-expanded', 'true');
                if (arrow) arrow.classList.add('rotate-90');
                localStorage.setItem('sidebar-group-' + groupName, '1');
            }
        });
    });

    // Delete Form Confirmation Modal Interceptor
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.method.toLowerCase() === 'post' && form.querySelector('input[name="_method"][value="DELETE"]')) {
            if (!form.dataset.confirmed) {
                e.preventDefault();
                var modal = document.createElement('div');
                modal.className = 'modal-backdrop-custom';
                modal.innerHTML = (
                    '<div class="modal-container-custom mx-4 max-w-md">' +
                    '<div class="modal-header-custom">' +
                    '<h3 class="text-xs font-bold uppercase tracking-wider text-red-600">Konfirmasi Hapus</h3>' +
                    '<button type="button" id="confirm-x" class="text-gray-400 hover:text-gray-600 font-bold text-base">&times;</button>' +
                    '</div>' +
                    '<div class="modal-body-custom leading-relaxed">' +
                    'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan dapat memengaruhi integritas relasi data transaksi.' +
                    '</div>' +
                    '<div class="modal-footer-custom">' +
                    '<button type="button" id="confirm-cancel" class="btn-secondary">Batal</button>' +
                    '<button type="button" id="confirm-submit" class="btn-destructive">Hapus</button>' +
                    '</div>' +
                    '</div>'
                );
                document.body.appendChild(modal);

                var closeModal = function () { modal.remove(); };
                modal.querySelector('#confirm-cancel').onclick = closeModal;
                modal.querySelector('#confirm-x').onclick = closeModal;
                modal.querySelector('#confirm-submit').onclick = function () {
                    form.dataset.confirmed = 'true';
                    closeModal();
                    form.submit();
                };
            }
        }
    });

    // Universal Form Submission Loading States
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.action.includes('logout') || form.dataset.confirmed === 'false' || form.dataset.liveFilter === 'true') return;
        var btn = form.querySelector('button[type="submit"]');
        if (btn) {
            setTimeout(function () {
                btn.disabled = true;
                btn.innerHTML = btn.dataset.loadingText || 'Memproses...';
            }, 10);
        }
    });

    // Real-time Clock Updater
    (function () {
        function updateClock() {
            var el = document.getElementById('realtime-clock');
            if (!el) return;
            var now = new Date();
            var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            var dayName = days[now.getDay()];
            var date = now.getDate();
            var monthName = months[now.getMonth()];
            var year = now.getFullYear();

            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');

            el.textContent = dayName + ', ' + date + ' ' + monthName + ' ' + year + ' \u2022 ' + hours + ':' + minutes + ':' + seconds + ' WIB';
        }
        updateClock();
        setInterval(updateClock, 1000);
    })();
}

// Idle Auto-Logout (30 menit, sinkron antar tab)
function initIdleAutoLogout() {
    (function () {
        var IDLE = 30 * 60 * 1000;
        var WARN = 30 * 1000;
        var TICK = 5000;
        var KEY_LAST = 'pos_apotek_last_activity';
        var KEY_LOGOUT = 'pos_apotek_logout_request';
        var lastTouch = 0;
        var modal = null;
        var countdownTimer = null;
        var loggingOut = false;

        function now() { return Date.now(); }

        function touch() {
            localStorage.setItem(KEY_LAST, String(now()));
        }

        function doLogout() {
            if (loggingOut) return;
            loggingOut = true;
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var token = csrfMeta ? csrfMeta.content : '';
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ auto_logout: '1', _token: token }),
                credentials: 'same-origin'
            }).then(function (res) {
                if (res.redirected || res.ok) {
                    window.location.href = res.redirected ? res.url : '/login';
                } else {
                    window.location.href = '/login';
                }
            }).catch(function () {
                window.location.href = '/login';
            });
        }

        function requestLogout() {
            localStorage.setItem(KEY_LOGOUT, String(now()));
            doLogout();
        }

        function hideWarning() {
            if (modal) { modal.remove(); modal = null; }
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
        }

        function showWarning(remaining) {
            if (!modal || !modal.isConnected) {
                modal = document.createElement('div');
                modal.className = 'modal-backdrop-custom';
                modal.innerHTML = (
                    '<div class="modal-container-custom mx-4 max-w-md">' +
                    '<div class="modal-header-custom">' +
                    '<h3 class="text-xs font-bold uppercase tracking-wider text-amber-600">Peringatan Sesi</h3>' +
                    '</div>' +
                    '<div class="modal-body-custom leading-relaxed">' +
                    'Kamu tidak melakukan aktivitas selama 30 menit. Sesi akan berakhir dalam <strong id="idle-countdown">' + remaining + '</strong> detik.' +
                    '</div>' +
                    '<div class="modal-footer-custom">' +
                    '<button type="button" id="idle-resume" class="btn-primary">Lanjutkan Sesi</button>' +
                    '</div>' +
                    '</div>'
                );
                document.body.appendChild(modal);
                var resumeBtn = modal.querySelector('#idle-resume');
                if (resumeBtn) resumeBtn.addEventListener('click', touch);

                var last = parseInt(localStorage.getItem('pos_apotek_last_activity') || '0', 10);
                if (countdownTimer) clearInterval(countdownTimer);
                countdownTimer = setInterval(function () {
                    var el = modal ? modal.querySelector('#idle-countdown') : null;
                    if (!el) return;
                    var secs = Math.ceil((IDLE - (now() - last)) / 1000);
                    el.textContent = Math.max(secs, 0);
                }, 1000);
            } else {
                var el = modal.querySelector('#idle-countdown');
                if (el) el.textContent = Math.max(remaining, 0);
            }
        }

        function check() {
            var last = parseInt(localStorage.getItem('pos_apotek_last_activity') || '0', 10);
            if (!last) { touch(); return; }
            var elapsed = now() - last;
            if (elapsed >= IDLE) requestLogout();
            else if (elapsed >= IDLE - WARN) showWarning(Math.ceil((IDLE - elapsed) / 1000));
            else hideWarning();
        }

        function activityHandler() {
            var t = now();
            if (t - lastTouch >= 2000) { lastTouch = t; touch(); }
            hideWarning();
        }

        touch();

        var EVENTS = ['mousemove', 'mousedown', 'click', 'keydown', 'touchstart', 'scroll'];
        EVENTS.forEach(function (ev) {
            document.addEventListener(ev, activityHandler, { passive: true });
        });

        window.addEventListener('storage', function (e) {
            if (e.key === 'pos_apotek_logout_request' && e.newValue) {
                hideWarning();
                window.location.href = "/login";
            }
        });

        setInterval(check, TICK);
    })();
}

// Expose init functions for page-specific code to call if needed
window.Layout = {
    initSidebarExpand: initSidebarExpand,
    initSidebarGroupRestore: initSidebarGroupRestore,
    initSidebarScrollRestore: initSidebarScrollRestore,
    initToastAutoDismiss: initToastAutoDismiss,
    initShellInteractions: initShellInteractions,
    initIdleAutoLogout: initIdleAutoLogout,
    initAll: function () {
        initSidebarExpand();
        initSidebarGroupRestore();
        initSidebarScrollRestore();
        initToastAutoDismiss();
        initShellInteractions();
        initIdleAutoLogout();
    }
};

// Auto-init on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    window.Layout.initAll();
});

export { initSidebarExpand, initSidebarGroupRestore, initSidebarScrollRestore, initToastAutoDismiss, initShellInteractions, initIdleAutoLogout };