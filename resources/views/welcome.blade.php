@extends('layouts.admin')

@section('title', 'Dashboard Laba & Saham - PT/CV')

@section('header_title', 'Ringkasan Keuangan')
@section('header_subtitle', 'Periode: ' . \Carbon\Carbon::now()->translatedFormat('F Y'))

@section('content')
<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card 1 -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <p class="text-sm font-medium text-slate-500 mb-1">Total Pemasukan</p>
            <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
    </div>

    @if($user->role === 'admin')
    <!-- Card 2 -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <p class="text-sm font-medium text-slate-500 mb-1">Total Pengeluaran</p>
            <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1">HPP: Rp {{ number_format($totalHpp, 0, ',', '.') }} | Ops: Rp {{ number_format($totalExpense - $totalHpp, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-4 sm:p-6 shadow-lg relative overflow-hidden group text-white">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <p class="text-sm font-medium text-slate-300 mb-1">Laba Bersih Bulan Ini</p>
            <h3 class="text-2xl font-bold text-white">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Laba Kotor: Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
        </div>
    </div>
    @endif

    <!-- Card 4 -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 sm:p-6 shadow-lg shadow-indigo-200/50 relative overflow-hidden group text-white">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <p class="text-sm font-medium text-indigo-100 mb-1">Dividen Investor ({{ $investorSharePercentage }}%)</p>
            <h3 class="text-2xl font-bold text-white">Rp {{ number_format($estimatedDividend, 0, ',', '.') }}</h3>
            @if($user->role === 'investor')
            <div class="mt-2 flex items-center gap-2">
                @if($weeklyDividendChange > 0)
                    <span class="bg-green-400/30 text-green-100 px-2 py-0.5 rounded flex items-center gap-1 text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        +{{ number_format($weeklyDividendChange, 1, ',', '.') }}%
                    </span>
                @elseif($weeklyDividendChange < 0)
                    <span class="bg-red-400/30 text-red-100 px-2 py-0.5 rounded flex items-center gap-1 text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        {{ number_format($weeklyDividendChange, 1, ',', '.') }}%
                    </span>
                @else
                    <span class="bg-slate-400/30 text-slate-100 px-2 py-0.5 rounded flex items-center gap-1 text-xs font-medium">
                        0%
                    </span>
                @endif
                <span class="text-indigo-200 text-xs">vs minggu lalu</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Charts & Tables Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Chart -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100 lg:col-span-2">
        <h3 class="font-semibold text-slate-800 text-lg mb-6">Tren Laba (Data Tersimulasi)</h3>
        <div class="relative h-[300px] w-full">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100 flex flex-col">
        <h3 class="font-semibold text-slate-800 text-lg mb-6">Transaksi Terbaru</h3>
        <div class="space-y-4 flex-1">
            @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer">
                    <div>
                        <p class="font-medium text-slate-800 text-sm">{{ $transaction->description }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
                    </div>
                    <span class="font-semibold text-sm {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type == 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada transaksi.</p>
            @endforelse
        </div>
        @if($user->role === 'admin')
        <a href="{{ route('transactions.create') }}" class="block text-center mt-4 w-full py-2 border border-dashed border-slate-300 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-700 transition-colors text-sm font-medium">
            + Tambah Transaksi
        </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    
    const userRole = '{{ $user->role }}';
    
    if (userRole === 'admin') {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Laba Bersih', 'HPP / Belanja Bahan', 'Operasional'],
                datasets: [{
                    data: [
                        {{ max(0, $netProfit) }}, 
                        {{ $totalHpp }}, 
                        {{ $totalExpense - $totalHpp }}
                    ],
                    backgroundColor: ['#10b981', '#f43f5e', '#f97316'], // Hijau, Merah, Orange
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom' } 
                }
            }
        });
    } else {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Bulan Ini'],
                datasets: [{
                    label: 'Dividen Anda (Juta Rp)',
                    data: [5, 6, 5.5, 7, 6.5, {{ $estimatedDividend / 1000000 }}],
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false } 
                }
            }
        });
    }
</script>
@endpush
