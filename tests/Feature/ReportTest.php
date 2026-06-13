<?php

use App\Models\User;
use App\Models\Transaction;

test('unauthenticated users are redirected to login when visiting reports page', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
});

test('investor users cannot access reports page', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $this->actingAs($user)->get(route('reports.index'))->assertStatus(403);
});

test('admin users can access reports index', function () {
    $user = User::factory()->create(['role' => 'admin']);
    Transaction::create([
        'type' => 'income',
        'amount' => 100000,
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Income Test'
    ]);

    $response = $this->actingAs($user)->get(route('reports.index', ['range_type' => 'monthly']));

    $response->assertStatus(200)
        ->assertSee('Income Test')
        ->assertSee('Ekspor Laporan')
        ->assertSee('Laba Kotor');
});

test('admin users can access reports print page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('reports.print', [
        'start_date' => now()->startOfMonth()->format('Y-m-d'),
        'end_date' => now()->endOfMonth()->format('Y-m-d')
    ]));

    $response->assertStatus(200)
        ->assertSee('Laporan Keuangan')
        ->assertSee('Alaskakii');
});
