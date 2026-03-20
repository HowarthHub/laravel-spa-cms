<?php

use App\Http\Controllers\Public\PublicFormController;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Public\PublicPostController;
use App\Http\Controllers\Public\PublicSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');

Route::get('/search', PublicSearchController::class)->name('public.search');

Route::get('/blog', [PublicPostController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [PublicPostController::class, 'show'])->name('public.blog.show');

Route::post('/forms/{form}/submit', [PublicFormController::class, 'submit'])->name('public.form.submit');

Route::get('/{slug}', [PublicPageController::class, 'show'])->name('public.page.show');
Route::get('/{parent}/{child}', [PublicPageController::class, 'showChild'])->name('public.page.show.child');
