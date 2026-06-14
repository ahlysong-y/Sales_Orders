<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // បង្កើត User គំរូសម្រាប់ Login
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // បង្កើតអតិថិជនគំរូ (Customers)
        Customer::create([
            'name' => 'ក្រុមហ៊ុន អាហារូបត្ថម្ភ ភ្នំពេញ',
            'email' => 'phnompenh@mail.com',
            'credit_limit' => 5000.00,
            'status' => 'active'
        ]);

        Customer::create([
            'name' => 'ហាងលក់ទំនិញទូទៅ សុខា',
            'email' => 'sokha@mail.com',
            'credit_limit' => 2500.00,
            'status' => 'active'
        ]);

        // បង្កើតមុខទំនិញគំរូ (Products)
        Product::create(['name' => 'iPhone 15 Pro Max', 'stock_quantity' => 20]);
        Product::create(['name' => 'iPad Air M2', 'stock_quantity' => 15]);
        Product::create(['name' => 'AirPods Pro 2', 'stock_quantity' => 50]);
    }
}
