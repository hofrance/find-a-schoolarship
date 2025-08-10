<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectionsPageController;

Route::get('/', [DetectionsPageController::class, 'index'])->name('detections.index');
Route::get('/detections/{detection}', [DetectionsPageController::class, 'show'])->name('detections.show');
