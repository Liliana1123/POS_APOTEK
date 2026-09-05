<div class="space-y-5">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                No. Faktur
            </span>
            <strong class="font-mono text-gray-800">
                {{ $penerimaan->no_faktur }}
            </strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                Supplier
            </span>
            <strong class="text-gray-800">
                {{ $penerimaan->supplier->nama ?? '—' }}
            </strong>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                Total Faktur
            </span>
            <strong class="font-mono text-gray-800">
                Rp {{ number_format($penerimaan->totalFaktur(), 0, ',', '.') }}
            </strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                Total Dibayar
            </span>
            <strong class="font-mono text-gray-800">
                Rp {{ number_format($penerimaan->totalDibayar(), 0, ',', '.') }}
            </strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                Sisa Tagihan
            </span>
            <strong class="font-mono text-gray-800">
                Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
            </strong>
        </div>
    </div>

        {{-- FORM PEMBAYARAN BARU --}}
    @if ($penerimaan->sisaTagihan() > 0)
        <div class="border-t border-gray-200 pt-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-800">
                    Tambah Pembayaran
                </h3>
                <p class="text-caption mt-1">
                    Masukkan pembayaran untuk mengurangi sisa tagihan.
                </p>
            </div>

            <form action="{{ route('penerimaan.payments.store', $penerimaan) }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">
                            Tanggal Bayar
                        </label>

                        <input
                            type="date"
                            name="tanggal_bayar"
                            value="{{ now()->format('Y-m-d') }}"
                            class="form-input"
                            required
                        >
                    </div>

                    <div>
                        <label class="form-label">
                            Jumlah Pembayaran
                        </label>

                        <input
                            type="number"
                            name="jumlah"
                            min="0.01"
                            max="{{ $penerimaan->sisaTagihan() }}"
                            step="0.01"
                            class="form-input"
                            placeholder="Masukkan jumlah"
                            required
                        >
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label">
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        rows="3"
                        class="form-input"
                        placeholder="Keterangan pembayaran (opsional)"
                    ></textarea>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- RIWAYAT PEMBAYARAN --}}
    <div class="border-t border-gray-200 pt-5">
        <div class="mb-4">
            <h3 class="text-sm font-bold text-gray-800">
                Riwayat Pembayaran
            </h3>
        </div>

        @if ($penerimaan->pembayaran->count())
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                Tanggal
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">
                                Jumlah
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                Keterangan
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                Dicatat Oleh
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($penerimaan->pembayaran->sortByDesc('tanggal_bayar') as $pembayaran)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $pembayaran->tanggal_bayar->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3 text-right font-mono font-semibold text-gray-800">
                                    Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $pembayaran->keterangan ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $pembayaran->user->name ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                Belum ada riwayat pembayaran.
            </div>
        @endif
    </div>

</div>