<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Products::create([
            "name" => "Product 1",
            "available_stock" => 10
        ]);
        Products::create([
            "name" => "Product 2",
            "available_stock" => 10
        ]);
        Products::create([
            "name" => "Product 3",
            "available_stock" => 10
        ]);
    }
}
