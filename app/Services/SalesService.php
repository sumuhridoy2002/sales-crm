<?php

namespace App\Services;

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
                # Pessimistic Lock (To avoid Race condition)
                $inventory = BranchProduct::where('branch_id', $branchId)
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->stock_quantity < $item['quantity']) {
                    throw new Exception("Product ID {$item['product_id']} এর পর্যাপ্ত স্টক নেই।");
                }

                // স্টক কমানো
                $inventory->decrement('stock_quantity', $item['quantity']);

                $subtotal = $item['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                $saleItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                ];
            }

            // সেলস এন্ট্রি
            $sale = Sale::create([
                'branch_id'    => $branchId,
                'customer_id'  => $customerId,
                'total_amount' => $totalAmount,
            ]);

            $sale->items()->createMany($saleItems);

            // ইভেন্ট ফায়ার (অ্যাসিনক্রোনাস কাজের জন্য)
            event(new SaleCompleted($sale));

            return $sale;
        });
    }
}