<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\MonitorController;
use App\Http\Controllers\PingController;

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
        'monitors' => \App\Models\Monitor::all(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('monitors', MonitorController::class);

    Route::get('/monitors/{monitor}/ping', [MonitorController::class, 'ping'])->name('monitors.ping');
    Route::get('/monitors/{monitor}/pings', [MonitorController::class, 'pings'])->name('monitors.pings');
    Route::get('/monitors/{monitor}/latestPings', [MonitorController::class, 'latestPings'])->name('monitors.latestPings');
    Route::get('/monitors/{monitor}/lastChange', [MonitorController::class, 'lastChange'])->name('monitors.lastChange');
    Route::post('/monitors/import', [MonitorController::class, 'import'])->name('monitors.import');
});

require __DIR__.'/auth.php';
