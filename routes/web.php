<?php

use App\Http\Controllers\ProfileController;
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

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [\App\Http\Controllers\Auth\OidcClientController::class, 'login'])->name('auth.login');
    Route::get('/auth/callback', [
        \App\Http\Controllers\Auth\OidcClientController::class,
        'callback',
    ])->name('auth.callback');
});

Route::get('/auth/frontchannel-logout', \App\Http\Controllers\Auth\FrontChannelLogoutController::class)->name('auth.frontchannel-logout');

Route::get('/', function () {
    return \Illuminate\Support\Facades\Redirect::route('dashboard');
});

Route::middleware(['auth:web',\App\Http\Middleware\AccessTokenValidationMiddleware::class,\App\Http\Middleware\ForceOverseatingRedirectMiddleware::class])->group(function () {
    Route::get('join',[\App\Http\Controllers\Applications\InvitationController::class,'view'])->name('join');
    Route::get('applications/create',[\App\Http\Controllers\Applications\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('applications',[\App\Http\Controllers\Applications\ApplicationController::class, 'store'])->name('applications.store');
    Route::get('applications/edit',[\App\Http\Controllers\Applications\ApplicationController::class, 'edit'])->name('applications.edit');
    Route::put('applications',[\App\Http\Controllers\Applications\ApplicationController::class, 'update'])->name('applications.update');
    Route::get('applications/delete',[\App\Http\Controllers\Applications\ApplicationController::class, 'delete'])->name('applications.delete');
    Route::delete('applications',[\App\Http\Controllers\Applications\ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::get('applications/invitees',[\App\Http\Controllers\Applications\InviteesController::class,'view'])->name('applications.invitees.view');
    Route::delete('applications/invitees',[\App\Http\Controllers\Applications\InviteesController::class,'destroy'])->name('applications.invitees.destroy');
    Route::post('applications/invitees/regenerate-keys',[\App\Http\Controllers\Applications\InviteesController::class,'regenerateKeys'])->name('applications.invitees.regenerate-keys');

    Route::post('join',[\App\Http\Controllers\Applications\InvitationController::class,'store'])->name('join.submit');

    Route::get('table/verify', \App\Http\Controllers\TableVerifyController::class)->name('table.verify');
    Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');
});

