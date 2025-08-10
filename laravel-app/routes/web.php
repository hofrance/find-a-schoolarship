<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectionsPageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CareerController;

// Page d'accueil - Bourses
Route::get('/', [DetectionsPageController::class, 'index'])->name('detections.index');
Route::get('/detections/{detection}', [DetectionsPageController::class, 'show'])->name('detections.show');

// Routes pour les articles de blog (orientation)
Route::prefix('orientation')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/categorie/{category}', [ArticleController::class, 'category'])->name('category');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
});

// Routes pour les métiers et débouchés
Route::prefix('metiers')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/secteur/{sector}', [CareerController::class, 'sector'])->name('sector');
    Route::get('/{slug}', [CareerController::class, 'show'])->name('show');
});
