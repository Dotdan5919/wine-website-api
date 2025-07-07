<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

// CRUD for blogs
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('blogs.show');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy');

            // comments (CRUD)

    Route::get('/comments',[CommentController::class,'index'])->name('comment.index');
    Route::post('/comments',[CommentController::class,'store'])->name('comments.store');
    Route::get('/comments/{id}',[CommentController::class,'show'])->name('comments.show');
    Route::post('/comments/{id}',[CommentController::class,'update'])->name('comments.update');
    Route::delete('/comments/{id}',[CommentController::class,'destroy'])->name('comments.destroy');



    // Reports

    Route::get('/reports',[ReportController::class,'index'])->name('report.index');
    Route::get('/reports/{id}',[ReportController::class,'show'])->name('report.show');
    Route::post('/reports',[ReportController::class,'store'])->name('reports.store');
    Route::post('/reports/{id}',[ReportController::class,'update'])->name('reports.update');
    Route::delete('/reports/{id}',[ReportController::class,'destroy'])->name('reports.destroy');


      // Products

    Route::get('/products',[ProductController::class,'index'])->name('product.index');
    Route::post('/products',[ProductController::class,'store'])->name('products.store');
    Route::get('/products/{id}',[ProductController::class,'show'])->name('product.show');
    Route::post('/products/{id}',[ProductController::class,'update'])->name('products.update');
    Route::delete('/products/{id}',[ProductController::class,'destroy'])->name('products.destroy');



         // comments

    Route::get('/products',[ProductController::class,'index'])->name('product.index');
    Route::post('/products',[ProductController::class,'store'])->name('products.store');
    Route::get('/products/{id}',[ProductController::class,'show'])->name('product.show');
    Route::post('/products/{id}',[ProductController::class,'update'])->name('products.update');
    Route::delete('/products/{id}',[ProductController::class,'destroy'])->name('products.destroy');



    


    
    // Add your other protected routes here
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Welcome to dashboard',
            'user' => $request->user()
        ]);



        // blog routes


          

    });


 Route::apiResource('posts', BlogController::class);
    
    // Alternative route names if you prefer the old naming convention
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
  



});