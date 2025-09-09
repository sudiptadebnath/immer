<?php

use App\Http\Controllers\ConfController;
use App\Http\Controllers\PujaController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RepoController;

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
Route::get('/gpass/{id}', [PujaController::class, 'gpass'])->name('puja.gpass');
Route::get('/gpass/pdf/{id}', [PujaController::class, 'downloadPdf'])->name('puja.gpass.pdf');
Route::get('/get/committees', [ConfController::class, 'get_committees'])->name('conf.get.committees');

Route::middleware(['check.user.session','request.sanitize'])->prefix('user')->group(function () {

    Route::get("/logout", function () {
        Session::flush();
        return redirect('/');
    });

    Route::get("/dashboard", fn() => view('user.dashboard'))->name('user.dashboard');

    Route::middleware('role:a')->prefix('users')->group(function () {
        Route::get('/', fn() => view("user.users"))->name('user.users');
        Route::post('/add', [UserController::class, 'add']);
        Route::get('/data', [UserController::class, 'data'])->name('users.data');
        Route::view('/profile','user.profile');
        Route::get('/{id}', [UserController::class, 'get']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });

    Route::middleware('role:ao')->prefix('puja')->group(function () {
        Route::view('/','puja.index');
        Route::get('/data', [PujaController::class, 'data'])->name('puja.data');
        Route::post('/add', [PujaController::class, 'add']);
        Route::get('/entryslip/{id}', [PujaController::class, 'entryslip'])->name('puja.entryslip');
        Route::get('/{id}', [PujaController::class, 'get']);
        Route::put('/{id}', [PujaController::class, 'update']);
        Route::delete('/{id}', [PujaController::class, 'delete']);
    });

    Route::middleware('role:ao')->prefix('repo')->group(function () {
        Route::view('/istat', 'repo.istat')->name('repo.istat');
        Route::view('/regs', 'repo.reg')->name('repo.regs');
        Route::get('/regsdata', [RepoController::class, 'regsdata'])->name('repo.regsdata');
        Route::view('/immer', 'repo.immer')->name('repo.immer');
        Route::get('/immerdata', [RepoController::class, 'immerdata'])->name('repo.immerdata');
        Route::view('/dhun', 'repo.dhun')->name('repo.dhun');
        Route::get('/dhundata', [RepoController::class, 'dhundata'])->name('repo.dhundata');
    });

    Route::middleware('role:aos')->prefix('att')->group(function () {
        Route::get('/scan', [ScanController::class, 'scanview'])->name('att.scan');
        Route::post('/mark_by_mob', [ScanController::class, 'mark_by_mob'])->name('att.mark_by_mob');
        Route::get('/scanstat', [ScanController::class, 'scanstat'])->name('att.scanstat');
        Route::get('/scanstat_bydt', [ScanController::class, 'scanstat_bydt'])->name('att.scanstat_bydt');
        Route::get('/getcomm_bydt', [ScanController::class, 'getcomm_bydt'])->name('att.getcomm_bydt');
        Route::post('/mark_by_qr', [ScanController::class, 'mark_by_qr'])->name('att.mark_by_qr');
    });

    Route::middleware('role:ao')->prefix('conf')->group(function () {
        //Route::get('/settings', fn() => view("conf.settings"))->name('conf.settings');

        Route::get('/action', fn() => view("conf.actions"))->name('conf.action');
        Route::get('/data/action', [ConfController::class, 'data_action'])->name('conf.data.action');
        Route::post('/data/action/updateorder', [ConfController::class, 'updateorder_action'])->name('conf.updateorder.action');
        Route::get('/get/action/{id}', [ConfController::class, 'get_action'])->name('conf.get.action');
        Route::post('/add/action', [ConfController::class, 'add_action'])->name('conf.add.action');
        Route::put('/edit/action/{id}', [ConfController::class, 'edit_action'])->name('conf.edit.action');
        Route::delete('/del/action/{id}', [ConfController::class, 'del_action'])->name('conf.del.action');

        Route::get('/category', fn() => view("conf.category"))->name('conf.category');
        Route::get('/data/category', [ConfController::class, 'data_category'])->name('conf.data.category');
        Route::post('/data/category/updateorder', [ConfController::class, 'updateorder_category'])->name('conf.updateorder.category');
        Route::get('/get/category/{id}', [ConfController::class, 'get_category'])->name('conf.get.category');
        Route::post('/add/category', [ConfController::class, 'add_category'])->name('conf.add.category');
        Route::put('/edit/category/{id}', [ConfController::class, 'edit_category'])->name('conf.edit.category');
        Route::delete('/del/category/{id}', [ConfController::class, 'del_category'])->name('conf.del.category');

        Route::get('/committee', fn() => view("conf.committee"))->name('conf.committee');
        Route::get('/data/committee', [ConfController::class, 'data_committee'])->name('conf.data.committee');
        Route::post('/data/committee/updateorder', [ConfController::class, 'updateorder_committee'])->name('conf.updateorder.committee');
        Route::get('/get/committee/{id}', [ConfController::class, 'get_committee'])->name('conf.get.committee');
        Route::post('/add/committee', [ConfController::class, 'add_committee'])->name('conf.add.committee');
        Route::put('/edit/committee/{id}', [ConfController::class, 'edit_committee'])->name('conf.edit.committee');
        Route::delete('/del/committee/{id}', [ConfController::class, 'del_committee'])->name('conf.del.committee');

        Route::get('/immerdt', fn() => view("conf.immerdt"))->name('conf.immerdt');
        Route::get('/data/immerdt', [ConfController::class, 'data_immerdt'])->name('conf.data.immerdt');
        Route::get('/get/immerdt/{id}', [ConfController::class, 'get_immerdt'])->name('conf.get.immerdt');
        Route::post('/add/immerdt', [ConfController::class, 'add_immerdt'])->name('conf.add.immerdt');
        Route::put('/edit/immerdt/{id}', [ConfController::class, 'edit_immerdt'])->name('conf.edit.immerdt');
        Route::delete('/del/immerdt/{id}', [ConfController::class, 'del_immerdt'])->name('conf.del.immerdt');
    });
});
