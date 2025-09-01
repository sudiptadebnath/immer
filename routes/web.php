<?php

use App\Http\Controllers\ConfController;
use App\Http\Controllers\PujaController;
use App\Http\Controllers\ScanController;
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

Route::get('/register', fn() => view("register"));
Route::post('/register', [PujaController::class, 'add']);


Route::middleware('check.user.session')->prefix('user')->group(function () {

    Route::get("/logout", function () {
        Session::flush();
        return redirect('/');
    });

    Route::get("/dashboard", fn() => view('user.dashboard'))->name('user.dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', fn() => view("user.users"))->name('user.users');
        Route::post('/add', [UserController::class, 'add']);
        Route::get('/data', [UserController::class, 'data'])->name('users.data');
        Route::view('/profile','user.profile');
        Route::get('/{id}', [UserController::class, 'get']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });

    Route::prefix('puja')->group(function () {
        Route::view('/','puja.index');
        Route::get('/data', [PujaController::class, 'data'])->name('puja.data');
        Route::get('/gpass/{id}', [PujaController::class, 'gpass'])->name('puja.gpass');
        Route::get('/gpass/pdf/{id}', [PujaController::class, 'downloadPdf'])->name('puja.gpass.pdf');
        Route::post('/add', [PujaController::class, 'add']);
        Route::get('/{id}', [PujaController::class, 'get']);
        Route::put('/{id}', [PujaController::class, 'update']);
        Route::delete('/{id}', [PujaController::class, 'delete']);
    });

    Route::prefix('att')->group(function () {
        Route::get('/scan', [ScanController::class, 'scanview'])->name('att.scan');
        Route::post('/mark_by_mob', [ScanController::class, 'mark_by_mob'])->name('att.mark_by_mob');
        Route::get('/scanstat', [ScanController::class, 'scanstat'])->name('att.scanstat');
        Route::post('/mark_by_qr', [ScanController::class, 'mark_by_qr'])->name('att.mark_by_qr');
    });

    Route::prefix('conf')->group(function () {
        Route::get('/settings', fn() => view("conf.settings"))->name('conf.settings');

        Route::get('/action', fn() => view("conf.actions"))->name('conf.action');
        Route::get('/action_data', [ConfController::class, 'action_data'])->name('conf.action_data');

        Route::get('/category', fn() => view("conf.settings",['page' => 'category']))->name('conf.category');
        Route::get('/commitee', fn() => view("conf.settings",['page' => 'commitee']))->name('conf.commitee');
        Route::get('/immerdt', fn() => view("conf.settings",['page' => 'immerdt']))->name('conf.immerdt');
        Route::post('/settings', [ConfController::class, 'save_settings'])->name('conf.save_settings');
    });
});
