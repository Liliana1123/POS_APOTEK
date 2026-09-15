@props([
    'colspan' => '100%',
    'title' => null,
    'message' => null,
    'search' => null,
])

@php
    $searchTerm = $search ?? request('cari');
    $defaultTitle = $searchTerm ? 'Data Tidak Ditemukan' : 'Data Kosong';
    $defaultDesc = $searchTerm
        ? 'Tidak ada data yang cocok dengan kata kunci "' . $searchTerm . '".'
        : 'Belum ada data terdaftar di sistem.';
@endphp

<tr>
    <td colspan="{{ $colspan }}" class="p-0">
        <div class="empty-state-container">
            <div class="empty-state-title">
                {{ $title ?? $defaultTitle }}
            </div>
            <div class="empty-state-desc">
                {{ $message ?? $defaultDesc }}
            </div>
        </div>
    </td>
</tr>
