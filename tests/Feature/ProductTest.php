<?php

use App\Models\User;
use App\Models\Product;

test('unauthenticated users are redirected to login when visiting products page', function () {
    $this->get(route('products.index'))->assertRedirect(route('login'));
});

test('investor users cannot access products page', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $this->actingAs($user)->get(route('products.index'))->assertStatus(403);
});

test('investor users cannot create products', function () {
    $user = User::factory()->create(['role' => 'investor']);
    $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Deep Clean',
        'price' => 50000,
    ])->assertStatus(403);
});

test('admin users can access products index', function () {
    $user = User::factory()->create(['role' => 'admin']);
    Product::create(['name' => 'Deep Clean', 'price' => 50000]);

    $this->actingAs($user)->get(route('products.index'))
        ->assertStatus(200)
        ->assertSee('Deep Clean')
        ->assertSee('Rp 50.000');
});

test('admin users can create a product', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Unyellowing',
        'price' => 75000,
    ]);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'name' => 'Unyellowing',
        'price' => 75000,
    ]);
});

test('admin users can update a product', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::create(['name' => 'Old Jasa', 'price' => 30000]);

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'New Jasa',
        'price' => 45000,
    ]);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New Jasa',
        'price' => 45000,
    ]);
});

test('admin users can delete a product', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::create(['name' => 'Trash Jasa', 'price' => 10000]);

    $response = $this->actingAs($user)->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
