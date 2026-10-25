@extends('layouts.app')
@section('title', 'Izin Akses')

@section('content')
<x-page-header 
    title="Izin Akses" 
    subtitle="Kelola hak akses menu navigasi aplikasi serta visibilitas kartu dan widget pada Dashboard." 
/>

<form method="POST" action="{{ route('permission.update') }}" id="form-permission">
    @csrf

    {{-- TAB SWITCHER --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-gray-200">
        <div class="inline-flex p-1 bg-gray-100 rounded-xl space-x-1 border border-gray-200">
            <button type="button" 
                    id="tab-btn-pages" 
                    onclick="switchPermissionTab('pages')"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 bg-white text-blue-700 shadow-xs">
                <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                <span>Izin Menu & Halaman</span>
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-100 text-blue-800 font-extrabold">{{ $pagePermissions->count() }}</span>
            </button>
            <button type="button" 
                    id="tab-btn-dashboard" 
                    onclick="switchPermissionTab('dashboard')"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <x-heroicon-o-chart-bar class="w-4 h-4" />
                <span>Widget Dashboard</span>
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-200 text-gray-700 font-extrabold">{{ $dashboardPermissions->flatten()->count() }}</span>
            </button>
        </div>

        <div class="text-xs text-gray-500 font-medium">
            <span id="tab-desc-pages">Atur hak buka halaman (Access) dan manipulasi data (CRUD).</span>
            <span id="tab-desc-dashboard" class="hidden">Atur kartu metrik KPI dan komponen widget yang tampil di Dashboard.</span>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 1: IZIN MENU & HALAMAN                               --}}
    {{-- ======================================================== --}}
    <div id="tab-content-pages" class="tab-pane">
        <div class="table-custom-container">
            <table class="table-custom min-w-[56rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th rowspan="2" scope="col" class="w-12 text-center">No</th>
                        <th rowspan="2" scope="col" class="w-44">Modul / Menu</th>
                        <th rowspan="2" scope="col" class="w-72">Halaman / Fitur</th>
                        @foreach ($roles as $role => $meta)
                            <th colspan="2" scope="colgroup" class="text-center border-l border-blue-500">
                                {{ $meta['label'] }}
                                @if ($meta['kunci'] ?? false)
                                    <span class="block text-[10px] font-normal text-blue-200 normal-case">selalu aktif</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($roles as $role => $meta)
                            <th scope="col" class="text-center bg-blue-100 text-blue-900 w-24 border-l border-blue-500">Access</th>
                            <th scope="col" class="text-center bg-blue-100 text-blue-900 w-24">CRUD</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-custom-body">
                    @php $no = 1; @endphp
                    @foreach ($pagePermissions as $perm)
                        <tr class="{{ $no % 2 === 1 ? 'bg-white' : 'bg-gray-50/75' }} hover:bg-blue-50/30 transition-colors">
                            <td class="text-center text-gray-400 font-medium">{{ $no++ }}</td>
                            <td class="text-gray-600 text-xs font-semibold">
                                <span class="badge-chip bg-slate-100 text-slate-700">{{ $perm->group }}</span>
                            </td>
                            <td class="font-bold text-gray-800 text-sm">{{ $perm->label }}</td>

                            @foreach ($roles as $role => $meta)
                                @foreach (['view' => 'Access', 'manage' => 'CRUD'] as $level => $label)
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="{{ $level }}[{{ $role }}][{{ $perm->id }}]"
                                            value="1"
                                            @checked($matrix[$perm->id][$role][$level] ?? false)
                                            @disabled($meta['kunci'] ?? false)
                                            aria-label="{{ $label }} {{ $meta['label'] }} — {{ $perm->label }}"
                                            title="{{ $meta['label'] }} · {{ $label }}"
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer disabled:cursor-not-allowed">
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 2: WIDGET DASHBOARD                                  --}}
    {{-- ======================================================== --}}
    <div id="tab-content-dashboard" class="tab-pane hidden">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 mb-4 text-xs text-amber-800 flex items-center gap-2.5">
            <x-heroicon-o-information-circle class="w-5 h-5 text-amber-600 shrink-0" />
            <div>
                <strong>Pengaturan Visibilitas Dashboard:</strong> Centang pada kolom role untuk mengaktifkan kartu metrik atau komponen widget bersangkutan. Jika tidak dicentang, widget akan disembunyikan sepenuhnya dari layar pengguna.
            </div>
        </div>

        <div class="table-custom-container">
            <table class="table-custom min-w-[56rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-12 text-center">No</th>
                        <th scope="col" class="w-48">Kategori</th>
                        <th scope="col" class="w-80">Nama Kartu / Widget Dashboard</th>
                        @foreach ($roles as $role => $meta)
                            <th scope="col" class="text-center border-l border-blue-500 w-32">
                                {{ $meta['label'] }}
                                @if ($meta['kunci'] ?? false)
                                    <span class="block text-[10px] font-normal text-blue-200 normal-case">selalu aktif</span>
                                @else
                                    <span class="block text-[10px] font-normal text-blue-200 normal-case">Tampilkan</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-custom-body">
                    @php $dashNo = 1; @endphp
                    @foreach ($dashboardPermissions as $groupName => $items)
                        {{-- Row Pemisah Kategori --}}
                        <tr class="bg-blue-50/80 border-t-2 border-b border-blue-200">
                            <td colspan="{{ 3 + count($roles) }}" class="py-2 px-4 text-xs font-black text-blue-900 uppercase tracking-wider">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    {{ str_replace('Dashboard: ', '', $groupName) }}
                                    <span class="text-[10px] font-bold text-blue-600 lowercase">({{ $items->count() }} item)</span>
                                </span>
                            </td>
                        </tr>

                        @foreach ($items as $perm)
                            <tr class="{{ $dashNo % 2 === 1 ? 'bg-white' : 'bg-gray-50/75' }} hover:bg-blue-50/30 transition-colors">
                                <td class="text-center text-gray-400 font-medium">{{ $dashNo++ }}</td>
                                <td class="text-gray-500 text-xs font-semibold">
                                    {{ str_replace('Dashboard: ', '', $perm->group) }}
                                </td>
                                <td class="font-bold text-gray-800 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $perm->label }}</span>
                                    </div>
                                </td>

                                @foreach ($roles as $role => $meta)
                                    <td class="text-center border-l border-gray-100">
                                        <input type="checkbox"
                                            name="view[{{ $role }}][{{ $perm->id }}]"
                                            value="1"
                                            @checked($matrix[$perm->id][$role]['view'] ?? false)
                                            @disabled($meta['kunci'] ?? false)
                                            aria-label="Tampilkan {{ $perm->label }} untuk {{ $meta['label'] }}"
                                            title="Tampilkan untuk {{ $meta['label'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer disabled:cursor-not-allowed">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex items-center justify-between gap-3 mt-6 pt-4 border-t border-gray-200">
        <span class="text-xs text-gray-500">Perubahan izin akses langsung aktif seketika setelah disimpan.</span>
        
        <div class="flex items-center gap-3">
            <button type="submit"
                    formaction="{{ route('permission.reset') }}"
                    onclick="return confirm('Reset semua izin akses ke default sistem? Tindakan ini menimpa pengaturan saat ini.')"
                    class="btn-secondary">
                Reset ke Default
            </button>

            <button type="submit" class="btn-primary">
                Simpan Izin
            </button>
        </div>
    </div>
</form>

<script>
function switchPermissionTab(tab) {
    const tabPages = document.getElementById('tab-content-pages');
    const tabDashboard = document.getElementById('tab-content-dashboard');
    const btnPages = document.getElementById('tab-btn-pages');
    const btnDashboard = document.getElementById('tab-btn-dashboard');
    const descPages = document.getElementById('tab-desc-pages');
    const descDashboard = document.getElementById('tab-desc-dashboard');

    if (tab === 'pages') {
        tabPages.classList.remove('hidden');
        tabDashboard.classList.add('hidden');
        descPages.classList.remove('hidden');
        descDashboard.classList.add('hidden');

        btnPages.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 bg-white text-blue-700 shadow-xs';
        btnDashboard.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 text-gray-600 hover:text-gray-900';
        localStorage.setItem('permission_active_tab', 'pages');
    } else {
        tabPages.classList.add('hidden');
        tabDashboard.classList.remove('hidden');
        descPages.classList.add('hidden');
        descDashboard.classList.remove('hidden');

        btnDashboard.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 bg-white text-blue-700 shadow-xs';
        btnPages.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2 text-gray-600 hover:text-gray-900';
        localStorage.setItem('permission_active_tab', 'dashboard');
    }
}

// Restore saved tab on load
document.addEventListener('DOMContentLoaded', () => {
    const savedTab = localStorage.getItem('permission_active_tab');
    if (savedTab === 'dashboard') {
        switchPermissionTab('dashboard');
    }
});
</script>
@endsection
