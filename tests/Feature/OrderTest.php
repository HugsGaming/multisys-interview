<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful order placement.
     *
     * @return void
     */
    public function test_successful_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'available_stock' => 10
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/order', [
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $response->assertStatus(201)
        ->assertJson([
            'message' => 'You have successfully ordered this product.'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'available_stock' => 5
        ]);
    }

    public function test_order_with_insufficient_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'available_stock' => 3
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/order', [
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $response->assertStatus(400)
        ->assertJson([
            'error' => "Failed to order this product due to unavailability of the stock"
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'available_stock' => 3,
        ]);
    }
}
