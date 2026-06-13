@extends('layouts.admin')

@section('title', 'Tambah Investor Baru - PT/CV')

@section('header_title', 'Tambah Investor')
@section('header_subtitle', 'Daftarkan investor baru dan atur hak persentase pembagian dividen')

@section('content')
<div class="max-w-lg mx-auto bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
    <h3 class="text-xl font-bold text-slate-800 mb-6">Pendaftaran Investor</h3>

    <form action="{{ route('investors.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Tipe Akun -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Akun Pengguna</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="radio" name="account_type" value="new" checked class="text-blue-600 focus:ring-blue-500">
                    <span>Buat Akun Baru</span>
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="radio" name="account_type" value="existing" class="text-blue-600 focus:ring-blue-500">
                    <span>Pilih Akun yang Sudah Ada</span>
                </label>
            </div>
            @error('account_type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Investor -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Investor</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Contoh: Bapak Budi Santoso" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Persentase Saham -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Persentase Saham (%)</label>
            <input type="number" step="0.01" name="share_percentage" value="{{ old('share_percentage') }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Contoh: 30.00" min="0" max="100" required>
            @error('share_percentage')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Bagian Pengguna Baru -->
        <div id="new-user-fields" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Email Baru</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Contoh: budi@investor.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                <input type="password" name="password" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm" placeholder="Min. 8 karakter">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bagian Pengguna yang Sudah Ada -->
        <div id="existing-user-fields" style="display: none;">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Akun Pengguna</label>
            <select name="user_id" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm">
                <option value="">-- Pilih Akun --</option>
                @foreach($unlinkedUsers as $unlinkedUser)
                    <option value="{{ $unlinkedUser->id }}" {{ old('user_id') == $unlinkedUser->id ? 'selected' : '' }}>
                        {{ $unlinkedUser->name }} ({{ $unlinkedUser->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-4 border-t border-slate-100">
            <a href="{{ route('investors.index') }}" class="text-slate-500 hover:text-slate-800 font-medium text-sm transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition text-sm">
                Simpan Investor
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
        const newUserFields = document.getElementById('new-user-fields');
        const existingUserFields = document.getElementById('existing-user-fields');
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const userIdSelect = document.querySelector('select[name="user_id"]');

        function toggleFields() {
            const selectedType = document.querySelector('input[name="account_type"]:checked').value;
            if (selectedType === 'new') {
                newUserFields.style.display = 'block';
                existingUserFields.style.display = 'none';
                emailInput.setAttribute('required', 'required');
                passwordInput.setAttribute('required', 'required');
                userIdSelect.removeAttribute('required');
            } else {
                newUserFields.style.display = 'none';
                existingUserFields.style.display = 'block';
                emailInput.removeAttribute('required');
                passwordInput.removeAttribute('required');
                userIdSelect.setAttribute('required', 'required');
            }
        }

        accountTypeInputs.forEach(input => {
            input.addEventListener('change', toggleFields);
        });

        // Run initially
        toggleFields();
    });
</script>
@endpush
