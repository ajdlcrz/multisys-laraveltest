<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\User;
use Database\Seeders\ProductsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    public $user;
    public $token;
    protected function setUp(): void
    {
        parent::setUp();

        $data = User::create([
            'name' => 'Backend',
            'email' => 'backend@multisyscorp.com',
            'password' => Hash::make('test123'),
        ]);

        $credentials = [
           'email' => 'backend@multisyscorp.com',
           'password' => 'test123'
        ];

        $this->token = JWTAuth::attempt($credentials);

        $this->user = User::find($data->id);

        $this->seed(ProductsSeeder::class);
    }

    /** @test */
    public function successfully_created_an_order_and_unavailable_stock()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/order', [
            'product_id' => 1,
            'quantity' => 2,
        ],[
            'Authorization' => 'Bearer ' . $this->token, // Add the token to the header
        ]);

        $response->assertStatus(201);
        $response->assertJson(["message" => "You have successfully ordered this product."]);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'available_stock' => 8,
        ]);

        $response = $this->actingAs($this->user, 'api')->postJson('/api/order', [
            'product_id' => 2,
            'quantity' => 9999,
        ],[
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(400);
        $response->assertJson(["message" => "Failed to order this product due to unavailability of the stock"]);
    }
}
