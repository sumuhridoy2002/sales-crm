<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Models\User;
use App\Mail\SaleInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UpdateCustomerMetricsAndKPI
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;
        
        // কাস্টমারের ফ্রেশ অবজেক্ট ডাটাবেজ থেকে নেওয়া
        $customer = $sale->customer;

        // ১. আগে চেক করুন কাস্টমারটি আসলেই ইনঅ্যাক্টিভ বা Lost ছিল কিনা
        $isInactive = $customer->last_purchase_at === null || 
                      $customer->last_purchase_at->lt(Carbon::now()->subDays(90));

        // ২. যদি ইনঅ্যাক্টিভ হয় এবং কোনো এমপ্লয়ী অ্যাসাইন করা থাকে, তবে KPI বাড়িয়ে দিন
        if ($customer->assigned_employee_id && $isInactive) {
            User::where('id', $customer->assigned_employee_id)->increment('kpi_score', 10);
            
            // রিকভারি সফল হওয়ায় অ্যাসাইনমেন্ট রিমুভ করুন
            $customer->assigned_employee_id = null; 
        }

        // ৩. কাস্টমারের লাস্ট পারচেজ টাইম কারেন্ট টাইমে আপডেট করুন
        $customer->last_purchase_at = Carbon::now();
        $customer->save();

        // ৪. বোনাস ফিচার: কাস্টমারকে সাকসেসফুল ইনভয়েস মেইল পাঠান
        try {
            Mail::to($customer->email)->send(new SaleInvoiceMail($sale));
        } catch (\Exception $e) {
            // মেইল ফেইল করলেও যেন মেইন ট্রানজেকশন বা সেলস আটকে না যায়
            logger('Invoice mail failed to send: ' . $e->getMessage());
        }
    }
}