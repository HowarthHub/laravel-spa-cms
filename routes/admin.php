<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return \Inertia\Inertia::render('Admin/Dashboard/DashboardIndexPage');
    })->name('dashboard');
});
