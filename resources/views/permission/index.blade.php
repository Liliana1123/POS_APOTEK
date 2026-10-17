@extends('layouts.app')
@section('title', 'Izin Akses')

@section('content')
<x-page-header title="Izin Akses" subtitle="Centang Access untuk membuka halaman, CRUD untuk menambah, ubah, dan hapus data." />

<form method="POST" action="{{ route('permission.update') }}">
    @csrf

    <div class="card-base overflow-hidden">
        <div class="table-custom-container">
            <table class="table-custom min-w-[56rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th rowspan="2" scope="col" class="w-12 text-center">No</th>
                        <th rowspan="2" scope="col" class="w-40">Group</th>
                        <th rowspan="2" scope="col" class="w-72">Halaman</th>
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
                    @foreach ($permissions as $perm)
                        <tr class="{{ $loop->iteration % 2 === 1 ? 'bg-white' : 'bg-gray-200' }}">
                            <td class="text-center text-gray-400">{{ $loop->iteration }}</td>
                            <td class="text-gray-600 text-sm">{{ $perm->group }}</td>
                            <td class="font-medium text-gray-800">{{ $perm->label }}</td>

                            @foreach ($roles as $role => $meta)
                                @foreach (['view' => 'Access', 'manage' => 'CRUD'] as $level => $label)
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="{{ $level }}[{{ $role }}][{{ $perm->id }}]"
                                            value="1"
                                            @checked($matrix[$perm->id][$role][$level])
                                            @disabled($meta['kunci'] ?? false)
                                            aria-label="{{ $label }} {{ $meta['label'] }} — {{ $perm->label }}"
                                            title="{{ $meta['label'] }} · {{ $label }}"
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <button type="submit"
                formaction="{{ route('permission.reset') }}"
                onclick="return confirm('Reset semua izin akses ke default sistem? Tindakan ini menimpa pengaturan saat ini.')"
                class="btn-secondary">
            Reset ke Default
        </button>

        <button type="submit" class="btn-primary">Simpan Izin</button>
    </div>
</form>
@endsection
