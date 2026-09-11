<div class="space-y-4">

    <div>
        <h4 class="text-lg font-semibold text-gray-800">
            Penerimaan Susulan
        </h4>

        <p class="text-sm text-gray-500 mt-1">
            No. Faktur: {{ $penerimaan->no_faktur }}
        </p>

        <p class="text-sm text-gray-500">
            Supplier: {{ $penerimaan->supplier->nama ?? '-' }}
        </p>
    </div>

    @if ($detailPesanan->isEmpty())

        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">
            Tidak ada barang yang masih kurang.
        </div>

    @else
        <form id="form-susulan-penerimaan" method="POST" action="{{ route('penerimaan.susulan.store', $penerimaan) }}">
            @csrf
            <div class="overflow-x-auto">
                {{-- Tanggal Terima --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Terima
                    </label>

                    <input
                        type="date"
                        name="tanggal_terima"
                        required
                        class="form-input w-full"
                    >
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        rows="2"
                        class="form-input w-full"
                        placeholder="Keterangan penerimaan susulan..."
                    >{{ old('keterangan') }}</textarea>
                </div>

                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left px-3 py-2">
                                Barang
                            </th>

                            <th class="text-center px-3 py-2">
                                Dipesan
                            </th>

                            <th class="text-center px-3 py-2">
                                Diterima
                            </th>

                            <th class="text-center px-3 py-2">
                                Kurang
                            </th>

                            <th class="text-center px-3 py-2">
                                Susulan
                            </th>

                            <th class="text-center px-3 py-2">
                                Pembatalan
                            </th>

                            <th class="text-center px-3 py-2">
                                No. Batch
                            </th>

                            <th class="text-center px-3 py-2">
                                Expired Date
                            </th>

                            <th class="text-center px-3 py-2">
                                Harga Beli
                            </th>

                            <th class="text-center px-3 py-2">
                                Harga Jual
                            </th>

                            <th class="text-center px-3 py-2">
                                No. Rak
                            </th>


                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($detailPesanan as $detail)
                            <tr class="border-b">

                                {{-- Barang --}}
                                <td class="px-3 py-3">
                                    {{ $detail->barang->nama ?? '-' }}
                                </td>

                                {{-- Dipesan --}}
                                <td class="text-center px-3 py-3">
                                    {{ $detail->jumlah_dipesan }}
                                </td>

                                {{-- Diterima --}}
                                <td class="text-center px-3 py-3">
                                    {{ $detail->totalDiterima() }}
                                </td>

                                {{-- Kurang --}}
                                <td class="text-center px-3 py-3">
                                    <span class="font-semibold">
                                        {{ $detail->kekurangan() }}
                                    </span>
                                </td>

                                {{-- Susulan --}}
                                <td class="text-center px-3 py-3">
                                    <input
                                        type="number"
                                        name="jumlah_susulan[{{ $detail->id }}]"
                                        min="0"
                                        max="{{ $detail->kekurangan() }}"
                                        value="0"
                                        class="w-24 rounded-md border border-gray-300 text-center px-2 py-1">
                                </td>

                                {{-- Pembatalan --}}
                                <td class="text-center px-3 py-3">
                                    <input
                                        type="number"
                                        name="jumlah_pembatalan[{{ $detail->id }}]"
                                        min="0"
                                        max="{{ $detail->kekurangan() }}"
                                        value="0"
                                        class="w-24 rounded-md border border-gray-300 text-center px-2 py-1">
                                </td>
                                
                                {{-- No. Batch --}}
                                <td class="px-3 py-3">
                                    <input
                                        type="text"
                                        name="detail[{{ $detail->id }}][no_batch]"
                                        required
                                        class="w-full rounded-md border border-gray-300 px-2 py-1"
                                        placeholder="Batch..."
                                    >
                                </td>

                                {{-- Expired Date --}}
                                <td class="px-3 py-3">
                                    <input
                                        type="date"
                                        name="detail[{{ $detail->id }}][expired_date]"
                                        required
                                        class="w-full rounded-md border border-gray-300 px-2 py-1"
                                    >
                                </td>

                                {{-- Harga Beli --}}
                                <td class="px-3 py-3">
                                    <input
                                        type="number"
                                        name="detail[{{ $detail->id }}][harga_beli]"
                                        min="0"
                                        step="0.01"
                                        required
                                        class="w-full rounded-md border border-gray-300 px-2 py-1 text-right"
                                    >
                                </td>

                                {{-- Harga Jual --}}
                                <td class="px-3 py-3">
                                    <input
                                        type="number"
                                        name="detail[{{ $detail->id }}][harga_jual]"
                                        min="0"
                                        step="0.01"
                                        required
                                        class="w-full rounded-md border border-gray-300 px-2 py-1 text-right"
                                    >
                                </td>

                                {{-- No. Rak --}}
                                <td class="px-3 py-3">
                                    <input
                                        type="text"
                                        name="detail[{{ $detail->id }}][no_rak]"
                                        required
                                        class="w-full rounded-md border border-gray-300 px-2 py-1"
                                        placeholder="A-01"
                                    >
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="flex justify-end pt-4">
                <button
                    type="submit"
                    id="btn-simpan-susulan"
                    class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                >
                    Simpan Penerimaan Susulan
                </button>
            </div>
        </form>
    @endif
    

</div>