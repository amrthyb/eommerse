<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImagesTableSeeder extends Seeder
{
    public function run()
    {
        ProductImage::create([
            'product_id' => 1,  // Assuming Laptop product
            'image_url' => 'https://example.com/laptop.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ProductImage::create([
            'product_id' => 2,  // Assuming T-shirt product
            'image_url' => 'https://example.com/t-shirt.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
