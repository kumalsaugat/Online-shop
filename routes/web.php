<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/admin/login',[App\Http\Controllers\AdminLoginController::class, 'index'])->name('admin.login');

Route::group(['prefix' => 'admin'], function() {
    Route::group(['middleware' => 'admin.guest'], function() {
        Route::get('/login',[App\Http\Controllers\AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate',[App\Http\Controllers\AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

    });

    Route::group(['middleware' => 'admin.auth'], function() {
        Route::get('/dashboard',[App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout',[App\Http\Controllers\HomeController::class, 'logout'])->name('admin.logout');

        //Category
        Route::get('/categories/create',[App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories',[App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories',[App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');

        //temp-images.create
        Route::post('/upload-temp-image',[App\Http\Controllers\TempImagesController::class, 'create'])->name('temp-images.create');
    });
});
