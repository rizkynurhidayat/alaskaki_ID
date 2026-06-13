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
        $netProfit = $totalIncome - $totalExpense;
        
        $investorSharePercentage = 30; // Persentase default jika admin yang lihat
        
        if ($user->role === 'investor') {
            $investor = Investor::where('user_id', $user->id)->first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 0;
        }

        $estimatedDividend = ($netProfit > 0) ? ($netProfit * ($investorSharePercentage / 100)) : 0;

        // Hitung persentase kenaikan/penurunan dari minggu sebelumnya
        $startOfCurrentWeek = Carbon::now()->startOfWeek();
        $endOfCurrentWeek = Carbon::now()->endOfWeek();
        $startOfPrevWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfPrevWeek = Carbon::now()->subWeek()->endOfWeek();

        $currentWeekProfit = Transaction::whereBetween('transaction_date', [$startOfCurrentWeek, $endOfCurrentWeek])->where('type', 'income')->sum('amount') - Transaction::whereBetween('transaction_date', [$startOfCurrentWeek, $endOfCurrentWeek])->where('type', 'expense')->sum('amount');
        $prevWeekProfit = Transaction::whereBetween('transaction_date', [$startOfPrevWeek, $endOfPrevWeek])->where('type', 'income')->sum('amount') - Transaction::whereBetween('transaction_date', [$startOfPrevWeek, $endOfPrevWeek])->where('type', 'expense')->sum('amount');

        $currentWeekDividend = ($currentWeekProfit > 0) ? ($currentWeekProfit * ($investorSharePercentage / 100)) : 0;
        $prevWeekDividend = ($prevWeekProfit > 0) ? ($prevWeekProfit * ($investorSharePercentage / 100)) : 0;

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
            'totalIncome', 'totalExpense', 'netProfit', 'estimatedDividend', 'recentTransactions', 'user', 'investorSharePercentage', 'weeklyDividendChange'
        ));
    }
}
