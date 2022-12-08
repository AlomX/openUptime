<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonitorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Monitor;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'monitors' => Monitor::all('id')
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/dashboard', [MonitorController::class, 'store'])->name('monitor.store');
    Route::patch('/dashboard', [MonitorController::class, 'update'])->name('monitor.update');
    Route::delete('/dashboard', [MonitorController::class, 'destroy'])->name('monitor.destroy');
});

require __DIR__.'/auth.php';