<div class="space-y-4">
    <!-- Info Cards Grid (Selaras dengan Detail Penerimaan) -->
    <div class="card-base p-4 mb-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Terima</span>
            <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->tanggal, 'd F Y')</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Faktur</span>
            <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->tanggal_faktur, 'd F Y')</strong>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">No. Faktur</span>
            <strong class="text-gray-800 text-sm">{{ $penerimaan->no_faktur }}</strong>
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
                <x-status-badge type="pembayaran" :status="$penerimaan->lunas" />
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Status Penerimaan</span>
            <div class="mt-0.5">
                <x-status-badge type="penerimaan" :status="$penerimaan->statusPenerimaan()" />
            </div>
        </div>

        @if ($penerimaan->jatuh_tempo)
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Jatuh Tempo</span>
            <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->jatuh_tempo, 'd F Y')</strong>
        </div>
        @endif
    </div>

    @if ($detailPesanan->isEmpty())
    <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Semua pesanan barang pada faktur ini sudah lengkap diterima atau diselesaikan.</span>
    </div>
    @else
    <form id="form-susulan-penerimaan" method="POST" action="{{ route('penerimaan.susulan.store', $penerimaan, false) }}" data-no-loading>
        @csrf

        <!-- Form Input Tanggal & Keterangan -->
        <div class="card-base p-4 mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Tanggal Terima Susulan <span class="text-red-500 font-bold">*</span>
                </label>
                <input
                    type="date"
                    name="tanggal_terima"
                    value="{{ date('Y-m-d') }}"
                    required
                    class="form-input">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Keterangan atau Catatan Susulan
                </label>
                <input
                    type="text"
                    name="keterangan"
                    class="form-input"
                    placeholder="Contoh: Penerimaan susulan sisa kekurangan..."
                    value="{{ old('keterangan') }}">
            </div>
        </div>

        <!-- Alert Banner Error (Bila Terjadi Kesalahan Input) -->
        <div id="susulan-error-banner" class="hidden rounded-lg bg-red-50 border border-red-200 p-3 text-xs text-red-700 items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="susulan-error-text"></span>
        </div>

        <!-- Tabel Daftar Barang Kekurangan -->
        <div class="card-base p-0 mb-4 border border-slate-200">

            <div class="overflow-x-auto overflow-y-auto max-h-[430px]">

                <table class="table-susulan-penerimaan">
                    <thead>
                        <tr>
                            <th scope="col" class="w-10 text-center">NO</th>
                            <th scope="col" class="min-w-[13rem]">BARANG</th>
                            <th scope="col" class="text-right w-16">DIPESAN</th>
                            <th scope="col" class="text-right w-16">DITERIMA</th>
                            <th scope="col" class="text-right w-16">KURANG</th>
                            <th scope="col" class="text-center w-24">SUSULAN</th>
                            <th scope="col" class="text-center w-24">BATAL</th>
                            <th scope="col" class="w-28 text-center">NO. BATCH</th>
                            <th scope="col" class="w-32 text-center">EXPIRED</th>
                            <th scope="col" class="w-28 text-center">HARGA BELI</th>
                            <th scope="col" class="w-28 text-center">HARGA JUAL</th>
                            <th scope="col" class="w-16 text-center">RAK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($detailPesanan as $index => $detail)
                        @php
                        $barang = $detail->barang;
                        $latestDetail = $penerimaan->detail->where('barang_id', $detail->barang_id)->last();
                        $defaultBatch = $latestDetail?->no_batch ?? '';
                        $defaultExpired = $latestDetail?->expired_date ? \Carbon\Carbon::parse($latestDetail->expired_date)->format('Y-m-d') : '';
                        $defaultHargaBeli = $latestDetail?->harga_beli ?? $barang?->harga_beli ?? '';
                        $defaultHargaJual = $latestDetail?->harga_jual ?? $barang?->harga_jual ?? '';
                        $defaultRak = $latestDetail?->no_rak ?? $barang?->no_rak ?? '';
                        $kekurangan = $detail->kekurangan();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- NO --}}
                            <td class="text-center text-slate-600 font-semibold">{{ $index + 1 }}</td>

                            {{-- BARANG --}}
                            <td class="break-words">
                                <div class="text-slate-900 font-semibold break-words">
                                    {{ $barang->nama ?? '-' }}
                                </div>

                                <div class="text-[10px] text-slate-500 font-normal leading-tight break-words">
                                    Satuan: {{ $barang->satuan->nama ?? '-' }} |
                                    Pabrik: {{ $barang->pabrik->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- DIPESAN --}}
                            <td class="text-center text-slate-900 font-semibold">
                                {{ number_format($detail->jumlah_dipesan) }}
                            </td>

                            {{-- DITERIMA --}}
                            <td class="text-center text-blue-700 font-semibold">
                                {{ number_format($detail->totalDiterima()) }}
                            </td>

                            {{-- KURANG --}}
                            <td class="text-center text-amber-600 font-semibold">
                                {{ number_format($kekurangan) }}
                            </td>

                            {{-- INPUT SUSULAN --}}
                            <td class="text-center">
                                <input
                                    type="number"
                                    name="jumlah_susulan[{{ $detail->id }}]"
                                    min="0"
                                    max="{{ $kekurangan }}"
                                    value="0"
                                    class="form-input !py-1 text-center font-semibold text-emerald-700 border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500 w-full mx-auto">
                            </td>

                            {{-- INPUT PEMBATALAN --}}
                            <td class="text-center">
                                <input
                                    type="number"
                                    name="jumlah_pembatalan[{{ $detail->id }}]"
                                    min="0"
                                    max="{{ $kekurangan }}"
                                    value="0"
                                    class="form-input !py-1 text-center font-semibold text-red-600 border-red-300 focus:border-red-500 focus:ring-red-500 w-full mx-auto">
                            </td>

                            {{-- NO. BATCH --}}
                            <td>
                                <input
                                    type="text"
                                    name="detail[{{ $detail->id }}][no_batch]"
                                    value="{{ $defaultBatch }}"
                                    class="form-input !py-1 text-xs w-full text-center font-mono"
                                    placeholder="Batch...">
                            </td>

                            {{-- EXPIRED DATE --}}
                            <td>
                                <input
                                    type="date"
                                    name="detail[{{ $detail->id }}][expired_date]"
                                    value="{{ $defaultExpired }}"
                                    class="form-input !py-1 text-xs w-full">
                            </td>

                            {{-- HARGA BELI --}}
                            <td>
                                <input
                                    type="number"
                                    name="detail[{{ $detail->id }}][harga_beli]"
                                    min="0"
                                    step="0.01"
                                    value="{{ $defaultHargaBeli }}"
                                    class="form-input !py-1 text-xs text-right font-mono w-full"
                                    placeholder="0">
                            </td>

                            {{-- HARGA JUAL --}}
                            <td>
                                <input
                                    type="number"
                                    name="detail[{{ $detail->id }}][harga_jual]"
                                    min="0"
                                    step="0.01"
                                    value="{{ $defaultHargaJual }}"
                                    class="form-input !py-1 text-xs text-right font-mono w-full"
                                    placeholder="0">
                            </td>

                            {{-- NO. RAK --}}
                            <td>
                                <input
                                    type="text"
                                    name="detail[{{ $detail->id }}][no_rak]"
                                    value="{{ $defaultRak }}"
                                    class="form-input !py-1 text-xs text-center w-full"
                                    placeholder="Rak">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
            <p class="text-xs text-slate-500 text-center sm:text-left">
                <span class="font-semibold text-slate-700">* Petunjuk:</span> Masukkan jumlah <strong>Susulan</strong> (serta data batch dan harga) untuk barang yang datang, atau isi <strong>Batal</strong> bila pesanan dibatalkan supplier.
            </p>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button
                    type="button"
                    id="btn-batal-susulan"
                    class="btn-secondary w-full sm:w-auto">
                    Batal
                </button>

                <button
                    type="submit"
                    id="btn-simpan-susulan"
                    class="btn-primary w-full sm:w-auto whitespace-nowrap">
                    Simpan Penerimaan Susulan
                </button>
            </div>
        </div>
    </form>
    @endif
</div>