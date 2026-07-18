<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Jobs\SendReEngagementEmail;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function inactiveCustomers()
    {
        return response()->json(Customer::inactive(90)->with('assignedEmployee')->get());
    }

    public function assignCustomer(Request $request, Customer $customer)
    {
        $request->validate(['employee_id' => 'required|exists:users,id']);
        $customer->update(['assigned_employee_id' => $request->employee_id]);
        return response()->json(['message' => 'কাস্টমার সফলভাবে এসাইন করা হয়েছে।']);
    }

    public function reEngage(Customer $customer)
    {
        dispatch(new SendReEngagementEmail($customer));
        return response()->json(['message' => 'রি-এনগেজমেন্ট মেইল কিউতে পাঠানো হয়েছে।']);
    }
}