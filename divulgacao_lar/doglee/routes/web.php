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

Route::get('/p', function ($var = "vazio") {
    return redirect('/param');
});


Route::get('/param/{var?}', function ($var = "vazio") {
    return "O valor da var é {$var}";
});


Route::get('/login', function () {
    return 'login';
})->name('login');


Route::get('/', function () {
    return view('admin');
});

Route::get('/admin', function () {
    return view('admin');
})->middleware('auth');



Route::view('/admin2', 'welcome')->name('id-welcome');


Route::get('/redireciona', function () {
    return redirect()->route('id-welcome');
});

