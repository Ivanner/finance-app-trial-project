<?php

use Illuminate\Support\Facades\Route;

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
use App\Models\User;

Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

Route::get(
    '/dashboard',
    function () {
        return view('dashboard');
    }
)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])
    ->prefix('transactions')  // Matches The "/transaction/*" URL
    ->group(
        function () {
            Route::get('/', '\App\Http\Controllers\TransactionsController@list');
            Route::delete('/{transaction}', 'App\Http\Controllers\TransactionsController@delete');
            Route::post('/', 'App\Http\Controllers\TransactionsController@create');
            Route::post('/import', 'App\Http\Controllers\TransactionsController@import');
            Route::put('/{transaction}', 'App\Http\Controllers\TransactionsController@update');
        }
    );

require __DIR__.'/auth.php';
