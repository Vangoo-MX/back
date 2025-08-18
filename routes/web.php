<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    Auth\ForgotPasswordController,
    Auth\ResetPasswordController,
    Auth\AuthController,
    ContactsController,
    Users\UserController,
    Properties\PropertyController,
    Properties\PropertyQueueController,
};
use App\Http\Controllers\Apartments\ApartmentController;
use App\Http\Controllers\Apartments\ApartmentHighlightController;
use App\Http\Controllers\Apartments\ApartmentQueueController;
use App\Http\Controllers\DevelopmentsHorizontals\DevelopmentHorizontalController;
use App\Http\Controllers\DevelopmentsHorizontals\DevelopmentHorizontalHighlightController;
use App\Http\Controllers\DevelopmentsVerticals\DevelopmentVerticalController;
use App\Http\Controllers\DevelopmentsVerticals\DevelopmentVerticalHighlightController;
use App\Http\Controllers\Lots\LotController;
use App\Http\Controllers\Lots\LotHighlightController;
use App\Http\Controllers\Properties\PropertyHighlightController;
use App\Http\Controllers\Terrains\TerrainController;
use App\Http\Controllers\Terrains\TerrainHighlightController;
use App\Http\Controllers\Terrains\TerrainQueueController;
use App\Http\Controllers\Agendas\AgendaController;
use App\Http\Controllers\Agendas\DocumentController;

Route::redirect('/', '/auth/user/login');

/*---------------------------------------------------------------------*/
/* PAGES */

// recuperacion de contraseña
Route::prefix('auth')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login.view');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
        Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
        Route::get('reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
        Route::post('reset', [ResetPasswordController::class, 'reset'])->name('update');
    });
});

// Apartamentos
Route::prefix('apartments')->name('apartments.')->middleware('check.admin')->group(function () {
    Route::put('active/{id}', [ApartmentController::class, 'active'])
        ->name('active');
    Route::put('deactive/{id}', [ApartmentController::class, 'deactive'])
        ->name('deactive');
    Route::post('{apartment}/reorder-images', [ApartmentController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{apartment}/{filename}', [ApartmentController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [ApartmentHighlightController::class, 'apartmentByMunicipio'])
        ->name('municipio');
    Route::put('queue/rejected/{id}', [ApartmentQueueController::class, 'reject'])
        ->name('queue.reject');
    Route::resource('queue', ApartmentQueueController::class)
        ->except(['create', 'show', 'edit', 'destroy']);
    Route::resource('highlights', ApartmentHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', ApartmentController::class)
        ->except(['create', 'store'])
        ->parameters(['' => 'apartment']);
});

// Desarrollos horizontales
Route::prefix('developments/horizontal')->name('horizontals.')->middleware('check.admin')->group(function () {
    Route::post('{horizontal}/reorder-images', [DevelopmentHorizontalController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{horizontal}/{filename}', [DevelopmentHorizontalController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [DevelopmentHorizontalHighlightController::class, 'horizontalByMunicipio'])
        ->name('municipio');
    Route::resource('highlights', DevelopmentHorizontalHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', DevelopmentHorizontalController::class)
        ->except(['show'])
        ->parameters(['' => 'horizontal']);
});

// Desarrollos verticales
Route::prefix('developments/vertical')->name('verticals.')->middleware('check.admin')->group(function () {
    Route::post('{vertical}/reorder-images', [DevelopmentVerticalController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{vertical}/{filename}', [DevelopmentVerticalController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [DevelopmentVerticalHighlightController::class, 'verticalByMunicipio'])
        ->name('municipio');
    Route::resource('highlights', DevelopmentVerticalHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', DevelopmentVerticalController::class)
        ->except(['show'])
        ->parameters(['' => 'vertical']);
});

//Lotes
Route::prefix('lots')->name('lots.')->middleware('check.admin')->group(function () {
    Route::post('{lot}/reorder-images', [LotController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{lot}/{filename}', [LotController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [LotHighlightController::class, 'lotsByMunicipio'])
        ->name('municipio');
    Route::resource('highlights', LotHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', LotController::class)
        ->except(['show'])
        ->parameters(['' => 'lot']);
});

// Propiedades
Route::prefix('properties')->name('properties.')->middleware('check.admin')->group(function () {
    Route::put('active/{id}', [PropertyController::class, 'active'])
        ->name('active');
    Route::put('deactive/{id}', [PropertyController::class, 'deactive'])
        ->name('deactive');
    Route::post('{property}/reorder-images', [PropertyController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{property}/{filename}', [PropertyController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [PropertyHighlightController::class, 'propertyByMunicipio'])
        ->name('municipio');
    Route::put('queue/rejected/{id}', [PropertyQueueController::class, 'reject'])
        ->name('queue.reject');
    Route::resource('queue', PropertyQueueController::class)
        ->except(['create', 'show', 'edit', 'destroy']);
    Route::resource('highlights', PropertyHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', PropertyController::class)
        ->except(['create', 'store'])
        ->parameters(['' => 'property']);
});

// Terrenos
Route::prefix('terrains')->name('terrains.')->middleware('check.admin')->group(function () {
    Route::put('active/{id}', [TerrainController::class, 'active'])
        ->name('active');
    Route::put('deactive/{id}', [TerrainController::class, 'deactive'])
        ->name('deactive');
    Route::post('{terrain}/reorder-images', [TerrainController::class, 'reorderImages'])
        ->name('reorder-images');
    Route::delete('deleteImage/{terrain}/{filename}', [TerrainController::class, 'deleteImage'])
        ->name('deleteImage');
    Route::get('municipio/{id}', [TerrainHighlightController::class, 'terrainByMunicipio'])
        ->name('municipio');
    Route::put('queue/rejected/{id}', [TerrainQueueController::class, 'reject'])
        ->name('queue.reject');
    Route::resource('queue', TerrainQueueController::class)
        ->except(['create', 'show', 'edit', 'destroy']);
    Route::resource('highlights', TerrainHighlightController::class)
        ->except(['create', 'show', 'edit']);
    Route::resource('/', TerrainController::class)
        ->except(['create', 'store'])
        ->parameters(['' => 'terrain']);
});

//Gestion de usuarios
Route::prefix('users')->name('users.')->middleware('check.admin')->group(function () {
    Route::put('{user:uuid}/status', [UserController::class, 'statusUser'])
        ->name('status.update');
    Route::resource('/', UserController::class)
        ->parameters(['' => 'user:uuid']);
});

//Agenda
Route::prefix('agenda')->name('agenda.')->middleware('check.admin')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('index');
    Route::get('/leido/{agenda}', [AgendaController::class, 'marcarComoLeido'])->name('marcarLeido');
    Route::get('/status/{agenda}/{etapa}', [AgendaController::class, 'statusContact'])->name('statusContact');
    Route::get('/show/{agenda}', [DocumentController::class, 'getDocuments'])->name('showDocuments');
});



// Panel de administración
Route::prefix('overview')->name('admin.')->group(function () {
    // Dashboard
    Route::get('home', [AdminController::class, 'index'])->name('index');
});

Route::get('emailconfirm', [AdminController::class, 'email_confirm'])->name('emails.confirm');

Route::get('emailtemplate', [AdminController::class, 'email_template'])->name('emails.template');

/* Agenda */

Route::post('ep/saveContactDocs', [ContactsController::class, 'saveContactDocs'])->middleware('web')->name('contactDocs.post');
