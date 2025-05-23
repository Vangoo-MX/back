<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    TerrainsController,
    Auth\ForgotPasswordController,
    Auth\ResetPasswordController,
    Auth\AuthController,
    ContactsController,
    DevelopmentsController,
    DevelopmentsApartmentsController,
    DevelopmentsHorizontalApartmentsController,
    DevelopmentsHorizontalController,
    LotsController,
    Users\UserController,
    Properties\PropertyController,
    Properties\PropertyQueueController,
};
use App\Http\Controllers\Apartments\ApartmentController;
use App\Http\Controllers\Apartments\ApartmentHighlightController;
use App\Http\Controllers\Apartments\ApartmentQueueController;
use App\Http\Controllers\Properties\PropertyHighlightController;
use App\Http\Controllers\Terrains\TerrainController;
use App\Http\Controllers\Terrains\TerrainHighlightController;
use App\Http\Controllers\Terrains\TerrainQueueController;

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
    Route::delete('deleteImage/{apartmentId}/{imageId}', [ApartmentController::class, 'destroyImage'])
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

// Propiedades
Route::prefix('properties')->name('properties.')->middleware('check.admin')->group(function () {
    Route::put('active/{id}', [PropertyController::class, 'active'])
        ->name('active');
    Route::put('deactive/{id}', [PropertyController::class, 'deactive'])
        ->name('deactive');
    Route::delete('deleteImage/{propertyId}/{imageId}', [PropertyController::class, 'destroyImage'])
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
    Route::delete('deleteImage/{terrainId}/{imageId}', [TerrainController::class, 'destroyImage'])
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
    Route::put('{user}/status', [UserController::class, 'statusUser'])
        ->name('status.update');
    Route::resource('/', UserController::class)
        ->parameters(['' => 'user']);
});



// Panel de administración
Route::prefix('overview')->name('admin.')->group(function () {
    // Dashboard
    Route::get('home', [AdminController::class, 'index'])->name('index');

    // Terrenos
    Route::prefix('terrains')->group(function () {
        Route::get('list', [AdminController::class, 'terrains'])->name('terrains');
        Route::get('details/{id}', [AdminController::class, 'detailsTerrains'])->name('detailsTerrains');
        Route::get('edit/{terrains}', [AdminController::class, 'editTerrainPage'])->name('editTerrainPage');
        Route::post('update/{terrains}', [TerrainsController::class, 'updateTerrains'])->name('terrainsUpdate');
        Route::get('queue', [AdminController::class, 'queueTerrains'])->name('queueTerrains');
        Route::get('highlights', [AdminController::class, 'highlightsTerrains'])->name('highlights.terrains');
        Route::get('deleteHighlight/{id}', [TerrainsController::class, 'deleteTerrainHightlight'])->name('deleteHighlightTerrain');
        Route::post('addHighlight', [TerrainsController::class, 'addTerrainHightlight'])->name('addHighlightTerrain');
        Route::post('orderHighlight', [TerrainsController::class, 'orderTerrainHightlight'])->name('orderHighlightTerrain');
        Route::get('rejectQueue/{id}', [TerrainsController::class, 'rejectTerrainQueue'])->name('rejectTerrainQueue');
        Route::get('revisionQueue/{id}', [TerrainsController::class, 'revisionTerrainQueue'])->name('revisionTerrainQueue');
        Route::post('aprovedQueue', [TerrainsController::class, 'aprovedTerrainsQueue'])->name('aprovedTerrainQueue');
        Route::get('delete/{id}', [TerrainsController::class, 'deleteTerrain'])->name('deleteTerrain');
        Route::delete('deleteImage/{terrainId}/{imageId}', [TerrainsController::class, 'deleteImage'])->name('deleteImageTerrain');
        Route::get('deactiveTerrain/{id}', [TerrainsController::class, 'deactiveTerrain'])->name('deactiveTerrain');
        Route::get('activeTerrain/{id}', [TerrainsController::class, 'activeTerrain'])->name('activeTerrain');
    });

    // Desarrollo vertical
    Route::prefix('developments')->group(function () {
        Route::get('list', [AdminController::class, 'developments'])->name('developments');
        Route::get('highlights', [AdminController::class, 'highlightsdev'])->name('highlights.developments');
        Route::get('create', [AdminController::class, 'createdev'])->name('createdev');
        Route::get('edit/{id}', [AdminController::class, 'editdev'])->name('editdev');
        Route::get('deleteHighlight/{id}', [DevelopmentsController::class, 'deleteDevHightlight'])->name('deleteHighlightDev');
        Route::post('addHighlight', [DevelopmentsController::class, 'addDevHightlight'])->name('addHighlightDev');
        Route::post('orderHighlight', [DevelopmentsController::class, 'orderDevHightlight'])->name('orderHighlightDev');
        Route::post('store', [DevelopmentsController::class, 'storeDev'])->name('storeDev');
        Route::post('edit', [DevelopmentsController::class, 'editdev'])->name('editDev');
        Route::delete('deleteImage/{developmentId}/{imageId}', [DevelopmentsController::class, 'deleteImage'])->name('deleteImageDev');
        Route::get('delete/{id}', [DevelopmentsController::class, 'deleteDev'])->name('deleteDev');
    });

    // Desarrollo horizontal
    Route::prefix('horizontal')->group(function () {
        Route::get('list', [AdminController::class, 'developmentsHorizontal'])->name('developmentsHorizontal');
        Route::get('highlights', [AdminController::class, 'highlightsdevHorizontal'])->name('highlights.developmentsHorizontal');
        Route::get('create', [AdminController::class, 'createdevHorizontal'])->name('createdevHorizontal');
        Route::get('edit/{id}', [AdminController::class, 'editdevHorizontal'])->name('editdevHorizontal');
        Route::get('deleteHighlight/{id}', [DevelopmentsHorizontalController::class, 'deleteDevHorizontalHightlight'])->name('deleteHighlightDevHorizontal');
        Route::post('addHighlight', [DevelopmentsHorizontalController::class, 'addDevHorizontalHightlight'])->name('addHighlightDevHorizontal');
        Route::post('orderHighlight', [DevelopmentsHorizontalController::class, 'orderDevHorizontalHightlight'])->name('orderHighlightDevHorizontal');
        Route::post('store', [DevelopmentsHorizontalController::class, 'storeDevHorizontal'])->name('storeDevHorizontal');
        Route::post('editDevhorizontal', [DevelopmentsHorizontalController::class, 'editDevHorizontal'])->name('editDevHorizontal');
        Route::delete('deleteImage/{developmentId}/{imageId}', [DevelopmentsHorizontalController::class, 'deleteImage'])->name('deleteImageDevHorizontal');
        Route::get('delete/{id}', [DevelopmentsHorizontalController::class, 'deleteDevHorizontal'])->name('deleteDevHorizontal');
    });

    // Lotes
    Route::prefix('lots')->group(function () {
        Route::get('list', [AdminController::class, 'lots'])->name('lots');
        Route::get('highlights', [AdminController::class, 'highlightsLot'])->name('highlights.lots');
        Route::get('create', [AdminController::class, 'createLot'])->name('createLot');
        Route::get('edit/{id}', [AdminController::class, 'editLotPage'])->name('editLot');
        Route::post('store', [LotsController::class, 'storeLot'])->name('storeLot');
        Route::post('update', [LotsController::class, 'editLot'])->name('updateLot');
        Route::delete('deleteImage/{lotId}/{imageId}', [LotsController::class, 'deleteImage'])->name('deleteImageLot');
        Route::get('delete/{id}', [LotsController::class, 'deleteLot'])->name('deleteLot');
        Route::get('deleteHighlight/{id}', [LotsController::class, 'deleteLotHightlight'])->name('deleteHighlightLot');
        Route::post('addHighlight', [LotsController::class, 'addLotHightlight'])->name('addHighlightLot');
        Route::post('orderHighlight', [LotsController::class, 'orderLotHightlight'])->name('orderHighlightLot');
    });

    // Varios
    Route::get('contacts', [AdminController::class, 'contacts'])->name('contacts');
    Route::get('files', [AdminController::class, 'files'])->name('files');
    Route::get('statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('settings', [AdminController::class, 'settingsinfo'])->name('settings');
});

Route::get('emailconfirm', [AdminController::class, 'email_confirm'])->name('emails.confirm');

Route::get('emailtemplate', [AdminController::class, 'email_template'])->name('emails.template');

/* TERRENOS */
Route::get('ep/getAllTerrains', [TerrainsController::class, 'getAll'])->name('epTerrains.get');

Route::get('ep/getTerrainCard/{id}', [TerrainsController::class, 'getTerrainCard'])->name('epTerrainCard.get');

Route::get('ep/getMultiTerrainCard/{array}', [TerrainsController::class, 'getMultiTerrainCard'])->name('epMultiTerrainCard.get');

Route::get('ep/getTerrainsImagesCards', [TerrainsController::class, 'getTerrainsImagesCards'])->name('epTerrainsImagesCards.get');

Route::get('ep/getTerrainsImagesDetail/{id}', [TerrainsController::class, 'getTerrainsImagesDetail'])->name('epTerrainsImagesDetail.get');

Route::get('ep/get-terrain-by-municipio/{id}', [TerrainsController::class, 'getTerrainsByMunicipio']);

/* terrains queue */

Route::get('ep/deleteTerrainQueue/{id}', [TerrainsController::class, 'deleteTerrainQueue'])->name('epTerrainQueue.delete');

/* DESARROLLOS */
Route::get('ep/getAllDevelopments', [DevelopmentsController::class, 'getAll'])->name('epAllDevelopments.get');

Route::get('ep/get-devs-by-municipio/{id}', [DevelopmentsController::class, 'getDevsByMunicipio']);

/* OPCIONES DESARROLLOS */
Route::get('ep/getApartmentsFromDev/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsFromDev'])->name('epApartmentsFromDev.get');

Route::get('ep/getApartmentsImages/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsImages'])->name('epApartmentsImages.get');

/* DESARROLLOS HORIZONTALES */

Route::get('ep/get-devs-horizontal-by-municipio/{id}', [DevelopmentsHorizontalController::class, 'getDevsHorizontalByMunicipio']);

Route::get('ep/getDevHorizontalSearch/{estado?}/{municipio?}/{colonia?}/{status?}/{min?}/{max?}', [DevelopmentsHorizontalController::class, 'getDevHorizontalSearch'])->name('epDevHorizontalSearch.get');

/* OPCIONES DESARROLLOS HORIZONTALES */
Route::get('ep/getApartmentsFromDevHorizontal/{id}', [DevelopmentsHorizontalApartmentsController::class, 'getApartmentsFromDevHorizontal'])->name('epApartmentsFromDevHorizontal.get');

Route::get('ep/getApartmentsImagesHorizontal/{id}', [DevelopmentsHorizontalApartmentsController::class, 'getApartmentsImagesHorizontal'])->name('epApartmentsImagesHorizontal.get');

/* LOTES */

Route::get('ep/getAllLots', [LotsController::class, 'getAll'])->name('epAllLots.get');

Route::get('ep/get-lots-by-municipio/{id}', [LotsController::class, 'getLotsByMunicipio']);

/* Agenda */

Route::get('ep/getDocsAgenda/{id}', [ContactsController::class, 'getDocsAgenda'])->name('contactDocs.get');

Route::post('ep/saveContactDocs', [ContactsController::class, 'saveContactDocs'])->middleware('web')->name('contactDocs.post');

Route::get('ep/statusContact/{id_agenda}/{etapa}', [ContactsController::class, 'statusContact'])->name('contactStatus.get');

Route::get('ep/MarcarLeido/{id_agenda}', [ContactsController::class, 'marcarComoLeido'])->name('marcarComoLeido');
