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

        // Seed Products / Services
        \App\Models\Product::create(['name' => 'Deep Clean', 'price' => 50000]);
        \App\Models\Product::create(['name' => 'Unyellowing', 'price' => 75000]);
        \App\Models\Product::create(['name' => 'Repaint', 'price' => 120000]);

        Transaction::create(['type' => 'income', 'amount' => 100000000, 'transaction_date' => Carbon::now()->subDays(5), 'description' => 'Penjualan Jasa Cuci Sepatu']);
        Transaction::create(['type' => 'expense', 'amount' => 15000000, 'transaction_date' => Carbon::now()->subDays(4), 'description' => 'Gaji Karyawan', 'category' => 'operational']);
        Transaction::create(['type' => 'expense', 'amount' => 5000000, 'transaction_date' => Carbon::now()->subDays(3), 'description' => 'Biaya Iklan', 'category' => 'operational']);
        Transaction::create(['type' => 'expense', 'amount' => 40000000, 'transaction_date' => Carbon::now()->subDays(1), 'description' => 'Pembelian Material', 'category' => 'hpp']);
    }
}
