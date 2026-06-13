<?php

use App\Models\User;
use App\Models\Investor;

test('unauthenticated users are redirected to login when visiting investors page', function () {
    $this->get(route('investors.index'))->assertRedirect(route('login'));
});

test('investor users cannot access investors management page', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $this->actingAs($user)->get(route('investors.index'))->assertStatus(403);
});

test('superadmin users can access investors index', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $investorUser = User::factory()->create(['role' => 'investor']);
    Investor::create([
        'user_id' => $investorUser->id,
        'name' => 'Mitra A',
        'share_percentage' => 25.00
    ]);

    $this->actingAs($user)->get(route('investors.index'))
        ->assertStatus(200)
        ->assertSee('Mitra A')
        ->assertSee('25,00%');
});

test('superadmin users can create an investor with a new user account', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->post(route('investors.store'), [
        'account_type' => 'new',
        'name' => 'Investor Baru',
        'share_percentage' => 35.00,
        'email' => 'new_investor@alaskaki.id',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('investors.index'));
    
    $this->assertDatabaseHas('users', [
        'email' => 'new_investor@alaskaki.id',
        'role' => 'investor'
    ]);

    $this->assertDatabaseHas('investors', [
        'name' => 'Investor Baru',
        'share_percentage' => 35.00
    ]);
});

test('superadmin users can create an investor linked to an existing unlinked user', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $existingUser = User::factory()->create(['role' => 'investor']);

    $response = $this->actingAs($user)->post(route('investors.store'), [
        'account_type' => 'existing',
        'name' => 'Investor Lama',
        'share_percentage' => 40.00,
        'user_id' => $existingUser->id
    ]);

    $response->assertRedirect(route('investors.index'));

    $this->assertDatabaseHas('investors', [
        'user_id' => $existingUser->id,
        'name' => 'Investor Lama',
        'share_percentage' => 40.00
    ]);
});

test('superadmin users can update an investor', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $investorUser = User::factory()->create(['role' => 'investor', 'name' => 'Old User Name']);
    $investor = Investor::create([
        'user_id' => $investorUser->id,
        'name' => 'Old Name',
        'share_percentage' => 20.00
    ]);

    $response = $this->actingAs($user)->put(route('investors.update', $investor), [
        'name' => 'New Name',
        'share_percentage' => 30.00
    ]);

    $response->assertRedirect(route('investors.index'));

    $this->assertDatabaseHas('investors', [
        'id' => $investor->id,
        'name' => 'New Name',
        'share_percentage' => 30.00
    ]);

    // Check user name updated too
    $this->assertDatabaseHas('users', [
        'id' => $investorUser->id,
        'name' => 'New Name'
    ]);
});

test('superadmin users can delete an investor', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $investorUser = User::factory()->create(['role' => 'investor']);
    $investor = Investor::create([
        'user_id' => $investorUser->id,
        'name' => 'Mitra Dihapus',
        'share_percentage' => 15.00
    ]);

    $response = $this->actingAs($user)->delete(route('investors.destroy', $investor));

    $response->assertRedirect(route('investors.index'));

    $this->assertDatabaseMissing('investors', [
        'id' => $investor->id
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $investorUser->id
    ]);
});
