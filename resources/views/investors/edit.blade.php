@extends('layouts.admin')

@section('title', 'Edit Investor - PT/CV')

@section('header_title', 'Edit Investor')
@section('header_subtitle', 'Ubah detail nama investor atau persentase saham dividen')

@section('content')
<div class="max-w-lg mx-auto bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
    <h3 class="text-xl font-bold text-slate-800 mb-6">Ubah Profil Investor</h3>

    <form action="{{ route('investors.update', $investor) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama Investor -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Investor</label>
            <input type="text" name="name" value="{{ old('name', $investor->name) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Contoh: Bapak Budi Santoso" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Persentase Saham -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Persentase Saham (%)</label>
            <input type="number" step="0.01" name="share_percentage" value="{{ old('share_percentage', $investor->share_percentage) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Contoh: 30.00" min="0" max="100" required>
            @error('share_percentage')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Info Tambahan Akun Pengguna -->
        @if($investor->user)
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-150">
            <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Informasi Akun Terkait</h4>
            <p class="text-sm text-slate-700 font-medium">Email: <span class="font-normal">{{ $investor->user->email }}</span></p>
            <p class="text-xs text-slate-400 mt-1">Nama akun pengguna akan diperbarui secara otomatis jika nama investor di atas diubah.</p>
        </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-4 border-t border-slate-100">
            <a href="{{ route('investors.index') }}" class="text-slate-500 hover:text-slate-800 font-medium text-sm transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition text-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
