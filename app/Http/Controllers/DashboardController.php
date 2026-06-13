<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Investor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $transactions = Transaction::whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalHpp = $transactions->where('type', 'expense')->where('category', 'hpp')->sum('amount');
        
        // Laba Kotor = Pemasukan - HPP
        $grossProfit = $totalIncome - $totalHpp;
        
        // Laba Bersih = Pemasukan - Pengeluaran (HPP + Operasional)
        $netProfit = $totalIncome - $totalExpense;
        
        $investorSharePercentage = 30; // Persentase default jika admin yang lihat
        
        if ($user->role === 'investor') {
            $investor = Investor::where('user_id', $user->id)->first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 0;
        }

        // Dividen dihitung dari Laba Kotor
        $estimatedDividend = ($grossProfit > 0) ? ($grossProfit * ($investorSharePercentage / 100)) : 0;

        // Hitung persentase kenaikan/penurunan dari minggu sebelumnya
        $startOfCurrentWeek = Carbon::now()->startOfWeek();
        $endOfCurrentWeek = Carbon::now()->endOfWeek();
        $startOfPrevWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfPrevWeek = Carbon::now()->subWeek()->endOfWeek();

        // Hitung laba kotor minggu berjalan
        $currentWeekIncome = Transaction::whereBetween('transaction_date', [$startOfCurrentWeek, $endOfCurrentWeek])->where('type', 'income')->sum('amount');
        $currentWeekHpp = Transaction::whereBetween('transaction_date', [$startOfCurrentWeek, $endOfCurrentWeek])->where('type', 'expense')->where('category', 'hpp')->sum('amount');
        $currentWeekGrossProfit = $currentWeekIncome - $currentWeekHpp;

        // Hitung laba kotor minggu lalu
        $prevWeekIncome = Transaction::whereBetween('transaction_date', [$startOfPrevWeek, $endOfPrevWeek])->where('type', 'income')->sum('amount');
        $prevWeekHpp = Transaction::whereBetween('transaction_date', [$startOfPrevWeek, $endOfPrevWeek])->where('type', 'expense')->where('category', 'hpp')->sum('amount');
        $prevWeekGrossProfit = $prevWeekIncome - $prevWeekHpp;

        // Dividen masing-masing minggu dihitung dari laba kotor
        $currentWeekDividend = ($currentWeekGrossProfit > 0) ? ($currentWeekGrossProfit * ($investorSharePercentage / 100)) : 0;
        $prevWeekDividend = ($prevWeekGrossProfit > 0) ? ($prevWeekGrossProfit * ($investorSharePercentage / 100)) : 0;

        if ($prevWeekDividend > 0) {
            $weeklyDividendChange = (($currentWeekDividend - $prevWeekDividend) / $prevWeekDividend) * 100;
        } elseif ($currentWeekDividend > 0) {
            $weeklyDividendChange = 100;
        } else {
            $weeklyDividendChange = 0;
        }

        $recentTransactions = Transaction::orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        return view('welcome', compact(
            'totalIncome', 'totalExpense', 'totalHpp', 'grossProfit', 'netProfit', 'estimatedDividend', 'recentTransactions', 'user', 'investorSharePercentage', 'weeklyDividendChange'
        ));
    }
}
