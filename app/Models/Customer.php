<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'assigned_employee_id', 'last_purchase_at', 'purchase_count'];
    
    protected $casts = [
        'last_purchase_at' => 'datetime',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    public function scopeInactive($query, ?int $days = null)
    {
        $days ??= config('crm.inactive_days', 90);

        return $query->where(function ($q) use ($days) {
            $q->where('last_purchase_at', '<', Carbon::now()->subDays($days))
              ->orWhereNull('last_purchase_at');
        });
    }
}
