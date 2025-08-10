<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectionController;

Route::get('/detections', [DetectionController::class, 'index']);
