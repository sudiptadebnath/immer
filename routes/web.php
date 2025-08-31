<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    if (userLogged()) {
        return redirect()->route('user.dashboard');
    }
    return view('index');
});

Route::post('/login', [UserController::class, 'login']);

Route::middleware('check.allowsignup')->group(function () {
    Route::get('/register', fn() => view("register"));
    Route::post('/register', [UserController::class, 'register']);
});


Route::middleware('check.user.session')->prefix('user')->group(function () {

    Route::get("/logout", function () {
        Session::flush();
        return redirect('/');
    });
    Route::get("/dashboard", fn() => view('user.dashboard'))->name('user.dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', fn() => view("user.users"))->name('user.users');
        Route::get('/data', [UserController::class, 'data'])->name('users.data');
        Route::view('/profile','user.profile');
        Route::get('/gpass/{id}', [UserController::class, 'gpass'])->name('user.gpass');
        Route::get('/gpass/pdf/{id}', [UserController::class, 'downloadPdf'])->name('user.gpass.pdf');
        Route::post('/send_otp', [UserController::class, 'send_otp'])->name('user.send_otp');
        Route::post('/verify_otp', [UserController::class, 'verify_otp'])->name('user.verify_otp');
        Route::get('/scan', [UserController::class, 'scan'])->name('user.scan');
        Route::get('/scanstat', [UserController::class, 'scanstat'])->name('user.scanstat');
        Route::post('/attendance', [UserController::class, 'attendance'])->name('user.attendance');
        Route::get('/settings', fn() => view("user.settings"))->name('user.settings');
        Route::post('/settings', [UserController::class, 'save_settings'])->name('user.save_settings');
        Route::get('/{id}', [UserController::class, 'get']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/min/{id}', [UserController::class, 'update2']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });
});
