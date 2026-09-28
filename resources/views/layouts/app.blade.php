<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Apotek')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $apotek = \Illuminate\Support\Facades\Cache::remember(
            'info_apotek',
            now()->addHours(6),
            fn () => \App\Models\InfoApotek::first()
        );
    @endphp
    <link rel="icon" href="{{ $apotek?->logo ? asset('storage/' . $apotek->logo) : asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 h-screen overflow-hidden flex">
    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden transition-opacity" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar" class="group fixed top-0 left-0 h-screen w-72 max-w-[85vw] bg-blue-700 border-r border-blue-800 flex flex-col justify-between z-50 shrink-0 self-start transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:w-24 lg:hover:w-64 transition-all duration-200 ease-in-out print:hidden">
        <script>
            (function () {
                var sidebar = document.getElementById('app-sidebar');
                function expandSidebar() {
                    sidebar.classList.add('sidebar-expanded');
                }
                function collapseSidebar() {
                    sidebar.classList.remove('sidebar-expanded');
                }
                sidebar.addEventListener('mouseenter', expandSidebar);
                sidebar.addEventListener('mouseleave', collapseSidebar);
                sidebar.addEventListener('focusin', expandSidebar);
                sidebar.addEventListener('focusout', collapseSidebar);
            })();
        </script>
        <!-- Pinned Sidebar Header -->
        <div class="sidebar-header shrink-0 px-5 py-5 flex justify-between items-center">
            @php
                $abbr = ($apotek?->nama_apotek ?? '')
                    ? collect(explode(' ', $apotek->nama_apotek))
                        ->take(2)
                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                        ->implode('')
                    : 'AK';
            @endphp
            <div class="font-bold text-base text-white tracking-wider flex items-center gap-3 sidebar-header-logo-wrapper">
                <div class="w-[3.5rem] h-[3.5rem] rounded-lg bg-white p-2 shrink-0 flex items-center justify-center shadow-md">
                    @if($apotek?->logo)
                        <img src="{{ asset('storage/' . $apotek->logo) }}"
                             alt="{{ $apotek?->nama_apotek }}"
                             class="w-full h-full object-contain object-center"
                             aria-hidden="true">
                    @else
                        <span class="text-blue-700 font-extrabold text-base">{{ $abbr }}</span>
                    @endif
                </div>
                <span class="sidebar-nav-label truncate whitespace-nowrap font-bold text-base tracking-wide uppercase">
                    {{ $apotek?->nama_apotek ?? 'APOTEK KITA' }}
                </span>
            </div>
            <button id="close-sidebar" class="lg:hidden text-blue-200 hover:text-white focus:outline-none" aria-label="Tutup menu">
                <x-heroicon-o-x-mark class="w-5 h-5" aria-hidden="true" />
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 min-h-0 overflow-y-auto sidebar-scroll-area flex flex-col">
            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-1.5">
                <x-nav-link
                    route="dashboard"
                    icon="home"
                    icon-type="s"
                    label="Dashboard"
                    variant="top"
                />

                <!-- Group: Master Data -->
                @php
                    $masterActive = request()->routeIs('barang.*') || request()->routeIs('kategori.*')
                        || request()->routeIs('satuan.*') || request()->routeIs('pabrik.*')
                        || request()->routeIs('supplier.*') || request()->routeIs('pelanggan.*')
                        || request()->routeIs('user.*') || request()->routeIs('pengaturan.*');
                @endphp
                @if (auth()->user()->isAdmin())
                    <x-sidebar-group name="master" icon="cube" icon-type="s" label="Data Master" :has-active="$masterActive">
                        <x-nav-link route="barang.index" pattern="barang.*" icon="archive-box" label="Barang / Produk" />
                        <x-nav-link route="kategori.index" pattern="kategori.*" icon="percent-badge" label="Kategori" />
                        <x-nav-link route="satuan.index" pattern="satuan.*" icon="scale" label="Satuan" />
                        <x-nav-link route="pabrik.index" pattern="pabrik.*" icon="building-storefront" label="Pabrik" />
                        <x-nav-link route="supplier.index" pattern="supplier.*" icon="building-office-2" label="Supplier" />
                        <x-nav-link route="pelanggan.index" pattern="pelanggan.*" icon="user" label="Pelanggan / Member" />
                        <x-nav-link route="user.index" pattern="user.*" icon="users" label="Kelola User" />
                        <x-nav-link route="pengaturan.index" pattern="pengaturan.*" icon="cog-6-tooth" label="Pengaturan Apotek" />
                    </x-sidebar-group>
                @endif

                <!-- Group: Transaksi -->
                @php
                    $transaksiActive = request()->routeIs('penjualan.*') || request()->routeIs('penerimaan.*')
                        || request()->routeIs('rusak.*');
                @endphp
                <x-sidebar-group name="transaksi" icon="banknotes" icon-type="s" label="Transaksi" :has-active="$transaksiActive">
                    <x-nav-link route="penjualan.index" pattern="penjualan.*" icon="shopping-cart" label="Penjualan (Kasir)" />
                    @if (auth()->user()->isAdmin())
                        <x-nav-link route="penerimaan.index" pattern="penerimaan.*" icon="truck" label="Penerimaan barang" />
                        <x-nav-link route="rusak.index" pattern="rusak.*" icon="exclamation-triangle" label="Barang rusak" />
                    @endif
                </x-sidebar-group>

                <!-- Group: Laporan (Admin Only) -->
                @if (auth()->user()->isAdmin())
                    <x-sidebar-group name="laporan" icon="chart-bar" icon-type="s" label="Laporan" :has-active="request()->routeIs('laporan.*')">
                        <x-nav-link route="laporan.stok" icon="document-chart-bar" label="Laporan stok" />
                        <x-nav-link route="laporan.penerimaan" icon="document-duplicate" label="Laporan penerimaan" />
                        <x-nav-link route="laporan.penjualan" icon="currency-dollar" label="Laporan penjualan" />
                        <x-nav-link route="laporan.rusak" icon="no-symbol" label="Laporan barang rusak" />
                        <x-nav-link route="laporan.laba-rugi" icon="chart-pie" label="Laporan laba-rugi" />
                        <x-nav-link route="laporan.diskon" icon="ticket" label="Laporan diskon" />
                    </x-sidebar-group>
                @endif

                <!-- Group: Promo & Log (Admin Only) -->
                @if (auth()->user()->isAdmin())
                    @php
                        $promoActive = request()->routeIs('custom-discount.*') || request()->routeIs('activity-log');
                    @endphp
                    <x-sidebar-group name="promo" icon="gift" icon-type="s" label="Promo & Log" :has-active="$promoActive">
                        <x-nav-link route="custom-discount.index" pattern="custom-discount.*" icon="tag" label="Custom Discount" />
                        <x-nav-link route="activity-log" icon="book-open" label="Log Aktivitas" />
                    </x-sidebar-group>
                @endif

            </nav>
        </div>

        <!-- Restore sidebar group state synchronously (blocking) to prevent flicker -->
        <script>
            (function () {
                document.querySelectorAll('.sidebar-group').forEach(function (group) {
                    var name = group.dataset.group;
                    var saved = localStorage.getItem('sidebar-group-' + name);
                    var items = group.querySelector('.sidebar-group-items');
                    if (!items) return;
                    var expand = saved === '1' || items.dataset.hasActive === 'true';
                    if (expand) {
                        items.style.maxHeight = '999px';
                        items.setAttribute('data-expanded', '1');
                        var btn = group.querySelector('.sidebar-group-header');
                        if (btn) btn.setAttribute('aria-expanded', 'true');
                        var arrow = items.previousElementSibling ? items.previousElementSibling.querySelector('.sidebar-group-arrow') : null;
                        if (arrow) arrow.classList.add('rotate-90');
                    }
                });
            })();
        </script>

        <!-- Sidebar Profile Card (Bottom) -->
        <div class="sidebar-footer shrink-0 px-6 py-4 border-t border-blue-600 bg-blue-800 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-full bg-white text-blue-700 font-bold flex items-center justify-center shrink-0 text-xs shadow-md" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-footer-info min-w-0 overflow-hidden whitespace-nowrap">
                    <span class="block text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="block text-[9px] font-bold text-blue-200 uppercase tracking-wider truncate">{{ auth()->user()->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-footer-logout shrink-0 overflow-hidden">
                @csrf
                <button type="submit" class="text-blue-200 hover:text-red-400 transition-colors p-1" title="Logout" aria-label="Logout">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" aria-hidden="true" />
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto overflow-x-hidden">
        <!-- Top Navbar (Sticky) -->
        <nav class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-5 py-3 flex justify-between items-center print:hidden shadow-xs">
            <div class="flex items-center gap-3">
                <button id="mobile-sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5" aria-hidden="true" />
                </button>
                <span class="font-bold text-sm text-gray-800 uppercase tracking-wide">@yield('title', 'Dashboard')</span>
            </div>
            
            <div class="flex items-center gap-3 md:gap-4 text-xs">
                <!-- Real-time Clock Widget -->
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-lg text-slate-700 shadow-2xs font-medium">
                    <x-heroicon-o-clock class="w-4 h-4 text-blue-600 shrink-0" />
                    <span id="realtime-clock" class="font-mono text-[11px] sm:text-xs tracking-tight">--:--:--</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="inline md:hidden">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1" aria-label="Logout">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" aria-hidden="true" />
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Wrapper -->
        <main class="flex-1 p-6 w-full pb-12">
            @yield('content')
        </main>
    </div>

    <!-- Session Flash Toast Notifications -->
    @if (session('success'))
    <div id="toast-success" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800" role="status" aria-live="polite">
        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 shrink-0" aria-hidden="true" />
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('toast-success').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm" aria-label="Tutup notifikasi">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 3500);
    </script>
    @endif

    @if (session('error'))
    <div id="toast-error" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800" role="alert" aria-live="assertive">
        <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 shrink-0" aria-hidden="true" />
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('toast-error').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm" aria-label="Tutup notifikasi">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-error');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    </script>
    @endif

    <!-- Shell Interactions & Global Logic Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('app-sidebar');
            const toggle = document.getElementById('mobile-sidebar-toggle');
            const backdrop = document.getElementById('sidebar-backdrop');
            const closeBtn = document.getElementById('close-sidebar');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) backdrop.classList.remove('hidden');
                    const scrollArea = sidebar.querySelector('.sidebar-scroll-area');
                    if (scrollArea) scrollArea.scrollTop = 0;
                });
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }

            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Escape key to close sidebar drawer
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            // Sidebar Group Accordion Toggle
            document.querySelectorAll('.sidebar-group-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    const group = this.closest('.sidebar-group');
                    const items = group.querySelector('.sidebar-group-items');
                    const arrow = this.querySelector('.sidebar-group-arrow');
                    const groupName = group.dataset.group;

                    const isExpanded = items.getAttribute('data-expanded') === '1';

                    if (isExpanded) {
                        items.style.maxHeight = '0px';
                        items.setAttribute('data-expanded', '0');
                        this.setAttribute('aria-expanded', 'false');
                        localStorage.setItem('sidebar-group-' + groupName, '0');
                    } else {
                        items.style.maxHeight = '999px';
                        items.setAttribute('data-expanded', '1');
                        this.setAttribute('aria-expanded', 'true');
                        localStorage.setItem('sidebar-group-' + groupName, '1');
                    }
                    arrow.classList.toggle('rotate-90');
                });
            });
        });

        // Delete Form Confirmation Modal Interceptor
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.method.toLowerCase() === 'post' && form.querySelector('input[name="_method"][value="DELETE"]')) {
                if (!form.dataset.confirmed) {
                    e.preventDefault();
                    const modal = document.createElement('div');
                    modal.className = 'modal-backdrop-custom';
                    modal.innerHTML = `
                        <div class="modal-container-custom mx-4">
                            <div class="modal-header-custom">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-red-600">Konfirmasi Hapus</h3>
                                <button type="button" id="confirm-x" class="text-gray-400 hover:text-gray-600 font-bold text-base">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                            <div class="modal-body-custom leading-relaxed">
                                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan dapat memengaruhi integritas relasi data transaksi.
                            </div>
                            <div class="modal-footer-custom">
                                <button type="button" id="confirm-cancel" class="btn-secondary">Batal</button>
                                <button type="button" id="confirm-submit" class="btn-destructive">Hapus</button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    const closeModal = () => modal.remove();
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

        // Universal Form Submission Loading States to prevent double-submit
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.action.includes('logout') || form.dataset.confirmed === 'false') {
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                setTimeout(() => {
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
                
                el.textContent = dayName + ', ' + date + ' ' + monthName + ' ' + year + ' • ' + hours + ':' + minutes + ':' + seconds + ' WIB';
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>

    <!-- Auto Logout Saat Idle (30 menit, sinkron antar tab) -->
    <script>
        (function () {
            var IDLE = 30 * 60 * 1000;      // 30 menit tanpa aktivitas
            var WARN = 30 * 1000;           // peringatan 30 detik sebelum logout
            var TICK = 5000;                // interval pengecekan tiap 5 detik
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
                fetch('{{ route('logout') }}', {
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
                if (modal) {
                    modal.remove();
                    modal = null;
                }
                if (countdownTimer) {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                }
            }

            function showWarning(remaining) {
                if (!modal || !modal.isConnected) {
                    modal = document.createElement('div');
                    modal.className = 'modal-backdrop-custom';
                    modal.innerHTML = '' +
                        '<div class="modal-container-custom mx-4">' +
                            '<div class="modal-header-custom">' +
                                '<h3 class="text-xs font-bold uppercase tracking-wider text-amber-600">Peringatan Sesi</h3>' +
                            '</div>' +
                            '<div class="modal-body-custom leading-relaxed">' +
                                'Kamu tidak melakukan aktivitas selama 30 menit. Sesi akan berakhir dalam <strong id="idle-countdown">' + remaining + '</strong> detik.' +
                            '</div>' +
                            '<div class="modal-footer-custom">' +
                                '<button type="button" id="idle-resume" class="btn-primary">Lanjutkan Sesi</button>' +
                            '</div>' +
                        '</div>';
                    document.body.appendChild(modal);
                    var resumeBtn = modal.querySelector('#idle-resume');
                    if (resumeBtn) resumeBtn.addEventListener('click', touch);

                    var last = parseInt(localStorage.getItem(KEY_LAST) || '0', 10);
                    var shown = remaining;
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
                var last = parseInt(localStorage.getItem(KEY_LAST) || '0', 10);
                if (!last) { touch(); return; }
                var elapsed = now() - last;
                if (elapsed >= IDLE) {
                    requestLogout();
                } else if (elapsed >= IDLE - WARN) {
                    showWarning(Math.ceil((IDLE - elapsed) / 1000));
                } else {
                    hideWarning();
                }
            }

            function activityHandler() {
                var t = now();
                if (t - lastTouch >= 2000) {
                    lastTouch = t;
                    touch();
                }
                hideWarning();
            }

            touch();

            var EVENTS = ['mousemove', 'mousedown', 'click', 'keydown', 'touchstart', 'scroll'];
            EVENTS.forEach(function (ev) {
                document.addEventListener(ev, activityHandler, { passive: true });
            });

            window.addEventListener('storage', function (e) {
                if (e.key === KEY_LOGOUT && e.newValue) {
                    hideWarning();
                    window.location.href = "/login";
                }
            });

            setInterval(check, TICK);
        })();
    </script>
</body>

</html>