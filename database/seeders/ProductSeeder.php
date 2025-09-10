<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation',
                'price' => 1499000,
                'category' => 'Electronics',
                'image' => '/assets/img/products/headphones.jpg',
                'rating' => 4.5,
                'stock_quantity' => 50,
                'in_stock' => true,
                'is_active' => true
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Feature-rich smartwatch with health monitoring',
                'price' => 2999000,
                'category' => 'Electronics',
                'image' => '/assets/img/products/smartwatch.jpg',
                'rating' => 4.8,
                'stock_quantity' => 30,
                'in_stock' => true,
                'is_active' => true
            ],
            [
                'name' => 'Laptop Backpack',
                'description' => 'Durable laptop backpack with multiple compartments',
                'price' => 749000,
                'category' => 'Accessories',
                'image' => '/assets/img/products/backpack.jpg',
                'rating' => 4.3,
                'stock_quantity' => 75,
                'in_stock' => true,
                'is_active' => true
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Portable Bluetooth speaker with premium sound quality',
                'price' => 1199000,
                'category' => 'Electronics',
                'image' => '/assets/img/products/speaker.jpg',
                'rating' => 4.6,
                'stock_quantity' => 0,
                'in_stock' => false,
                'is_active' => true
            ],
            [
                'name' => 'Gaming Mouse',
                'description' => 'High-precision gaming mouse with RGB lighting',
                'price' => 899000,
                'category' => 'Gaming',
                'image' => '/assets/img/products/mouse.jpg',
                'rating' => 4.7,
                'stock_quantity' => 40,
                'in_stock' => true,
                'is_active' => true
            ],
            [
                'name' => 'USB-C Hub',
                'description' => 'Multi-port USB-C hub with HDMI and USB 3.0',
                'price' => 599000,
                'category' => 'Accessories',
                'image' => '/assets/img/products/hub.jpg',
                'rating' => 4.4,
                'stock_quantity' => 60,
                'in_stock' => true,
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
