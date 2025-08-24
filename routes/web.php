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
Route::get('/register', fn() => view("register"));
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::middleware('check.user.session')->prefix('user')->group(function () {

    Route::get("/logout", function () {
        Session::flush();
        return redirect('/');
    });
    Route::get("/dashboard",fn() => view('user.dashboard'))->name('user.dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', fn() => view("user.users"))->name('user.users');
        Route::get('/data', [UserController::class, 'data'])->name('users.data');
        Route::get('/gpass/{id}', [UserController::class, 'gpass'])->name('user.gpass');
        Route::get('/gpass/pdf/{id}', [UserController::class, 'downloadPdf'])->name('user.gpass.pdf');
        Route::get('/scan', [UserController::class, 'scan'])->name('user.scan');
        Route::get('/scanstat', [UserController::class, 'scanstat'])->name('user.scanstat');
        Route::post('/attendance', [UserController::class, 'attendance'])->name('user.attendance');
        Route::get('/{id}', [UserController::class, 'get']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });

});