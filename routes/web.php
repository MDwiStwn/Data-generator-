<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestDataController;
use App\Livewire\TestDataGenerator;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Main generator page (Livewire)
Route::get('/generator', TestDataGenerator::class)
    ->middleware(['auth'])
    ->name('generator');

// Dashboard redirects to generator
Route::get('/dashboard', function () {
    return redirect()->route('generator');
})->middleware(['auth', 'verified'])->name('dashboard');

// Export endpoints with auth + rate limiting
Route::middleware(['auth', 'throttle:export'])->group(function () {
    Route::post('/export/csv', [TestDataController::class, 'exportCsv'])->name('export.csv');
    Route::post('/export/excel', [TestDataController::class, 'exportExcel'])->name('export.excel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
