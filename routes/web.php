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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/books/add', [App\Http\Controllers\BookController::class, 'view'])->name('book.view');

Route::post('/books/add', [App\Http\Controllers\BookController::class, 'add'])->name('book.add');


// Route::get('/', [App\Http\Controllers\BookController::class, 'index']);

Route::get('/books/{id}', [App\Http\Controllers\BookController::class, 'show'])->name('book.detail');

Route::post('/books/{id}', [App\Http\Controllers\ItemController::class, 'view']);

Route::post('/delete-item',[App\Http\Controllers\ItemController::class, 'deleteItem'])->name('delete.item');

Route::post('/delete/book/{bookId}', [App\Http\Controllers\BookController::class, 'deleteBook'])->name('delete.book');

Route::post('/edit/book/{bookId}', [App\Http\Controllers\BookController::class, 'editBook'])->name('edit.book');
