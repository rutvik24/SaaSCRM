<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientFormController;
use App\Http\Controllers\FormDataController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\RazorPaySubscriptionController;
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

//Route::middleware('check-subdomain')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/check-availability', [HomeController::class, 'checkAvailability'])->name('check-availability');

    Route::get('/check-domain/{planType}', [HomeController::class, 'checkDomain'])->name('check-domain');
    Route::get('/checkout/{userId}/{planType}', [RazorPaySubscriptionController::class, 'index'])->name('checkout');
    Route::post('/checkout/{userId}/{planType}/{planId}', [RazorPaySubscriptionController::class, 'store'])->name('checkout.store');

    Route::get('/signup/{subdomain}/{planType}', [AuthController::class, 'create'])->name('auth.create');
    Route::post('/signup', [AuthController::class, 'store'])->name('auth.store');

    Route::get('razorpay-payment', [RazorpayPaymentController::class, 'index']);
    Route::post('razorpay-payment', [RazorpayPaymentController::class, 'store'])->name('razorpay.payment.store');
//});

Route::get('/expired', [HomeController::class, 'expired'])->name('expired');

Route::post('/callback/razorpay', [RazorPaySubscriptionController::class, 'callback'])->name('callback.razorpay');

Route::middleware('check-subscription')->group(function () {
    Route::get('/new/app', [ClientFormController::class, 'new']);
    Route::post('/new/app', [ClientFormController::class, 'store']);
    Route::get('/view', [ClientFormController::class, 'index'])->name('client-view');
});

Route::get('callback/razorpay', [RazorPaySubscriptionController::class, 'callback'])->name('razorpay.callback');

