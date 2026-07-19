<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateBranchStockRequest;
use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('branches')->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('products.index', compact('products', 'branches'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('products.index')->with('status', 'Product created successfully.');
    }

    public function updateStock(UpdateBranchStockRequest $request): RedirectResponse
    {
        BranchProduct::updateOrCreate(
            [
                'branch_id' => $request->branch_id,
                'product_id' => $request->product_id,
            ],
            ['stock_quantity' => $request->stock_quantity]
        );

        return redirect()->route('products.index')->with('status', 'Branch stock updated successfully.');
    }
}
