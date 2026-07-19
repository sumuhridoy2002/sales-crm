<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Models\User;
use App\Mail\SaleInvoiceMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class UpdateCustomerMetricsAndKPI
{
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;
        $customer = $sale->customer;
        $inactiveDays = config('crm.inactive_days', 90);

        $isInactive = $customer->last_purchase_at === null
            || $customer->last_purchase_at->lt(Carbon::now()->subDays($inactiveDays));

        if ($customer->assigned_employee_id && $isInactive) {
            User::where('id', $customer->assigned_employee_id)
                ->increment('kpi_score', config('crm.kpi_recovery_points', 10));

            $customer->assigned_employee_id = null;
        }

        $customer->last_purchase_at = Carbon::now();
        $customer->purchase_count = ($customer->purchase_count ?? 0) + 1;
        $customer->save();

        try {
            Mail::to($customer->email)->send(new SaleInvoiceMail($sale));
        } catch (\Exception $e) {
            logger('Invoice mail failed to send: '.$e->getMessage());
        }
    }
}
