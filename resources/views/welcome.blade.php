<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Laba & Saham - PT/CV</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col transition-all duration-300">
        <div class="h-20 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-2xl font-bold tracking-wider bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Profit<span class="font-light text-white">Share</span></h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-600/20 text-blue-400 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-y-auto bg-[#F8FAFC]">
        <!-- Header -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Ringkasan Keuangan</h2>
                <p class="text-sm text-slate-500">Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
        </header>

        <!-- Content Padding -->
        <div class="p-8 space-y-8">
            
            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Pemasukan</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Pengeluaran</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-lg relative overflow-hidden group text-white">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-slate-300 mb-1">Laba Bersih Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-white">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-200/50 relative overflow-hidden group text-white">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <p class="text-sm font-medium text-indigo-100 mb-1">Dividen Investor (30%)</p>
                        <h3 class="text-2xl font-bold text-white">Rp {{ number_format($estimatedDividend, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Charts & Tables Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Chart -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 lg:col-span-2">
                    <h3 class="font-semibold text-slate-800 text-lg mb-6">Tren Laba (Data Tersimulasi)</h3>
                    <div class="relative h-[300px] w-full">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col">
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
                </div>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('profitChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Bulan Ini'],
                datasets: [{
                    label: 'Laba Bersih',
                    data: [25, 30, 28, 35, 32, {{ $netProfit / 1000000 }}],
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
