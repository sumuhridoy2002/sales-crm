<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\BranchProduct;
use App\Events\SaleCompleted;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesService
{
    public function executeSale(int $branchId, int $customerId, array $items): Sale
    {
        return DB::transaction(function () use ($branchId, $customerId, $items) {
            $totalAmount = 0;
            $saleItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                $inventory = BranchProduct::where('branch_id', $branchId)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || $inventory->stock_quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for product {$product->sku}.");
                }

                $inventory->decrement('stock_quantity', $item['quantity']);

                $unitPrice = $product->price;
                $totalAmount += $unitPrice * $item['quantity'];

                $saleItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ];
            }

            $sale = Sale::create([
                'branch_id' => $branchId,
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
            ]);

            $sale->items()->createMany($saleItems);

            $sale->load(['items.product', 'customer', 'branch']);

            event(new SaleCompleted($sale));

            return $sale;
        });
    }
}
