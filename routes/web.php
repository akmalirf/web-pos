<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('/categories', App\Http\Controllers\CategoryController::class);
Route::get('/api/categories', [App\Http\Controllers\CategoryController::class, 'api']);

Route::resource('/suppliers', App\Http\Controllers\SupplierController::class);
Route::get('/api/suppliers', [App\Http\Controllers\SupplierController::class, 'api']);

Route::resource('/products', App\Http\Controllers\ProductController::class);
Route::get('/api/products', [App\Http\Controllers\ProductController::class, 'api']);
