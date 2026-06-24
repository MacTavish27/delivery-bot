<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Burgerlar',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'name'        => 'Classic Burger',
            'description' => 'Mazali burger',
            'price'       => 25000,
            'is_active'   => true,
            'sort_order'  => 1,
        ]);
    }
}
