<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Investor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Investor::create([
            'name' => 'Investor A',
            'share_percentage' => 30.00
        ]);

        Transaction::create(['type' => 'income', 'amount' => 100000000, 'transaction_date' => Carbon::now()->subDays(5), 'description' => 'Penjualan Produk A']);
        Transaction::create(['type' => 'expense', 'amount' => 15000000, 'transaction_date' => Carbon::now()->subDays(4), 'description' => 'Gaji Karyawan']);
        Transaction::create(['type' => 'expense', 'amount' => 5000000, 'transaction_date' => Carbon::now()->subDays(3), 'description' => 'Biaya Iklan']);
        Transaction::create(['type' => 'expense', 'amount' => 40000000, 'transaction_date' => Carbon::now()->subDays(1), 'description' => 'Pembelian Material']);
    }
}
