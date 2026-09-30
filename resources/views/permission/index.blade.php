@extends('layouts.app')
@section('title', 'Izin Akses')

@section('content')
<x-page-header title="Izin Akses" subtitle="Centang Lihat untuk membuka halaman, Kelola untuk dapat menambah, ubah, dan hapus data." />

<form method="POST" action="{{ route('permission.update') }}">
    @csrf

    <div class="space-y-6">
        @php $grupSekarang = null; @endphp

        @foreach ($permissions as $perm)
            @if ($perm->group !== $grupSekarang)
                @php $grupSekarang = $perm->group; @endphp
                <h3 class="text-sm font-semibold text-gray-900 mt-6 first:mt-0">{{ $grupSekarang }}</h3>
            @endif

            <div class="card-base overflow-hidden">
                <div class="table-custom-container">
                    <table class="table-custom min-w-[64rem]">
                        <thead class="table-custom-header">
                            <tr>
                                <th scope="col" class="w-64">Halaman</th>
                                @foreach ($roles as $role => $meta)
                                    <th scope="col" class="text-center w-32">
                                        {{ $meta['label'] }}
                                        @if ($meta['kunci'] ?? false)
                                            <span class="block text-[10px] font-normal text-gray-400">selalu aktif</span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="table-custom-body divide-gray-150">
                            <tr>
                                <td class="font-medium text-gray-800">{{ $perm->label }}</td>

                                @foreach ($roles as $role => $meta)
                                    <td class="text-center">
                                        @if ($meta['kunci'] ?? false)
                                            <span class="text-gray-300 text-xs">—</span>
                                        @else
                                            <div class="inline-flex items-center gap-4">
                                                @foreach (['view' => 'Lihat', 'manage' => 'Kelola'] as $level => $label)
                                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox"
                                                            name="{{ $level }}[{{ $role }}][{{ $perm->id }}]"
                                                            value="1"
                                                            @checked($matrix[$perm->id][$role][$level])
                                                            data-permission-toggle
                                                            data-role="{{ $role }}"
                                                            data-permission="{{ $perm->id }}"
                                                            class="peer sr-only">
                                                        <span class="relative h-5 w-9 rounded-full bg-gray-300 transition-colors
                                                                    peer-checked:bg-blue-600 peer-focus-visible:ring-2
                                                                    peer-focus-visible:ring-blue-400 peer-focus-visible:ring-offset-1
                                                                    after:absolute after:top-0.5 after:left-0.5 after:h-4 after:w-4
                                                                    after:rounded-full after:bg-white after:transition-transform
                                                                    peer-checked:after:translate-x-4"></span>
                                                        <span class="text-xs text-gray-600 font-sans">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-gray-100">
        <button type="submit" class="btn-primary">Simpan Izin</button>
    </div>
</form>

<form method="POST" action="{{ route('permission.reset') }}"
      class="mt-3"
      onsubmit="return confirm('Reset semua izin akses ke default sistem? Tindakan ini menimpa pengaturan saat ini.')">
    @csrf
    <button type="submit" class="btn-secondary">Reset ke Default</button>
</form>
@endsection
