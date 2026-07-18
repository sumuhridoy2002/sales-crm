<?php

use App\Http\Controllers\CrmController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ল্যান্ডিং পেজ সরাসরি লগইনে নিয়ে যাবে
Route::get('/', function () {
    return redirect('/login');
});

// অথেনটিকেটেড রাউটস
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ড্যাশবোর্ড রাউট (যেখানে কাস্টমার ও KPI দেখা যাবে)
    Route::get('/dashboard', [CrmController::class, 'dashboard'])->name('dashboard');

    // শুধুমাত্র অ্যাডমিনের জন্য প্রটেক্টেড রাউটস (Role Middleware)
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/crm/customers/{customer}/assign', [CrmController::class, 'assignCustomer']);
        Route::post('/crm/customers/{customer}/re-engage', [CrmController::class, 'reEngage']);
    });

    // Breeze default profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';