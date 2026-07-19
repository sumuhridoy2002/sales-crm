<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SalesService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(protected SalesService $salesService) {}

    public function index(): View
    {
        $sales = Sale::with(['customer', 'branch', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $branches = Branch::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $products = Product::with('branches')->orderBy('name')->get();

        return view('sales.create', compact('branches', 'customers', 'products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->salesService->executeSale(
                $request->integer('branch_id'),
                $request->integer('customer_id'),
                $request->input('items')
            );

            return redirect()
                ->route('sales.index')
                ->with('status', "Sale #{$sale->id} recorded successfully.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['sale' => $e->getMessage()]);
        }
    }
}
