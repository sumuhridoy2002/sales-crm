<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::with('assignedEmployee')
            ->withCount('sales')
            ->orderBy('name')
            ->get();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'assignedEmployee',
            'sales' => fn ($query) => $query->with(['branch', 'items.product'])->latest(),
        ]);

        return view('customers.show', compact('customer'));
    }
}
