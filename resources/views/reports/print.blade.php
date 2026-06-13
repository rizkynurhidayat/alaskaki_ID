<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Alaskakii</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }
        @media print {
            body {
                background-color: transparent;
                color: #000000;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</head>
<body class="p-6 md:p-12 max-w-4xl mx-auto">

    <!-- Top Navigation Bar for Screen Only -->
    <div class="no-print flex items-center justify-between mb-8 bg-slate-50 p-4 rounded-xl border border-slate-200">
        <div class="text-sm text-slate-500">
            <strong>Pratinjau Cetak PDF</strong>. Tekan tombol cetak atau gunakan jalan pintas Ctrl+P.
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 border border-slate-200 hover:bg-slate-100 rounded-lg text-sm font-medium transition">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-3a2 2 0 00-2-2H5a2 2 0 00-2 2v3a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Header Invoice/Laporan -->
    <div class="border-b-2 border-slate-800 pb-6 mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-wider">Alas<span class="text-blue-600 font-light">kakii</span></h1>
            <p class="text-xs text-slate-500 mt-1">Jasa Cuci Sepatu Premium & Perhitungan Profit Share</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-slate-800">Laporan Keuangan</h2>
            <p class="text-sm text-slate-600 font-medium mt-0.5">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        @if($user->role === 'superadmin')
        <div class="border border-slate-200 rounded-xl p-4">
            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ringkasan Pendapatan</h4>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Total Pemasukan:</span>
                    <span class="font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>HPP / Belanja Bahan:</span>
                    <span>-Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-1 text-slate-900 font-bold">
                    <span>Laba Kotor (Gross Profit):</span>
                    <span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="border border-slate-200 rounded-xl p-4">
            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Laba Bersih & Dividen</h4>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Laba Kotor:</span>
                    <span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-500">
                    <span>Biaya Operasional:</span>
                    <span>-Rp {{ number_format($totalOperational, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-1 font-bold">
                    <span>Laba Bersih Akhir:</span>
                    <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-dashed border-slate-200 pt-1 text-blue-600 font-bold">
                    <span>Dividen Investor ({{ $investorSharePercentage }}%):</span>
                    <span>Rp {{ number_format($estimatedDividend, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @else
        <!-- Investor Mode Summary Box -->
        <div class="border border-slate-200 rounded-xl p-4 col-span-2">
            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ringkasan Laba & Dividen</h4>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-slate-50 p-3 rounded-lg">
                    <span class="text-xs text-slate-500 block mb-1">Total Pemasukan</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-lg">
                    <span class="text-xs text-slate-500 block mb-1">Laba Kotor</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 font-bold">
                    <span class="text-xs text-blue-600 block mb-1">Dividen Investor ({{ $investorSharePercentage }}%)</span>
                    <span class="font-bold text-blue-700">Rp {{ number_format($estimatedDividend, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Table of Transactions -->
    <div class="mb-8">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Detail Transaksi</h3>
        <table class="w-full text-left border-collapse text-xs print-border">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-300">
                    <th class="p-3 font-semibold">Tanggal</th>
                    <th class="p-3 font-semibold">Keterangan</th>
                    <th class="p-3 font-semibold text-center">Jenis/Kategori</th>
                    <th class="p-3 font-semibold text-right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($transactions as $trx)
                    <tr class="align-middle">
                        <td class="p-3 text-slate-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                        <td class="p-3 font-medium text-slate-900">
                            {{ $trx->description }}
                            @if($trx->product)
                                <span class="block text-[10px] text-blue-600 font-normal">Jasa: {{ $trx->product->name }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-center whitespace-nowrap capitalize">
                            {{ $trx->type === 'income' ? 'Pemasukan' : 'Pengeluaran (' . ($trx->category ?? 'Lainnya') . ')' }}
                        </td>
                        <td class="p-3 text-right font-bold text-slate-900">
                            {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer Signatures -->
    <div class="mt-16 grid grid-cols-2 gap-12 text-center text-xs">
        <div>
            <p class="text-slate-400 mb-16">Disiapkan Oleh,</p>
            <p class="font-bold text-slate-900 underline">Admin Keuangan</p>
            <p class="text-slate-500">Alaskakii Team</p>
        </div>
        <div>
            <p class="text-slate-400 mb-16">Diketahui Oleh,</p>
            <p class="font-bold text-slate-900 underline">Bapak Investor</p>
            <p class="text-slate-500">Mitra Profit Share</p>
        </div>
    </div>

    <script>
        // Auto trigger browser print window after page load
        window.onload = function() {
            // Slight timeout to let fonts/css render correctly
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
