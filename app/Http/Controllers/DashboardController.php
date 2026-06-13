<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ambil semua transaksi bulan ini
        $transactions = Transaction::whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        
        // Logika Hitung Laba Bersih
        $netProfit = $totalIncome - $totalExpense;
        
        // Hitung estimasi Dividen 30%
        $investorSharePercentage = 30; // Bisa juga diambil dinamis dari tabel Investor nantinya
        $estimatedDividend = ($netProfit > 0) ? ($netProfit * ($investorSharePercentage / 100)) : 0;

        // Ambil 5 transaksi terbaru untuk tabel
        $recentTransactions = Transaction::orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        return view('welcome', compact(
            'totalIncome', 'totalExpense', 'netProfit', 'estimatedDividend', 'recentTransactions'
        ));
    }
}
