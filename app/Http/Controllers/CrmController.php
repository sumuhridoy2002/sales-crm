<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignCustomerRequest;
use App\Jobs\SendReEngagementEmail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function dashboard(): View
    {
        $inactiveDays = config('crm.inactive_days', 90);
        $inactiveCustomers = Customer::inactive($inactiveDays)
            ->with('assignedEmployee')
            ->orderBy('last_purchase_at')
            ->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('dashboard', compact('inactiveCustomers', 'employees', 'inactiveDays'));
    }

    public function assignCustomer(AssignCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update(['assigned_employee_id' => $request->employee_id]);

        return redirect()->back()->with('status', 'Employee assigned successfully.');
    }

    public function reEngage(Customer $customer): RedirectResponse
    {
        dispatch(new SendReEngagementEmail($customer));

        return redirect()->back()->with('status', 'Re-engagement email queued.');
    }
}
