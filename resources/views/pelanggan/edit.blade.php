@extends('layouts.app')
@section('title', 'Edit Pelanggan / Member')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                Edit Pelanggan / Member
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Ubah data profil pelanggan / member.
            </p>
        </div>

        <a href="{{ route('pelanggan.index') }}"
           class="btn-secondary py-2 px-4 shrink-0">
            ← Kembali
        </a>
    </div>


    {{-- Form --}}
    <form action="{{ route('pelanggan.update', $pelanggan) }}"
          method="POST"
          class="card-base">

        @csrf
        @method('PUT')


        {{-- Data Member --}}
        <div class="space-y-5">

            {{-- Member ID --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Member ID
                </label>

                <input
                    type="text"
                    value="{{ $pelanggan->member_id }}"
                    readonly
                    class="form-input bg-gray-100 text-gray-400 font-mono cursor-not-allowed"
                >

                <p class="text-[10px] text-gray-400 mt-1">
                    Member ID bersifat permanen dan tidak dapat diubah.
                </p>
            </div>


            {{-- Nama --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Nama Membership
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $pelanggan->nama) }}"
                    required
                    class="form-input"
                    placeholder="Masukkan nama pelanggan"
                >

                @error('nama')
                    <p class="text-red-600 text-[10px] mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Telepon --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Nomor HP / Telepon
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="telepon"
                    value="{{ old('telepon', $pelanggan->telepon) }}"
                    required
                    class="form-input"
                    placeholder="Contoh: 08123456789"
                >

                @error('telepon')
                    <p class="text-red-600 text-[10px] mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Alamat --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Alamat
                    <span class="text-red-500">*</span>
                </label>

                <textarea
                    name="alamat"
                    required
                    rows="3"
                    class="form-input resize-none"
                    placeholder="Masukkan alamat pelanggan"
                >{{ old('alamat', $pelanggan->alamat) }}</textarea>

                @error('alamat')
                    <p class="text-red-600 text-[10px] mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Tanggal Lahir --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Tanggal Lahir
                </label>

                <input
                    type="date"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', optional($pelanggan->tanggal_lahir)->format('Y-m-d')) }}"
                    class="form-input"
                >

                @error('tanggal_lahir')
                    <p class="text-red-600 text-[10px] mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>


        {{-- Footer Button --}}
        <div class="flex justify-end gap-2 border-t border-gray-200 mt-6 pt-5">

            <a href="{{ route('pelanggan.index') }}"
               class="btn-secondary py-2 px-5">
                Batal
            </a>

            <button
                type="submit"
                class="btn-primary py-2 px-5">
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection