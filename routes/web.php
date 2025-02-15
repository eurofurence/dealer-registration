<?php

use App\Http\Controllers\Applications\ApplicationController;
use App\Http\Controllers\CommentController;
use App\Models\Application;
use App\Models\Comment;
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

Route::redirect('/login', '/auth/login')->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [\App\Http\Controllers\Auth\OidcClientController::class, 'login'])->name('auth.login');
    Route::get('/auth/callback', [
        \App\Http\Controllers\Auth\OidcClientController::class,
        'callback',
    ])->name('auth.callback');
});

Route::get('/auth/frontchannel-logout', \App\Http\Controllers\Auth\FrontChannelLogoutController::class)->name('auth.frontchannel-logout');
Route::post('/auth/frontchannel-logout', \App\Http\Controllers\Auth\FrontChannelLogoutController::class)->name('auth.frontchannel-logout-post');

Route::get('/', function () {
    return \Illuminate\Support\Facades\Redirect::route('dashboard');
});

Route::middleware(['auth:web', \App\Http\Middleware\AccessTokenValidationMiddleware::class, \App\Http\Middleware\ForceOverseatingRedirectMiddleware::class, \App\Http\Middleware\ConventionDateRestrictionMiddleware::class])->group(function () {
    Route::post('invitation/join', [\App\Http\Controllers\Applications\InvitationController::class, 'view'])->name('invitation.join');
    Route::get('invitation/join', fn() => \Illuminate\Support\Facades\Redirect::route('dashboard'));
    Route::post('invitation/confirm', [\App\Http\Controllers\Applications\InvitationController::class, 'store'])->name('invitation.confirm');
    Route::get('invitation/confirm', fn() => \Illuminate\Support\Facades\Redirect::route('dashboard'));
    Route::get('applications/create', [\App\Http\Controllers\Applications\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('applications', [\App\Http\Controllers\Applications\ApplicationController::class, 'store'])->name('applications.store');
    Route::get('applications/edit', [\App\Http\Controllers\Applications\ApplicationController::class, 'edit'])->name('applications.edit');
    Route::put('applications', [\App\Http\Controllers\Applications\ApplicationController::class, 'update'])->name('applications.update');
    Route::get('applications/delete', [\App\Http\Controllers\Applications\ApplicationController::class, 'delete'])->name('applications.delete');
    Route::delete('applications', [\App\Http\Controllers\Applications\ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::get('applications/invitees', [\App\Http\Controllers\Applications\InviteesController::class, 'view'])->name('applications.invitees.view');
    Route::post('applications/invitees/delete', [\App\Http\Controllers\Applications\InviteesController::class, 'delete'])->name('applications.invitees.delete');
    Route::delete('applications/invitees', [\App\Http\Controllers\Applications\InviteesController::class, 'destroy'])->name('applications.invitees.destroy');
    Route::post('applications/invitees/codes', [\App\Http\Controllers\Applications\InviteesController::class, 'codes'])->name('applications.invitees.codes');

    Route::get('table/confirm', [\App\Http\Controllers\TableVerifyController::class, 'view'])->name('table.confirm');
    Route::put('table/confirm', [\App\Http\Controllers\TableVerifyController::class, 'update'])->name('table.update');

    Route::post('join', [\App\Http\Controllers\Applications\InvitationController::class, 'store'])->name('join.submit');

    Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');

    Route::get('applications/invitees/regenerate-keys', function () {
        return \Illuminate\Support\Facades\Redirect::route('dashboard');
    });

    Route::get('applications', function () {
        return \Illuminate\Support\Facades\Redirect::route('dashboard');
    });

    Route::get('closed', fn() => view('closed'))->name('closed');

    Route::get('admin/export/appdata', [ApplicationController::class, 'exportAppDataAdmin']);
    Route::get('admin/export/csv', [ApplicationController::class, 'exportCsvAdmin']);
    Route::get('admin/export/comments', [CommentController::class, 'exportCommentsAdmin']);
});

Route::middleware(['auth:web', \App\Http\Middleware\AccessTokenValidationMiddleware::class])->group(function () {
    Route::get('frontdesk', \App\Http\Controllers\FrontdeskController::class)->name('frontdesk')->can('viewAny', Application::class);
    Route::post('frontdesk/check-in', [\App\Http\Controllers\FrontdeskController::class, 'checkIn'])->name('frontdesk.check-in')->can('checkIn', Application::class);
    Route::post('frontdesk/check-out', [\App\Http\Controllers\FrontdeskController::class, 'checkOut'])->name('frontdesk.check-out')->can('checkOut', Application::class);
    Route::post('frontdesk/comment', [\App\Http\Controllers\FrontdeskController::class, 'comment'])->name('frontdesk.comment')->can('create', Comment::class);
});


// Basic auth using credentials from env
Route::middleware('auth.api.basic')->group(function () {
    Route::get('export/appdata', [ApplicationController::class, 'exportAppData']);
});
