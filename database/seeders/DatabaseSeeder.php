<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $employee = User::create([
            'name' => 'Rahim Employee',
            'email' => 'employee@system.com',
            'password' => Hash::make('password'),
            'role' => 'employee'
        ]);

        $dhakaBranch = Branch::create(['name' => 'Dhaka Branch', 'location' => 'Mirpur']);

        $product = Product::create(['sku' => 'PROD-100', 'name' => 'Gaming Mouse', 'price' => 1500.00]);
        $dhakaBranch->products()->attach($product->id, ['stock_quantity' => 10]);

        // একজন একটিভ এবং একজন অলরেডি ইনএকটিভ (৯৫ দিন আগের পারচেজ) কাস্টমার তৈরি
        Customer::create(['name' => 'Active Riad', 'email' => 'riad@gmail.com', 'last_purchase_at' => now()->subDays(2)]);
        Customer::create([
            'name' => 'Lost Sakib',
            'email' => 'sakib@gmail.com',
            'last_purchase_at' => now()->subDays(95),
            'assigned_employee_id' => $employee->id
        ]);
    }
}