<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    AdminController,
    ApartmentsController,
    TerrainsController,
    Auth\ForgotPasswordController,
    Auth\ResetPasswordController,
    CommissionsController,
    ContactsController,
    UserController,
    PropertiesController,
    DevelopmentsController,
    DevelopmentsApartmentsController,
    DevelopmentsHorizontalApartmentsController,
    DevelopmentsHorizontalController,
    LotsController,
    MailsController
};


Route::redirect('/', '/user/login');

/*---------------------------------------------------------------------*/
/* PAGES */

// recuperacion de contraseña
Route::prefix('auth')->name('password.')->group(function () {
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('update');
});

// Panel de administración
Route::prefix('overview')->name('admin.')->group(function () {
    // Dashboard
    Route::get('home', [AdminController::class, 'index'])->name('index');

    // Gestión de usuarios
    Route::prefix('users')->group(function () {
        Route::get('create', [AdminController::class, 'create'])->name('create');
        Route::post('store', [AdminController::class, 'store'])->name('storeuser');
        Route::get('list', [AdminController::class, 'allusers'])->name('users');
        Route::get('{id}', [AdminController::class, 'show'])->name('user');
        Route::get('edit/{user}', [AdminController::class, 'edit'])->name('edit');
        Route::post('update/{user}', [AdminController::class, 'update'])->name('update');
        Route::get('password/{user}', [AdminController::class, 'password'])->name('passChange');
        Route::post('passwordUpdate/{user}', [AdminController::class, 'passwordUpdate'])->name('passUpdate');
        Route::delete('delete/{id}', [AdminController::class, 'destroy'])->name('destroy');
    });

    // Propiedades
    Route::prefix('properties')->group(function () {
        Route::get('list', [AdminController::class, 'properties'])->name('properties');
        Route::get('details/{id}', [AdminController::class, 'details'])->name('details');
        Route::get('edit/{propiedad}', [AdminController::class, 'showProperties'])->name('showProperties');
        Route::post('update/{propiedad}', [PropertiesController::class, 'updateProperties'])->name('propertiesUpdate');
        Route::get('queue', [AdminController::class, 'queue'])->name('queue');
        Route::get('highlights', [AdminController::class, 'highlights'])->name('highlights.properties');
        Route::get('deleteHighlight/{id}', [PropertiesController::class, 'deletePropertyHightlight'])->name('deleteHighlightProperties');
        Route::post('addHighlight', [PropertiesController::class, 'addPropertyHightlight'])->name('addHighlightProperties');
        Route::post('orderHighlight', [PropertiesController::class, 'orderPropertyHightlight'])->name('orderHighlightProperties');
        Route::get('rejectQueue/{id}', [PropertiesController::class, 'rejectPropertyQueue'])->name('rejectPropertyQueue');
        Route::get('revisionQueue/{id}', [PropertiesController::class, 'revisionPropertyQueue'])->name('revisionPropertyQueue');
        Route::post('aprovedQueue', [PropertiesController::class, 'aprovedPropertyQueue'])->name('aprovedPropertyQueue');
        Route::get('delete/{id}', [PropertiesController::class, 'deleteProperty'])->name('deleteProperty');
        Route::delete('deleteImage/{propertyId}/{imageId}', [PropertiesController::class, 'deleteImage'])->name('deleteImageProperty');
        Route::get('deactiveProperty/{id}', [PropertiesController::class, 'deactiveProperty'])->name('deactiveProperty');
        Route::get('activeProperty/{id}', [PropertiesController::class, 'activeProperty'])->name('activeProperty');
    });

    // Apartamentos
    Route::prefix('apartments')->group(function () {
        Route::get('list', [AdminController::class, 'apartments'])->name('apartments');
        Route::get('details/{id}', [AdminController::class, 'detailsApartments'])->name('detailsApartments');
        Route::get('edit/{apartments}', [AdminController::class, 'editApartmentPage'])->name('editApartmentPage');
        Route::post('update/{apartments}', [ApartmentsController::class, 'updateApartments'])->name('apartmentsUpdate');
        Route::get('queue', [AdminController::class, 'queueApartments'])->name('queueApartments');
        Route::get('highlights', [AdminController::class, 'highlightsApartments'])->name('highlights.apartments');
        Route::get('deleteHighlight/{id}', [ApartmentsController::class, 'deleteApartmentHightlight'])->name('deleteHighlightApartment');
        Route::post('addHighlight', [ApartmentsController::class, 'addApartmentHightlight'])->name('addHighlightApartment');
        Route::post('orderHighlight', [ApartmentsController::class, 'orderApartmentHightlight'])->name('orderHighlightApartment');
        Route::get('rejectQueue/{id}', [ApartmentsController::class, 'rejectApartmentQueue'])->name('rejectApartmentQueue');
        Route::get('revisionQueue/{id}', [ApartmentsController::class, 'revisionApartmentQueue'])->name('revisionApartmentQueue');
        Route::post('aprovedQueue', [ApartmentsController::class, 'aprovedApartmentsQueue'])->name('aprovedApartmentQueue');
        Route::get('delete/{id}', [ApartmentsController::class, 'deleteApartment'])->name('deleteApartment');
        Route::delete('deleteImage/{apartmentId}/{imageId}', [ApartmentsController::class, 'deleteImage'])->name('deleteImageApartment');
        Route::get('deactiveApartment/{id}', [ApartmentsController::class, 'deactiveApartment'])->name('deactiveApartment');
        Route::get('activeApartment/{id}', [ApartmentsController::class, 'activeApartment'])->name('activeApartment');
    });

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

Route::get('/getColonias', [AdminController::class, 'getColonias'])->name('getColonias');

Route::get('emailconfirm', [AdminController::class, 'email_confirm'])->name('emails.confirm');

Route::get('emailtemplate', [AdminController::class, 'email_template'])->name('emails.template');

/* USER ENDPOINTS*/
Route::prefix('user')->name('user.')->group(function () {
    Route::post('register', [UserController::class, 'register'])
        ->name('register');
    Route::get('login', [UserController::class, 'showLoginForm'])->name('login.view');
    Route::post('login', [UserController::class, 'login'])
        ->name('login');
    Route::get('logout', [UserController::class, 'logout'])
        ->name('logout');
    Route::get('status/{userid}/{status}', [UserController::class, 'statusUser'])
        ->name('changestatus');
});

/*endpoints*/

/* PROPIEDADES */

Route::get('ep/get-properties-by-municipio/{id}', [PropertiesController::class, 'getpropertiesbymunicipio']);

/* property queue */

Route::get('ep/deletePropertyQueue/{id}', [PropertiesController::class, 'deletePropertyQueue'])->name('epPropertyQueue.delete');

Route::get('ep/getPropertyQueueEP/{id}', [PropertiesController::class, 'getPropertyQueueEP']);

/* APARTAMENTOS */

Route::get('ep/getAllApartments', [ApartmentsController::class, 'getAll'])->name('epApartments.get');

Route::get('ep/getApartmentCard/{id}', [ApartmentsController::class, 'getApartmentCard'])->name('epApartmentCard.get');

Route::get('ep/getMultiApartmentCard/{array}', [ApartmentsController::class, 'getMultiApartmentCard'])->name('epMultiApartmentCard.get');

Route::get('ep/getApartmentsImagesCards', [ApartmentsController::class, 'getApartmentsImagesCards'])->name('epApartmentsImagesCards.get');

Route::get('ep/getApartmentsImagesDetail/{id}', [ApartmentsController::class, 'getApartmentsImagesDetail'])->name('epApartmentsImagesDetail.get');

Route::get('ep/get-apartment-by-municipio/{id}', [ApartmentsController::class, 'getApartmentsByMunicipio']);

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

Route::post('ep/saveContactDocs', [ContactsController::class, 'saveContactDocs'])->middleware('web')->name('contactDocs.post');

Route::get('ep/statusContact/{id_agenda}/{etapa}', [ContactsController::class, 'statusContact'])->name('contactStatus.get');

Route::get('ep/MarcarLeido/{id_agenda}', [ContactsController::class, 'marcarComoLeido'])->name('marcarComoLeido');
