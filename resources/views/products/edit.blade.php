@extends('layouts.admin')

@section('title', 'Edit Jasa / Produk - PT/CV')

@section('header_title', 'Edit Jasa / Produk')
@section('header_subtitle', 'Ubah detail nama jasa cuci sepatu atau harganya')

@section('content')
<div class="max-w-lg mx-auto bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
    <h3 class="text-xl font-bold text-slate-800 mb-6">Ubah Jasa / Produk</h3>

    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama Jasa -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Jasa / Produk</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" placeholder="Contoh: Deep Clean, Unyellowing, Repaint" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Harga Jasa -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Harga Jasa (Rp)</label>
            <input type="number" name="price" value="{{ old('price', (int)$product->price) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" placeholder="Contoh: 50000" min="0" required>
            @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-4 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-800 font-medium text-sm transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
