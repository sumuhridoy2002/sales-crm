<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $branch = Branch::create(['name' => 'Test Branch', 'location' => 'Dhaka']);
        $product = Product::create(['sku' => 'TEST-001', 'name' => 'Test Product', 'price' => 100]);
        $customer = Customer::create(['name' => 'Test Customer', 'email' => 'test@example.com']);

        BranchProduct::create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock_quantity' => 1,
        ]);

        $service = app(SalesService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock for product TEST-001.');

        $service->executeSale($branch->id, $customer->id, [
            ['product_id' => $product->id, 'quantity' => 2],
        ]);
    }

    public function test_inactive_assigned_customer_sale_increments_employee_kpi(): void
    {
        Mail::fake();

        $employee = User::factory()->create([
            'role' => 'employee',
            'kpi_score' => 0,
        ]);

        $branch = Branch::create(['name' => 'Test Branch', 'location' => 'Dhaka']);
        $product = Product::create(['sku' => 'TEST-002', 'name' => 'Recovery Product', 'price' => 500]);
        $customer = Customer::create([
            'name' => 'Lost Customer',
            'email' => 'lost@example.com',
            'last_purchase_at' => now()->subDays(120),
            'purchase_count' => 1,
            'assigned_employee_id' => $employee->id,
        ]);

        BranchProduct::create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock_quantity' => 5,
        ]);

        app(SalesService::class)->executeSale($branch->id, $customer->id, [
            ['product_id' => $product->id, 'quantity' => 1],
        ]);

        $employee->refresh();
        $customer->refresh();

        $this->assertSame(10, $employee->kpi_score);
        $this->assertNull($customer->assigned_employee_id);
        $this->assertSame(2, $customer->purchase_count);
        $this->assertTrue($customer->last_purchase_at->isToday());
    }
}
