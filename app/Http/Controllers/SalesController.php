<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use Illuminate\Http\Request;
use Exception;

class SalesController extends Controller
{
    public function __construct(protected SalesService $salesService) {}

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            $sale = $this->salesService->executeSale(
                $request->branch_id,
                $request->customer_id,
                $request->items
            );
            return response()->json(['message' => 'বিক্রয় সফল হয়েছে!', 'sale_id' => $sale->id], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}