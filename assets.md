# Pedoman Standarisasi & Komponen UI (POS Apotek)

Dokumen ini adalah panduan baku arsitektur tampilan (Frontend/Blade), komponen UI, dan aturan penulisan kode untuk **Developer & AI Agent** yang bekerja di repositori POS Apotek.

---

## 1. Prinsip Utama (Core Principles)

1. **DRY (Don't Repeat Yourself)**: Dilarang menulis ulang markup HTML repetitif (seperti Header halaman, Filter card, Tombol Aksi tabel, Empty state, dan Modal). Selalu gunakan Blade Component yang sudah tersedia.
2. **Modal Form CRUD**: Untuk Master Data sederhana, **JANGAN** membuat file `create.blade.php` atau `edit.blade.php` terpisah. Seluruh form Create & Edit dipusatkan di `index.blade.php` menggunakan `<x-modal-form>`.
3. **No Inline CSS**: Dilarang keras menggunakan inline CSS (misal `style="color: #F59E0B;"`). Gunakan class Tailwind CSS atau class utility yang sudah didefinisikan di `resources/css/app.css`.

---

## 2. Katalog Blade Components (`resources/views/components/`)

### A. `<x-page-header>`
Digunakan di bagian paling atas halaman untuk judul, subtitle, dan tombol aksi (Tambah, Export, Import).
```blade
<x-page-header title="Daftar Kategori" subtitle="Kelola tipe penggolongan/kategori obat.">
    <button type="button" id="btn-tambah-kategori" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Kategori</span>
    </button>
</x-page-header>
```

---

### B. `<x-card-filter>`
Digunakan untuk area pencarian dan filter data.

**1. Pencarian Sederhana (Single Input):**
```blade
<x-card-filter :action="route('kategori.index')" :reset-url="route('kategori.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama kategori..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>
</x-card-filter>
```

**2. Filter Grid / Multi-Kolom (Dropdown & Input Banyak):**
```blade
<x-card-filter :action="route('barang.index')" :reset-url="route('barang.index')" :grid="true" grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-5">
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Barang</label>
        <input type="text" name="cari" value="{{ request('cari') }}" class="form-input" placeholder="Cari obat...">
    </div>
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Kategori</label>
        <select name="kategori_id" class="form-input">
            <option value="">Semua Kategori</option>
            @foreach ($kategoris as $k)
                <option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
</x-card-filter>
```

---

### C. `<x-table-action>`
Digunakan untuk tombol aksi di kolom `Aksi` tabel (Edit, Hapus, Detail).

**1. Aksi dengan Modal Edit:**
```blade
<x-table-action
    edit-class="btn-edit-kategori"
    :edit-id="$kategori->id"
    :edit-data="['nama' => $kategori->nama]"
    :delete-url="route('kategori.destroy', $kategori)"
    delete-confirm="Yakin ingin menghapus kategori ini?"
/>
```

**2. Aksi dengan Halaman Terpisah (Detail / Edit Page):**
```blade
<x-table-action
    :show-url="route('barang.show', $barang)"
    :edit-url="route('barang.edit', $barang)"
    :delete-url="route('barang.destroy', $barang)"
/>
```

---

### D. `<x-empty-state>`
Digunakan di dalam tag `@empty` pada tabel data.
```blade
<tbody class="table-custom-body divide-gray-150">
    @forelse ($items as $index => $item)
        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
            ...
        </tr>
    @empty
        <x-empty-state colspan="3" />
    @endforelse
</tbody>
```

---

### E. `<x-badge>`
Digunakan untuk pill/badge status data.
- `variant="success"` (Hijau)
- `variant="warning"` (Kuning/Amber)
- `variant="danger"` (Merah)
- `variant="info"` (Biru)
- `variant="secondary"` (Abu-abu)
```blade
<x-badge variant="success">Aktif</x-badge>
<x-badge variant="danger">Habis</x-badge>
```

---

### F. `<x-modal-form>`
Digunakan untuk modal popup Create & Edit dinamis.
```blade
<x-modal-form
    id="modal-kategori"
    create-title="Tambah Kategori"
    edit-title="Edit Kategori"
    create-url="{{ route('kategori.store') }}"
    update-base="{{ url('kategori') }}"
    create-btn="#btn-tambah-kategori"
    edit-btn=".btn-edit-kategori">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Kategori <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama kategori...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
</x-modal-form>
```

---

## 3. Class Utility CSS Wajib (`resources/css/app.css`)

| Elemen | Class Utility Baku |
|---|---|
| **Tombol Utama** | `btn-primary` |
| **Tombol Sekunder / Batal** | `btn-secondary` |
| **Container Card / Filter** | `card-base` |
| **Input Form / Select / Textarea** | `form-input` |
| **Wrapper Tabel** | `table-custom-container` |
| **Tabel** | `table-custom min-w-[50rem]` |
| **Header Tabel** | `table-custom-header` |
| **Body Tabel** | `table-custom-body` |
| **Kolom Angka / ID** | `table-num` |
| **Teks Keterangan Kecil** | `text-caption` |

---

## 4. Cetak Biru Standar CRUD (*Golden Master Template*)

Setiap pembuatan halaman Master Data baru wajib mencontoh struktur dari file referensi [resources/views/kategori/index.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/kategori/index.blade.php) atau [resources/views/satuan/index.blade.php](file:///c:/xampp/htdocs/pos-apotek/resources/views/satuan/index.blade.php).

```blade
@extends('layouts.app')
@section('title', 'Nama Modul')

@section('content')
{{-- 1. Header --}}
<x-page-header title="Daftar Modul" subtitle="Deskripsi modul...">
    <button type="button" id="btn-tambah-modul" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Data</span>
    </button>
</x-page-header>

{{-- 2. Filter & Pencarian --}}
<x-card-filter :action="route('modul.index')" :reset-url="route('modul.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>
</x-card-filter>

{{-- 3. Tabel Data --}}
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Data</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-modul"
                                :edit-id="$item->id"
                                :edit-data="['nama' => $item->nama]"
                                :delete-url="route('modul.destroy', $item)"
                                delete-confirm="Yakin ingin menghapus data ini?"
                            />
                        </td>
                        <td class="table-num">{{ $item->id }}</td>
                        <td class="font-medium text-gray-800">{{ $item->nama }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="3" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $items->links() }}</div>

{{-- 4. Modal Form Create & Edit --}}
<x-modal-form
    id="modal-modul"
    create-title="Tambah Data"
    edit-title="Edit Data"
    create-url="{{ route('modul.store') }}"
    update-base="{{ url('modul') }}"
    create-btn="#btn-tambah-modul"
    edit-btn=".btn-edit-modul">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
</x-modal-form>
@endsection
```
