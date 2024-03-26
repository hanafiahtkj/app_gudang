<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\LabaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// })->name('index');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', DashboardController::class)->name('index');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('categories/loadDatatables', [CategoryController::class, 'loadDatatables'])->name('categories.loadDatatables');
    Route::resource('categories', CategoryController::class);

    Route::get('brands/loadDatatables', [BrandController::class, 'loadDatatables'])->name('brands.loadDatatables');
    Route::resource('brands', BrandController::class);

    Route::get('units/loadDatatables', [UnitController::class, 'loadDatatables'])->name('units.loadDatatables');
    Route::resource('units', UnitController::class);

    Route::get('warehouses/loadDatatables', [WarehouseController::class, 'loadDatatables'])->name('warehouses.loadDatatables');
    Route::resource('warehouses', WarehouseController::class);

    Route::get('products/loadDatatables', [ProductController::class, 'loadDatatables'])->name('products.loadDatatables');
    Route::resource('products', ProductController::class);

    Route::get('purchases/loadDatatables', [PurchaseController::class, 'loadDatatables'])->name('purchases.loadDatatables');
    Route::resource('purchases', PurchaseController::class);

    Route::get('sales/loadDatatables', [SaleController::class, 'loadDatatables'])->name('sales.loadDatatables');
    Route::get('sales/modalProducts', [SaleController::class, 'modalProducts'])->name('sales.modalProducts');
    Route::resource('sales', SaleController::class);

    Route::get('nilai/loadDatatables', [NilaiController::class, 'loadDatatables'])->name('nilai.loadDatatables');
    Route::resource('nilai', NilaiController::class);

    Route::get('laba/loadDatatables', [LabaController::class, 'loadDatatables'])->name('laba.loadDatatables');
    Route::get('laba/loadProsesData', [LabaController::class, 'loadProsesData'])->name('laba.loadProsesData');
    Route::resource('laba', LabaController::class);
});



require __DIR__.'/auth.php';
