@extends('layouts.admin')

@section('title', 'Ekspor Laporan Keuangan - PT/CV')

@section('header_title', 'Ekspor Laporan')
@section('header_subtitle', 'Filter dan unduh laporan laba bersih serta pembagian dividen')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Filter Card -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Rentang Waktu
        </h3>
        
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Rentang</label>
                <select name="range_type" id="range_type" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm">
                    <option value="weekly" {{ $rangeType === 'weekly' ? 'selected' : '' }}>Mingguan (Minggu Ini)</option>
                    <option value="monthly" {{ $rangeType === 'monthly' ? 'selected' : '' }}>Bulanan (Bulan Ini)</option>
                    <option value="yearly" {{ $rangeType === 'yearly' ? 'selected' : '' }}>Tahunan (Tahun Ini)</option>
                    <option value="custom" {{ $rangeType === 'custom' ? 'selected' : '' }}>Kustom (Pilih Tanggal)</option>
                </select>
            </div>
            
            <div id="custom-dates-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:col-span-2" style="display: none;">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-sm">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium p-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    Tampilkan
                </button>
                <a href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium p-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-3a2 2 0 00-2-2H5a2 2 0 00-2 2v3a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Preview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card Pemasukan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <p class="text-sm font-medium text-slate-500 mb-1">Total Pemasukan</p>
                <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                <span class="text-xs text-slate-400 mt-1 block">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Card Laba Kotor -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <p class="text-sm font-medium text-slate-500 mb-1">Laba Kotor</p>
                <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h3>
                @if($user->role === 'superadmin')
                <span class="text-xs text-slate-400 mt-1 block">HPP Belanja: Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                @endif
            </div>
        </div>

        <!-- Card Dividen -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-5 rounded-2xl shadow-lg relative overflow-hidden group text-white">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <p class="text-sm font-medium text-indigo-100 mb-1">Dividen Investor ({{ $investorSharePercentage }}%)</p>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($estimatedDividend, 0, ',', '.') }}</h3>
                <span class="text-xs text-indigo-200 mt-1 block">Diambil dari Laba Kotor</span>
            </div>
        </div>
    </div>

    @if($user->role === 'superadmin')
    <!-- Laba Bersih Card -->
    <div class="bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-sm font-medium text-slate-400 mb-1">Laba Bersih Setelah Semua Pengeluaran</p>
            <h3 class="text-3xl font-extrabold text-white">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
        </div>
        <div class="text-xs text-slate-400 space-y-1 sm:text-right">
            <p>HPP Belanja: Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
            <p>Operasional: Rp {{ number_format($totalOperational, 0, ',', '.') }}</p>
            <p>Total Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
    </div>
    @endif

    <!-- Transactions Table Card -->
    <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Detail Transaksi Terkait</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="p-4 font-medium rounded-tl-xl">Tanggal</th>
                        <th class="p-4 font-medium">Keterangan</th>
                        <th class="p-4 font-medium text-center">Jenis/Kategori</th>
                        <th class="p-4 font-medium text-right rounded-tr-xl">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-slate-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                            <td class="p-4 text-slate-800 font-medium">
                                {{ $trx->description }}
                                @if($trx->product)
                                    <span class="block text-xs text-blue-500 font-normal mt-0.5">Produk: {{ $trx->product->name }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($trx->type === 'income')
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Pemasukan</span>
                                @else
                                    @if($trx->category === 'hpp')
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Pengeluaran (HPP)</span>
                                    @elseif($trx->category === 'operational')
                                        <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Pengeluaran (Ops)</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Pengeluaran</span>
                                    @endif
                                @endif
                            </td>
                            <td class="p-4 text-right font-semibold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">Tidak ada transaksi pada rentang waktu terpilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rangeTypeSelect = document.getElementById('range_type');
        const customDatesContainer = document.getElementById('custom-dates-container');

        function toggleCustomDates() {
            if (rangeTypeSelect.value === 'custom') {
                customDatesContainer.style.display = 'grid';
            } else {
                customDatesContainer.style.display = 'none';
            }
        }

        rangeTypeSelect.addEventListener('change', toggleCustomDates);
        
        // Initial setup
        toggleCustomDates();
    });
</script>
@endpush
