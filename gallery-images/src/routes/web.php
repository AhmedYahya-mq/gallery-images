<?php

use Illuminate\Support\Facades\Route;

/**
 * Photo management routes.
 * 
 * Routes:
 * - index: عرض جميع الصور
 * - show: عرض صورة واحدة
 * - store: رفع صور جديدة
 * - destroy: حذف صورة
 */
Route::resource("photos", \App\Http\Controllers\PhotoController::class)->only([
    "index",
    "store"
])->names([
    "index" => "photos.index",
    "show" => "photos.show",
]);

Route::get("photos/show", [\App\Http\Controllers\PhotoController::class, "show"])->name("photos.show");
Route::delete("photos/", [\App\Http\Controllers\PhotoController::class, "destroy"])->name("photos.destroy");
