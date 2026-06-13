@extends('layouts.admin')

@section('title', 'Manajemen Produk & Layanan - PT/CV')

@section('header_title', 'Produk & Layanan Jasa')
@section('header_subtitle', 'Kelola daftar produk dan harga jasa cuci sepatu')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-xl font-bold text-slate-800">Daftar Jasa Cuci Sepatu</h3>
        <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-blue-700 shadow-sm shadow-blue-200 hover:shadow-md transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Jasa / Produk
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium rounded-tl-xl">Nama Jasa / Produk</th>
                    <th class="p-4 font-medium text-right">Harga Jasa (Rp)</th>
                    <th class="p-4 font-medium text-center rounded-tr-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-slate-850 font-medium">{{ $product->name }}</td>
                        <td class="p-4 text-right font-semibold text-slate-700">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-slate-500">Belum ada data produk / layanan. Silakan tambahkan.</td>
                    </tr>
                @endempty
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection
