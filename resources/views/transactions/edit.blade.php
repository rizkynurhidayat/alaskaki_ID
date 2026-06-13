@extends('layouts.admin')

@section('title', 'Edit Transaksi - PT/CV')

@section('header_title', 'Edit Transaksi')
@section('header_subtitle', 'Ubah data transaksi pemasukan atau pengeluaran')

@section('content')
<div class="max-w-lg mx-auto bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
    <h3 class="text-xl font-bold text-slate-800 mb-6">Ubah Transaksi</h3>

    <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Jenis Transaksi -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Transaksi</label>
            <select name="type" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" required>
                <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>Pemasukan (+)</option>
                <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>Pengeluaran (-)</option>
            </select>
            @error('type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Dropdown Produk (Hanya Muncul jika Pemasukan) -->
        <div id="product-select-container">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk / Layanan</label>
            <select id="product_id" name="product_id" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                <option value="" data-price="" data-name="">-- Pemasukan Lainnya (Input Manual) --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ (int)$product->price }}" data-name="{{ $product->name }}" {{ old('product_id', $transaction->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kategori Pengeluaran (Hanya Muncul jika Pengeluaran) -->
        <div id="category-select-container">
            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Pengeluaran</label>
            <select name="category" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                <option value="hpp" {{ old('category', $transaction->category) === 'hpp' ? 'selected' : '' }}>HPP / Belanja Bahan</option>
                <option value="operational" {{ old('category', $transaction->category) === 'operational' ? 'selected' : '' }}>Operasional</option>
            </select>
            @error('category')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nominal -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nominal (Rp)</label>
            <input type="number" name="amount" value="{{ old('amount', (int)$transaction->amount) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" placeholder="Contoh: 150000" min="1" required>
            @error('amount')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Transaksi</label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date) }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" required>
            @error('transaction_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Keterangan</label>
            <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition" placeholder="Contoh: Pembelian sabun pembersih" required>{{ old('description', $transaction->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-4 border-t border-slate-100">
            <a href="{{ route('transactions.index') }}" class="text-slate-500 hover:text-slate-800 font-medium text-sm transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const productContainer = document.getElementById('product-select-container');
        const productSelect = document.getElementById('product_id');
        const categoryContainer = document.getElementById('category-select-container');
        const categorySelect = document.querySelector('select[name="category"]');
        const amountInput = document.querySelector('input[name="amount"]');
        const descriptionInput = document.querySelector('textarea[name="description"]');

        let isInitialLoad = true;

        function handleTypeChange() {
            if (typeSelect.value === 'income') {
                productContainer.style.display = 'block';
                categoryContainer.style.display = 'none';
                categorySelect.removeAttribute('required');
            } else {
                productContainer.style.display = 'none';
                categoryContainer.style.display = 'block';
                categorySelect.setAttribute('required', 'required');
                productSelect.value = '';
                amountInput.readOnly = false;
            }
            handleProductChange();
        }

        function handleProductChange() {
            if (typeSelect.value !== 'income') {
                return;
            }
            
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const name = selectedOption.getAttribute('data-name');

            if (price && name) {
                // Only overwrite input values on user selection, not on initial load
                if (!isInitialLoad) {
                    amountInput.value = price;
                    descriptionInput.value = 'Pemasukan Jasa Cuci Sepatu: ' + name;
                }
                amountInput.readOnly = true;
            } else {
                amountInput.readOnly = false;
            }
        }

        typeSelect.addEventListener('change', function() {
            isInitialLoad = false;
            handleTypeChange();
        });
        
        productSelect.addEventListener('change', function() {
            isInitialLoad = false;
            handleProductChange();
        });

        // Run initially to set correct state based on old/existing values
        handleTypeChange();
        isInitialLoad = false; // set to false after initial render setup
    });
</script>
@endpush
