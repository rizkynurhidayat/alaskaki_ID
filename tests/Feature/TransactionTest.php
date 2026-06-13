<?php

use App\Models\User;
use App\Models\Transaction;

test('unauthenticated users are redirected to login when visiting create page', function () {
    $response = $this->get(route('transactions.create'));

    $response->assertRedirect(route('login'));
});

test('unauthenticated users are redirected to login when storing a transaction', function () {
    $response = $this->post(route('transactions.store'), [
        'type' => 'income',
        'amount' => 1000,
        'transaction_date' => '2026-06-13',
        'description' => 'Test Transaction',
    ]);

    $response->assertRedirect(route('login'));
});

test('investor users cannot access create page', function () {
    $user = User::factory()->create(['role' => 'investor']);

    $response = $this->actingAs($user)->get(route('transactions.create'));

    $response->assertStatus(403);
});

test('investor users cannot store a transaction', function () {
    $user = User::factory()->create(['role' => 'investor']);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'type' => 'income',
        'amount' => 1000,
        'transaction_date' => '2026-06-13',
        'description' => 'Test Transaction',
    ]);

    $response->assertStatus(403);
});

test('admin users can access create page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('transactions.create'));

    $response->assertStatus(200)
        ->assertSee('Tambah Transaksi Baru');
});

test('admin users can store a transaction', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'type' => 'income',
        'amount' => 15000000,
        'transaction_date' => '2026-06-13',
        'description' => 'Test Project Income',
    ]);

    $response->assertRedirect(route('transactions.index'));

    $this->assertDatabaseHas('transactions', [
        'type' => 'income',
        'amount' => 15000000.00,
        'transaction_date' => '2026-06-13',
        'description' => 'Test Project Income',
    ]);
});

test('transaction storing validates inputs', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'type' => 'invalid_type',
        'amount' => -100,
        'transaction_date' => 'not-a-date',
        'description' => '',
    ]);

    $response->assertSessionHasErrors(['type', 'amount', 'transaction_date', 'description']);
});

test('investor users cannot access edit transaction page', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->get(route('transactions.edit', $transaction));

    $response->assertStatus(403);
});

test('investor users cannot update a transaction', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'type' => 'income',
        'amount' => 60000,
        'transaction_date' => '2026-06-13',
        'description' => 'Updated Dummy',
    ]);

    $response->assertStatus(403);
});

test('investor users cannot delete a transaction', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

    $response->assertStatus(403);
});

test('admin users can access edit transaction page', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->get(route('transactions.edit', $transaction));

    $response->assertStatus(200)
        ->assertSee('Ubah Transaksi');
});

test('admin users can update a transaction', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'type' => 'expense',
        'category' => 'operational',
        'amount' => 35000,
        'transaction_date' => '2026-06-14',
        'description' => 'Updated Dummy Text',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'type' => 'expense',
        'amount' => 35000,
        'transaction_date' => '2026-06-14',
        'description' => 'Updated Dummy Text',
    ]);
});

test('admin users can delete a transaction', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $transaction = Transaction::create([
        'type' => 'income',
        'amount' => 50000,
        'transaction_date' => '2026-06-13',
        'description' => 'Dummy',
    ]);

    $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);
});
