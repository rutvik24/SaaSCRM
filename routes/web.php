<?php

use App\Http\Controllers\ClientFormController;
use App\Http\Controllers\FormDataController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/new', [FormDataController::class, 'store']);
Route::get('/new', [FormDataController::class, 'create']);

Route::get('/new/app', [ClientFormController::class, 'new']);
Route::post('/new/app', [ClientFormController::class, 'store']);
Route::get('/view', [ClientFormController::class, 'index'])->name('client-view');
