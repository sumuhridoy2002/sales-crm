<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inactive Customer Threshold
    |--------------------------------------------------------------------------
    |
    | Number of days without a purchase before a customer is considered
    | inactive ("lost") for CRM follow-up and KPI recovery tracking.
    |
    */

    'inactive_days' => (int) env('CRM_INACTIVE_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | KPI Points
    |--------------------------------------------------------------------------
    |
    | Points awarded to an assigned employee when a previously inactive
    | customer makes a new purchase.
    |
    */

    'kpi_recovery_points' => (int) env('CRM_KPI_RECOVERY_POINTS', 10),

];
