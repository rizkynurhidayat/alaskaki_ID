<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin Keuangan',
            'email' => 'admin@alaskaki.id',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create Investor User
        $investorUser = User::create([
            'name' => 'Bapak Investor',
            'email' => 'investor@alaskaki.id',
            'password' => Hash::make('password'),
            'role' => 'investor'
        ]);

        Investor::create([
            'user_id' => $investorUser->id,
            'name' => 'Investor A',
            'share_percentage' => 30.00
        ]);

        Transaction::create(['type' => 'income', 'amount' => 100000000, 'transaction_date' => Carbon::now()->subDays(5), 'description' => 'Penjualan Produk A']);
        Transaction::create(['type' => 'expense', 'amount' => 15000000, 'transaction_date' => Carbon::now()->subDays(4), 'description' => 'Gaji Karyawan']);
        Transaction::create(['type' => 'expense', 'amount' => 5000000, 'transaction_date' => Carbon::now()->subDays(3), 'description' => 'Biaya Iklan']);
        Transaction::create(['type' => 'expense', 'amount' => 40000000, 'transaction_date' => Carbon::now()->subDays(1), 'description' => 'Pembelian Material']);
    }
}
