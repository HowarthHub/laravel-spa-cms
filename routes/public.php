<?php

use App\Http\Controllers\Public\PublicFormController;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Public\PublicPostController;
use App\Http\Controllers\Public\PublicServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');

Route::get('/blog', [PublicPostController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [PublicPostController::class, 'show'])->name('public.blog.show');

Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('public.services.show');

Route::post('/forms/{form}/submit', [PublicFormController::class, 'submit'])->name('public.form.submit');

Route::get('/{slug}', [PublicPageController::class, 'show'])->name('public.page.show');
