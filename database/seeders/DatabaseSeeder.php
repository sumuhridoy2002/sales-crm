<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $employeeOne = User::create([
            'name' => 'Rahim Ahmed',
            'email' => 'rahim@system.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        $employeeTwo = User::create([
            'name' => 'Sadia Khan',
            'email' => 'sadia@system.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        $dhaka = Branch::create(['name' => 'Dhaka Branch', 'location' => 'Mirpur, Dhaka']);
        $chittagong = Branch::create(['name' => 'Chittagong Branch', 'location' => 'Agrabad, Chittagong']);
        $sylhet = Branch::create(['name' => 'Sylhet Branch', 'location' => 'Zindabazar, Sylhet']);

        $products = collect([
            ['sku' => 'PROD-100', 'name' => 'Gaming Mouse', 'price' => 1500.00],
            ['sku' => 'PROD-101', 'name' => 'Mechanical Keyboard', 'price' => 4200.00],
            ['sku' => 'PROD-102', 'name' => 'USB-C Hub', 'price' => 2800.00],
            ['sku' => 'PROD-103', 'name' => '27-inch Monitor', 'price' => 18500.00],
            ['sku' => 'PROD-104', 'name' => 'Wireless Headset', 'price' => 6500.00],
            ['sku' => 'PROD-105', 'name' => 'Laptop Stand', 'price' => 2200.00],
        ])->map(fn (array $data) => Product::create($data));

        $branchStock = [
            $dhaka->id => [10, 8, 15, 4, 12, 20],
            $chittagong->id => [6, 5, 9, 2, 7, 11],
            $sylhet->id => [4, 3, 6, 1, 5, 8],
        ];

        foreach ($branchStock as $branchId => $quantities) {
            foreach ($products as $index => $product) {
                BranchProduct::create([
                    'branch_id' => $branchId,
                    'product_id' => $product->id,
                    'stock_quantity' => $quantities[$index],
                ]);
            }
        }

        $activeCustomer = Customer::create([
            'name' => 'Active Riad',
            'email' => 'riad@gmail.com',
            'last_purchase_at' => now()->subDays(2),
            'purchase_count' => 3,
        ]);

        $lostCustomer = Customer::create([
            'name' => 'Lost Sakib',
            'email' => 'sakib@gmail.com',
            'last_purchase_at' => now()->subDays(95),
            'purchase_count' => 1,
            'assigned_employee_id' => $employeeOne->id,
        ]);

        $recentCustomer = Customer::create([
            'name' => 'Nadia Rahman',
            'email' => 'nadia@gmail.com',
            'last_purchase_at' => now()->subDays(14),
            'purchase_count' => 2,
        ]);

        $neverPurchased = Customer::create([
            'name' => 'New Customer Tanvir',
            'email' => 'tanvir@gmail.com',
            'last_purchase_at' => null,
            'purchase_count' => 0,
            'assigned_employee_id' => $employeeTwo->id,
        ]);

        $this->seedHistoricalSale($dhaka, $activeCustomer, $products[0], 2, 3000.00, now()->subDays(30));
        $this->seedHistoricalSale($dhaka, $activeCustomer, $products[1], 1, 4200.00, now()->subDays(10));
        $this->seedHistoricalSale($chittagong, $recentCustomer, $products[4], 1, 6500.00, now()->subDays(20));
        $this->seedHistoricalSale($sylhet, $lostCustomer, $products[2], 1, 2800.00, now()->subDays(95));

        $admin->createToken('ecommerce-api');

        $this->command?->info('Seeded demo users (password: password):');
        $this->command?->info('- admin@system.com (admin)');
        $this->command?->info('- rahim@system.com (employee)');
        $this->command?->info('- sadia@system.com (employee)');
        $this->command?->info('Create an API token with: php artisan tinker -> User::first()->createToken("demo")->plainTextToken');
    }

    private function seedHistoricalSale(
        Branch $branch,
        Customer $customer,
        Product $product,
        int $quantity,
        float $total,
        $createdAt
    ): void {
        $sale = Sale::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'total_amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $sale->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }
}
