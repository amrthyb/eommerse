<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'A powerful laptop for work and play.',
            'price' => 10000000.00,
            'stock' => 50,
            'category_id' => 1,  // Assuming Electronics category
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'T-shirt',
            'slug' => 't-shirt',
            'description' => 'Comfortable cotton t-shirt.',
            'price' => 150000.00,
            'stock' => 100,
            'category_id' => 2,  // Assuming Clothing category
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

