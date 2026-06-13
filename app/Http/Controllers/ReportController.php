<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Investor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_if(!in_array($user->role, ['superadmin', 'investor']), 403, 'Akses Ditolak: Anda tidak memiliki wewenang.');

        $rangeType = $request->get('range_type', 'monthly');
        $startDate = null;
        $endDate = null;

        if ($rangeType === 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($rangeType === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($rangeType === 'yearly') {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        } elseif ($rangeType === 'custom') {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        }

        $allTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get();

        // Calculate summary statistics
        $totalIncome = $allTransactions->where('type', 'income')->sum('amount');
        $totalExpense = $allTransactions->where('type', 'expense')->sum('amount');
        $totalHpp = $allTransactions->where('type', 'expense')->where('category', 'hpp')->sum('amount');
        $totalOperational = $totalExpense - $totalHpp;

        $grossProfit = $totalIncome - $totalHpp;
        $netProfit = $totalIncome - $totalExpense;

        // Fetch investor share percentage
        if ($user->role === 'investor') {
            $investor = Investor::where('user_id', $user->id)->first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 0;
        } else {
            $investor = Investor::first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 30.00;
        }
        
        $estimatedDividend = ($grossProfit > 0) ? ($grossProfit * ($investorSharePercentage / 100)) : 0;

        // Filter transactions for table display: investor can ONLY see income transactions
        if ($user->role === 'investor') {
            $transactions = $allTransactions->where('type', 'income');
        } else {
            $transactions = $allTransactions;
        }

        return view('reports.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'totalHpp', 'totalOperational',
            'grossProfit', 'netProfit', 'estimatedDividend', 'investorSharePercentage',
            'rangeType', 'startDate', 'endDate', 'user'
        ));
    }

    public function print(Request $request)
    {
        $user = Auth::user();
        abort_if(!in_array($user->role, ['superadmin', 'investor']), 403);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Simple validation
        if (!$startDate || !$endDate) {
            return redirect()->route('reports.index')->with('error', 'Rentang tanggal tidak valid untuk cetak PDF.');
        }

        $allTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalIncome = $allTransactions->where('type', 'income')->sum('amount');
        $totalExpense = $allTransactions->where('type', 'expense')->sum('amount');
        $totalHpp = $allTransactions->where('type', 'expense')->where('category', 'hpp')->sum('amount');
        $totalOperational = $totalExpense - $totalHpp;

        $grossProfit = $totalIncome - $totalHpp;
        $netProfit = $totalIncome - $totalExpense;

        // Fetch investor share percentage
        if ($user->role === 'investor') {
            $investor = Investor::where('user_id', $user->id)->first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 0;
        } else {
            $investor = Investor::first();
            $investorSharePercentage = $investor ? $investor->share_percentage : 30.00;
        }
        
        $estimatedDividend = ($grossProfit > 0) ? ($grossProfit * ($investorSharePercentage / 100)) : 0;

        // Filter transactions for table display: investor can ONLY see income transactions
        if ($user->role === 'investor') {
            $transactions = $allTransactions->where('type', 'income');
        } else {
            $transactions = $allTransactions;
        }

        return view('reports.print', compact(
            'transactions', 'totalIncome', 'totalExpense', 'totalHpp', 'totalOperational',
            'grossProfit', 'netProfit', 'estimatedDividend', 'investorSharePercentage',
            'startDate', 'endDate', 'user'
        ));
    }
}
