<style>
    .table-payment-penerimaan {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
        text-align: left;
    }

    .table-payment-penerimaan th {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.65rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-bottom: 1px solid #1d4ed8 !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
    }

    .table-payment-penerimaan td {
        padding: 0.55rem 0.75rem !important;
        font-size: 0.75rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle;
    }
</style>

<div class="space-y-4">
    <!-- Info Cards Grid (Selaras dengan Form Susulan & Detail Penerimaan) -->
    <div class="card-base p-4 mb-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Terima</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->tanggal?->format('d M Y') ?? '-' }}</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Faktur</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->tanggal_faktur?->format('d M Y') ?? '-' }}</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">No. Faktur</span>
            <strong class="text-gray-800 text-sm font-mono">{{ $penerimaan->no_faktur }}</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Supplier</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->supplier->nama ?? '-' }}</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Dicatat Oleh</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->user->name ?? '-' }}</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Status Pembayaran</span>
            <div class="mt-0.5">
                @if ($penerimaan->lunas)
                    <span class="badge-success">Lunas</span>
                @else
                    <span class="badge-warning">Belum Lunas</span>
                @endif
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Status Penerimaan</span>
            <div class="mt-0.5">
                @if ($penerimaan->statusPenerimaan() === 'LENGKAP')
                    <span class="badge-success">Lengkap</span>
                @else
                    <span class="badge-warning">Belum Lengkap</span>
                @endif
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Jatuh Tempo</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->jatuh_tempo ? $penerimaan->jatuh_tempo->format('d M Y') : '-' }}</strong>
        </div>
    </div>

    <!-- Ringkasan Tagihan & Keuangan -->
    <div class="card-base p-4 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 text-xs">
            <div class="p-3 bg-slate-50/80 rounded-lg border border-slate-200/80">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Total Belanja</span>
                <strong class="font-mono text-gray-800 text-sm">Rp {{ number_format($penerimaan->totalFaktur(), 0, ',', '.') }}</strong>
            </div>

            <div class="p-3 bg-slate-50/80 rounded-lg border border-slate-200/80">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">PPN</span>
                <strong class="font-mono text-gray-800 text-sm">Rp {{ number_format($penerimaan->ppn, 0, ',', '.') }}</strong>
            </div>

            <div class="p-3 bg-blue-50/50 rounded-lg border border-blue-200">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-600 mb-1">Total Tagihan</span>
                <strong class="font-mono font-bold text-blue-700 text-sm">Rp {{ number_format($penerimaan->totalTagihan(), 0, ',', '.') }}</strong>
            </div>

            <div class="p-3 bg-emerald-50/50 rounded-lg border border-emerald-200">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Total Dibayar</span>
                <strong class="font-mono font-bold text-emerald-600 text-sm">Rp {{ number_format($penerimaan->totalDibayar(), 0, ',', '.') }}</strong>
            </div>

            <div class="p-3 rounded-lg border {{ $penerimaan->sisaTagihan() > 0 ? 'bg-rose-50/50 border-rose-200 text-rose-700' : 'bg-emerald-50/50 border-emerald-200 text-emerald-700' }}">
                <span class="block text-[10px] font-bold uppercase tracking-wider {{ $penerimaan->sisaTagihan() > 0 ? 'text-rose-600' : 'text-emerald-600' }} mb-1">Sisa Tagihan</span>
                <strong class="font-mono font-bold text-sm">Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}</strong>
            </div>
        </div>

        @if ($penerimaan->kelebihanPembayaran() > 0)
            <div class="mt-3 p-2.5 bg-amber-50 rounded-lg border border-amber-200 flex justify-between items-center text-xs">
                <span class="font-bold text-amber-800 uppercase tracking-wider text-[10px]">Kelebihan Pembayaran</span>
                <strong class="font-mono text-amber-700 text-sm font-bold">Rp {{ number_format($penerimaan->kelebihanPembayaran(), 0, ',', '.') }}</strong>
            </div>
        @endif
    </div>

    @if ($penerimaan->sisaTagihan() > 0)
        <form action="{{ route('penerimaan.payments.store', $penerimaan) }}" method="POST">
            @csrf

            <!-- Form Input Pembayaran Baru (Selaras dengan Input Susulan) -->
            <div class="card-base p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        Tanggal Bayar <span class="text-red-500 font-bold">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal_bayar"
                        value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}"
                        class="form-input"
                        required
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        Jumlah Pembayaran <span class="text-red-500 font-bold">*</span>
                    </label>
                    <input
                        type="number"
                        name="jumlah"
                        min="0.01"
                        max="{{ $penerimaan->sisaTagihan() }}"
                        step="0.01"
                        value="{{ old('jumlah') }}"
                        class="form-input font-mono text-right"
                        placeholder="0"
                        required
                    >
                    <span class="text-[11px] text-gray-400 mt-1 block">Maks. Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        Keterangan / Catatan Pembayaran
                    </label>
                    <input
                        type="text"
                        name="keterangan"
                        value="{{ old('keterangan') }}"
                        class="form-input"
                        placeholder="Contoh: Transfer Bank BCA, Tunai, dsb."
                    >
                </div>
            </div>

            <!-- Tabel Riwayat Pembayaran -->
            <div class="card-base p-0 mb-4 border border-slate-200 overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Riwayat Pembayaran Faktur</span>
                    <span class="text-[11px] text-gray-500">{{ $penerimaan->pembayaran->count() }} transaksi dicatat</span>
                </div>

                <div class="overflow-x-auto max-h-[260px] overflow-y-auto">
                    <table class="table-payment-penerimaan">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">No</th>
                                <th class="w-36 text-left">Tanggal</th>
                                <th class="w-44 text-right">Jumlah</th>
                                <th class="text-left">Keterangan</th>
                                <th class="w-44 text-left">Dicatat Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($penerimaan->pembayaran->sortByDesc('tanggal_bayar') as $pembayaran)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="text-center text-slate-500 font-medium">{{ $loop->iteration }}</td>
                                    <td class="text-slate-700 font-medium">
                                        {{ $pembayaran->tanggal_bayar?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="text-right font-mono font-semibold text-emerald-600">
                                        Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="text-slate-600">
                                        {{ $pembayaran->keterangan ?: '—' }}
                                    </td>
                                    <td class="text-slate-600">
                                        {{ $pembayaran->user->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-xs text-gray-400 italic">
                                        Belum ada riwayat pembayaran untuk faktur ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Action Buttons (Selaras Form Susulan) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <p class="text-xs text-slate-500 text-center sm:text-left">
                    <span class="font-semibold text-slate-700">* Petunjuk:</span> Masukkan nominal pembayaran untuk mengurangi sisa tagihan hutang faktur ini.
                </p>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button
                        type="button"
                        class="btn-secondary w-full sm:w-auto"
                        onclick="document.getElementById('modal-payment-penerimaan').classList.add('hidden')"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-primary w-full sm:w-auto whitespace-nowrap"
                    >
                        Simpan Pembayaran
                    </button>
                </div>
            </div>
        </form>
    @else
        <!-- Banner Lunas (Selaras Alert Susulan) -->
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 mb-4 text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Tagihan faktur ini sudah <strong>Lunas</strong>. Tidak ada sisa tagihan yang perlu dibayar.</span>
        </div>

        <!-- Tabel Riwayat Pembayaran -->
        <div class="card-base p-0 mb-4 border border-slate-200 overflow-hidden">
            <div class="px-4 py-2.5 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Riwayat Pembayaran Faktur</span>
                <span class="text-[11px] text-gray-500">{{ $penerimaan->pembayaran->count() }} transaksi dicatat</span>
            </div>

            <div class="overflow-x-auto max-h-[300px] overflow-y-auto">
                <table class="table-payment-penerimaan">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th class="w-36 text-left">Tanggal</th>
                            <th class="w-44 text-right">Jumlah</th>
                            <th class="text-left">Keterangan</th>
                            <th class="w-44 text-left">Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($penerimaan->pembayaran->sortByDesc('tanggal_bayar') as $pembayaran)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="text-center text-slate-500 font-medium">{{ $loop->iteration }}</td>
                                <td class="text-slate-700 font-medium">
                                    {{ $pembayaran->tanggal_bayar?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="text-right font-mono font-semibold text-emerald-600">
                                    Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-slate-600">
                                    {{ $pembayaran->keterangan ?: '—' }}
                                </td>
                                <td class="text-slate-600">
                                    {{ $pembayaran->user->name ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-xs text-gray-400 italic">
                                    Belum ada riwayat pembayaran untuk faktur ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Action Button -->
        <div class="flex items-center justify-end gap-2 pt-2">
            <button
                type="button"
                class="btn-secondary w-full sm:w-auto"
                onclick="document.getElementById('modal-payment-penerimaan').classList.add('hidden')"
            >
                Tutup
            </button>
        </div>
    @endif
</div>