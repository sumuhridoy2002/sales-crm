<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Jobs\SendReEngagementEmail;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    // ড্যাশবোর্ডে ডেটা লোড করার মেথড
    public function dashboard()
    {
        $inactiveCustomers = Customer::inactive(90)->with('assignedEmployee')->get();
        $employees = User::where('role', 'employee')->get();

        return view('dashboard', compact('inactiveCustomers', 'employees'));
    }

    public function assignCustomer(Request $request, Customer $customer)
    {
        $request->validate(['employee_id' => 'required|exists:users,id']);
        
        $customer->update(['assigned_employee_id' => $request->employee_id]);

        return redirect()->back()->with('status', 'Employee assigned successfully!');
    }

    public function reEngage(Customer $customer)
    {
        dispatch(new SendReEngagementEmail($customer));

        return redirect()->back()->with('status', 'Re-engagement email queued!');
    }
}