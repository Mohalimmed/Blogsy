<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogsController;
use App\Http\Controllers\Api\BlogsyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\BlogController;
use App\Models\Blog;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    // Route::get('/user', 'user')->middleware('auth:sanctum');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::prefix('/blogs')->controller(BlogsController::class)->group(function () {
    Route::get('/', 'index');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', 'create');
        Route::post('/update/{blog_id}', 'update');
        Route::delete('/delete/{blog_id}', 'destroy');
        Route::get('/myblogs', 'myblogs');
        // Route::put('/{blog}', 'update');
        // Route::delete('/{blog}', 'destroy');
    });
});

Route::get('/category', CategoryController::class);
Route::post('/contact', ContactController::class);
