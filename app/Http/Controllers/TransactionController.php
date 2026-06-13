<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // 0. Menampilkan riwayat transaksi
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak: Hanya Admin yang dapat melihat riwayat transaksi.');
        }

        $transactions = Transaction::orderBy('transaction_date', 'desc')->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    // 1. Menampilkan form input
    public function create()
    {
        // Proteksi tingkat controller: hanya admin yang diizinkan
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak: Hanya Admin yang dapat menambahkan transaksi.');
        }

        $products = Product::orderBy('name', 'asc')->get();
        return view('transactions.create', compact('products'));
    }

    // 2. Menyimpan data form ke database
    public function store(Request $request)
    {
        // Proteksi
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Validasi input
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id',
        ]);

        // Simpan data melalui Eloquent Model
        Transaction::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'product_id' => $request->type === 'income' ? $request->product_id : null,
        ]);

        // Arahkan kembali ke menu transaksi
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    // 3. Menampilkan form edit transaksi
    public function edit(Transaction $transaction)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak: Hanya Admin yang dapat mengedit transaksi.');
        }

        $products = Product::orderBy('name', 'asc')->get();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    // 4. Memperbarui data transaksi di database
    public function update(Request $request, Transaction $transaction)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $transaction->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'product_id' => $request->type === 'income' ? $request->product_id : null,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    // 5. Menghapus transaksi dari database
    public function destroy(Transaction $transaction)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
