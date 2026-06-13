<?php

use App\Models\User;
use App\Models\Transaction;

test('unauthenticated users are redirected to login when visiting reports page', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
});

test('investor users can access reports index but cannot see sensitive details', function () {
    $user = User::factory()->create(['role' => 'investor']);
    
    // Seed an income and an expense
    Transaction::create([
        'type' => 'income',
        'amount' => 100000,
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Clean Shoes Pemasukan'
    ]);
    Transaction::create([
        'type' => 'expense',
        'category' => 'hpp',
        'amount' => 40000,
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Sabun Pengeluaran'
    ]);

    $response = $this->actingAs($user)->get(route('reports.index', ['range_type' => 'monthly']));

    $response->assertStatus(200)
        ->assertSee('Clean Shoes Pemasukan')
        ->assertDontSee('Sabun Pengeluaran') // Expense hidden for investor
        ->assertDontSee('Laba Bersih Setelah Semua Pengeluaran') // Net profit hidden
        ->assertDontSee('HPP Belanja:') // HPP subtext hidden
        ->assertSee('Laba Kotor')
        ->assertSee('Dividen Investor');
});

test('superadmin users can access reports index and see everything', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    Transaction::create([
        'type' => 'income',
        'amount' => 100000,
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Income Test'
    ]);
    Transaction::create([
        'type' => 'expense',
        'category' => 'hpp',
        'amount' => 40000,
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Expense Test'
    ]);

    $response = $this->actingAs($user)->get(route('reports.index', ['range_type' => 'monthly']));

    $response->assertStatus(200)
        ->assertSee('Income Test')
        ->assertSee('Expense Test')
        ->assertSee('Ekspor Laporan')
        ->assertSee('Laba Kotor')
        ->assertSee('Laba Bersih Setelah Semua Pengeluaran');
});

test('superadmin users can access reports print page', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->get(route('reports.print', [
        'start_date' => now()->startOfMonth()->format('Y-m-d'),
        'end_date' => now()->endOfMonth()->format('Y-m-d')
    ]));

    $response->assertStatus(200)
        ->assertSee('Laporan Keuangan')
        ->assertSee('Alaskakii');
});
