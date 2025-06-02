<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Electronics',
            'description' => 'Gadgets, devices, and tech products.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Clothing',
            'description' => 'Apparel and fashion items.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

