<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengunjungController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'umum'])->name('dashboard');
Route::get('/pengunjung', [PengunjungController::class, 'umum'])->name('pengunjung');
