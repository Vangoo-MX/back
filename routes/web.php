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
    InfoController,
    FavoritesController,
    LotsController,
    MailsController
};


Route::get('/', HomeController::class)->name('home');

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
        Route::delete('delete/{id}', [ApartmentsController::class, 'deleteApartment'])->name('deleteApartment');
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
    });

    // Lotes
    Route::prefix('lots')->group(function () {
        Route::get('list', [AdminController::class, 'lots'])->name('lots');
        Route::get('highlights', [AdminController::class, 'highlightsLot'])->name('highlights.lots');
        Route::get('create', [AdminController::class, 'createLot'])->name('createLot');
        Route::get('edit/{id}', [AdminController::class, 'editLotPage'])->name('editLot');
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
Route::prefix('ep')->group(function () {
    Route::get('csrf-token', function () {
        return json_encode(csrf_token());
    });
    // Usuarios
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('getNameUser/{id}', [UserController::class, 'getNameUser'])->name('getUsername');
        Route::post('register', [UserController::class, 'register'])->name('register');
        Route::post('login', [UserController::class, 'login'])->name('login');
        Route::get('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('getAllInfoUser/{id}', [UserController::class, 'getAllInfoUser'])->name('getAllInfoUser');
        Route::post('registerep', [UserController::class, 'registerEP']);
        Route::post('loginep', [UserController::class, 'loginEP']);
        Route::get('logoutep', [UserController::class, 'logoutEP']);
        Route::get('checkauth', [UserController::class, 'checkAuthEP'])->middleware("cors");
        Route::get('status/{userid}/{status}', [UserController::class, 'statusUser'])->name('changestatus');
    });
    Route::post('updateUserEP', [UserController::class, 'updateUserEP']);
    Route::post('updateUserEPp2', [UserController::class, 'updateUserEPp2']);
});

/*endpoints*/

/* PROPIEDADES */
Route::get('ep/getAllProperties', [PropertiesController::class, 'getAll'])->name('epProperties.get');

Route::get('ep/getPropertiesHightlights', [PropertiesController::class, 'getPropertiesHightlights'])->name('epPropertiesHightlights.get');

Route::get('ep/getPropertiesHightlightFromMunicipio/{id}', [PropertiesController::class, 'getPropertiesHightlightFromMunicipio'])->name('epPropertiesHightlightFromMunicipio.get');

Route::get('ep/getPropertiesImagesCards', [PropertiesController::class, 'getPropertiesImagesCards'])->name('epPropertiesImagesCards.get');

Route::get('ep/getPropertiesImagesDetail/{id}', [PropertiesController::class, 'getPropertiesImagesDetail'])->name('epPropertiesImagesDetail.get');

Route::get('ep/getProperty/{id}', [PropertiesController::class, 'getProperty'])->name('epProperty.get');

Route::get('ep/getPropertyRelated/{id}', [PropertiesController::class, 'getPropertyRelated'])->name('epPropertyRelated.get');

Route::get('ep/getPropertySearch/{estado?}/{municipio?}/{colonia?}/{type?}/{min?}/{max?}', [PropertiesController::class, 'getPropertySearch'])->name('epPropertySearch.get');

Route::get('ep/getPropertyCard/{id}', [PropertiesController::class, 'getPropertyCard'])->name('epPropertyCard.get');

Route::get('ep/getMultiPropertyCard/{array}', [PropertiesController::class, 'getMultiPropertyCard'])->name('epMultiPropertyCard.get');

Route::get('ep/deleteProperty/{id}', [PropertiesController::class, 'deleteProperty'])->name('epProperty.delete');

Route::get('ep/deactiveProperty/{id}', [PropertiesController::class, 'deactiveProperty'])->name('epProperty.deactive');

Route::get('ep/activeProperty/{id}', [PropertiesController::class, 'activeProperty'])->name('epProperty.activate');

Route::get('ep/deletePropertyEP/{id}', [PropertiesController::class, 'deletePropertyEP']);

Route::get('ep/get-properties-by-municipio/{id}', [PropertiesController::class, 'getpropertiesbymunicipio']);

/* Propiedades user */

Route::get('ep/getUserProperties/{id}', [PropertiesController::class, 'getUserProperties']);

Route::get('ep/getUserPropertiesQueue/{id}', [PropertiesController::class, 'getUserPropertiesQueue']);

/* property queue */
Route::post('ep/postPropertyQueue', [PropertiesController::class, 'postPropertiesQueue'])->name('epPropertyQueue.post');

Route::get('ep/rejectPropertyQueue/{id}', [PropertiesController::class, 'rejectPropertyQueue'])->name('epPropertyQueue.reject');

Route::get('ep/revisionPropertyQueue/{id}', [PropertiesController::class, 'revisionPropertyQueue'])->name('epPropertyQueue.revision');

Route::post('ep/aprovedPropertyQueue', [PropertiesController::class, 'aprovedPropertyQueue'])->name('epPropertyQueue.aproved');

Route::get('ep/deletePropertyQueue/{id}', [PropertiesController::class, 'deletePropertyQueue'])->name('epPropertyQueue.delete');

Route::get('ep/deletePropertyQueueEP/{id}', [PropertiesController::class, 'deletePropertyQueueEP']);

Route::post('ep/updatePropertiesQueue', [PropertiesController::class, 'updatePropertiesQueue']);

Route::post('ep/imagesPropertyQueue', [PropertiesController::class, 'imagesPropertyQueue']);

Route::post('ep/deleteImagesPropertyQueue', [PropertiesController::class, 'deleteImagesPropertyQueue']);

Route::get('ep/getPropertyQueueEP/{id}', [PropertiesController::class, 'getPropertyQueueEP']);

Route::delete('ep/editpropertie/{propertieId}/{imageId}', [PropertiesController::class, 'deleteImage'])->name('propertie.images.delete');

/* APARTAMENTOS */

Route::get('ep/getAllApartments', [ApartmentsController::class, 'getAll'])->name('epApartments.get');

Route::get('ep/getApartmentsHightlights', [ApartmentsController::class, 'getApartmentsHightlights'])->name('epApartmentsHightlights.get');

Route::get('ep/getApartmentsHightlightFromMunicipio/{id}', [ApartmentsController::class, 'getApartmentsHightlightFromMunicipio'])->name('epApartmentsHightlightFromMunicipio.get');

Route::get('ep/getApartmentCard/{id}', [ApartmentsController::class, 'getApartmentCard'])->name('epApartmentCard.get');

Route::get('ep/getMultiApartmentCard/{array}', [ApartmentsController::class, 'getMultiApartmentCard'])->name('epMultiApartmentCard.get');

Route::get('ep/getApartmentsImagesCards', [ApartmentsController::class, 'getApartmentsImagesCards'])->name('epApartmentsImagesCards.get');

Route::get('ep/getApartmentsImagesDetail/{id}', [ApartmentsController::class, 'getApartmentsImagesDetail'])->name('epApartmentsImagesDetail.get');

Route::get('ep/getApartment/{id}', [ApartmentsController::class, 'getApartment'])->name('epApartment.get');

Route::get('ep/getApartmentsRelated/{id}', [ApartmentsController::class, 'getApartmentsRelated'])->name('epApartmentsRelated.get');

Route::get('ep/getApartmentSearch/{estado?}/{municipio?}/{colonia?}/{type?}/{min?}/{max?}', [ApartmentsController::class, 'getApartmentSearch'])->name('epApartmentSearch.get');

Route::get('ep/get-apartment-by-municipio/{id}', [ApartmentsController::class, 'getApartmentsByMunicipio']);

Route::get('ep/deleteApartmentEP/{id}', [ApartmentsController::class, 'deleteApartmentEP']);

/* Apartamentos user */

Route::get('ep/getUserApartments/{id}', [ApartmentsController::class, 'getUserApartments']);

Route::get('ep/getUserApartmentsQueue/{id}', [ApartmentsController::class, 'getUserApartmentsQueue']);

/* apartments queue */
Route::post('ep/postApartmentsQueue', [ApartmentsController::class, 'postApartmentsQueue'])->name('epApartmentsQueue.post');

Route::get('ep/deleteApartmentQueueEP/{id}', [ApartmentsController::class, 'deleteApartmentQueueEP']);

Route::post('ep/updateApartmentsQueue', [ApartmentsController::class, 'updateApartmentsQueue']);

Route::post('ep/imagesApartmentsQueue', [ApartmentsController::class, 'imagesApartmentsQueue']);

Route::post('ep/deleteImagesApartmentsQueue', [ApartmentsController::class, 'deleteImagesApartmentsQueue']);

Route::get('ep/getApartmentQueueEP/{id}', [ApartmentsController::class, 'getApartmentQueueEP']);

/* TERRENOS */
Route::get('ep/getAllTerrains', [TerrainsController::class, 'getAll'])->name('epTerrains.get');

Route::get('ep/getTerrainsHightlights', [TerrainsController::class, 'getTerrainsHightlights'])->name('epTerrainsHightlights.get');

Route::get('ep/getTerrainsHightlightFromMunicipio/{id}', [TerrainsController::class, 'getTerrainsHightlightFromMunicipio'])->name('epTerrainsHightlightFromMunicipio.get');

Route::get('ep/getTerrainCard/{id}', [TerrainsController::class, 'getTerrainCard'])->name('epTerrainCard.get');

Route::get('ep/getMultiTerrainCard/{array}', [TerrainsController::class, 'getMultiTerrainCard'])->name('epMultiTerrainCard.get');

Route::get('ep/getTerrainsImagesCards', [TerrainsController::class, 'getTerrainsImagesCards'])->name('epTerrainsImagesCards.get');

Route::get('ep/getTerrainsImagesDetail/{id}', [TerrainsController::class, 'getTerrainsImagesDetail'])->name('epTerrainsImagesDetail.get');

Route::get('ep/getTerrain/{id}', [TerrainsController::class, 'getTerrain'])->name('epTerrain.get');

Route::get('ep/getTerrainsRelated/{id}', [TerrainsController::class, 'getTerrainsRelated'])->name('epTerrainsRelated.get');

Route::get('ep/getTerrainSearch/{estado?}/{municipio?}/{colonia?}/{type?}/{min?}/{max?}', [TerrainsController::class, 'getTerrainSearch'])->name('epTerrainSearch.get');

Route::get('ep/get-terrain-by-municipio/{id}', [TerrainsController::class, 'getTerrainsByMunicipio']);

Route::get('ep/deleteTerrain/{id}', [TerrainsController::class, 'deleteTerrain'])->name('epTerrain.delete');

Route::get('ep/deactiveTerrain/{id}', [TerrainsController::class, 'deactiveTerrain'])->name('epTerrain.deactive');

Route::get('ep/activeTerrain/{id}', [TerrainsController::class, 'activeTerrain'])->name('epTerrain.activate');

Route::delete('ep/edit-terrain/{terrainId}/{imageId}', [TerrainsController::class, 'deleteImage'])->name('terrain.images.delete');

Route::get('ep/deleteTerrainEP/{id}', [TerrainsController::class, 'deleteTerrainEP']);

/* Terrenos user */
Route::get('ep/getUserTerrains/{id}', [TerrainsController::class, 'getUserTerrains']);

Route::get('ep/getUserTerrainsQueue/{id}', [TerrainsController::class, 'getUserTerrainsQueue']);

/* terrains queue */
Route::post('ep/postTerrainsQueue', [TerrainsController::class, 'postTerrainsQueue'])->name('epTerrainsQueue.post');

Route::get('ep/rejectTerrainQueue/{id}', [TerrainsController::class, 'rejectTerrainQueue'])->name('epTerrainQueue.reject');

Route::get('ep/revisionTerrainQueue/{id}', [TerrainsController::class, 'revisionTerrainQueue'])->name('epTerrainQueue.revision');

Route::post('ep/aprovedTerrainQueue', [TerrainsController::class, 'aprovedTerrainsQueue'])->name('epTerrainsQueue.aproved');

Route::get('ep/deleteTerrainQueue/{id}', [TerrainsController::class, 'deleteTerrainQueue'])->name('epTerrainQueue.delete');

Route::get('ep/deleteTerrainQueueEP/{id}', [TerrainsController::class, 'deleteTerrainQueueEP']);

Route::post('ep/updateTerrainsQueue', [TerrainsController::class, 'updateTerrainsQueue']);

Route::post('ep/imagesTerrainsQueue', [TerrainsController::class, 'imagesTerrainsQueue']);

Route::post('ep/deleteImagesTerrainsQueue', [TerrainsController::class, 'deleteImagesTerrainsQueue']);

Route::get('ep/getTerrainQueueEP/{id}', [TerrainsController::class, 'getTerrainQueueEP']);

/* DESARROLLOS */
Route::get('ep/getAllDevelopments', [DevelopmentsController::class, 'getAll'])->name('epAllDevelopments.get');

Route::get('ep/getDevelopmentsVerticalHightlights', [DevelopmentsController::class, 'getDevelopmentsVerticalHightlights'])->name('epDevelopmentsVerticalHightlights.get');

Route::get('ep/getDevelopmentsVerticalHightlightFromMunicipio/{id}', [DevelopmentsController::class, 'getDevelopmentsVerticalHightlightFromMunicipio'])->name('epDevelopmentsVerticalHightlightFromMunicipio.get');

Route::get('ep/get-devs-by-municipio/{id}', [DevelopmentsController::class, 'getDevsByMunicipio']);

Route::get('ep/getDevelopmentsImagesCards', [DevelopmentsController::class, 'getDevelopmentsImagesCards'])->name('epDevelopmentsImagesCards.get');

Route::get('ep/getDevelopmentsImagesDetail/{id}', [DevelopmentsController::class, 'getDevelopmentsImagesDetail'])->name('epDevelopmentsImagesDetail.get');

Route::get('ep/getDevelopment/{id}', [DevelopmentsController::class, 'getDevelopment'])->name('epDevelopment.get');

Route::get('ep/getDevelopmentsRelated/{id}', [DevelopmentsController::class, 'getDevelopmentsRelated'])->name('epDevelopmentsRelated.get');

Route::get('ep/getDevCard/{id}', [DevelopmentsController::class, 'getDevCard'])->name('epDevCard.get');

Route::get('ep/getMultiDevCard/{id}', [DevelopmentsController::class, 'getMultiDevCard'])->name('epMultiDevCard.get');

Route::get('ep/getDevSearch/{estado?}/{municipio?}/{colonia?}/{status?}/{min?}/{max?}', [DevelopmentsController::class, 'getDevSearch'])->name('epDevSearch.get');

/* OPCIONES DESARROLLOS */
Route::get('ep/getApartmentsFromDev/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsFromDev'])->name('epApartmentsFromDev.get');

Route::get('ep/getApartmentsImages/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsImages'])->name('epApartmentsImages.get');

/* DESARROLLOS HORIZONTALES */
Route::get('ep/getAllDevelopmentsHorizontal', [DevelopmentsHorizontalController::class, 'getAll'])->name('epAllDevelopmentsHorizontal.get');

Route::get('ep/getDevelopmentsHorizontalHightlights', [DevelopmentsHorizontalController::class, 'getDevelopmentsHorizontalHightlights'])->name('epDevelopmentsHorizontalHightlights.get');

Route::get('ep/getDevelopmentsHorizontalHightlightFromMunicipio/{id}', [DevelopmentsHorizontalController::class, 'getDevelopmentsHorizontalHightlightFromMunicipio'])->name('epDevelopmentsHorizontalHightlightFromMunicipio.get');

Route::get('ep/get-devs-horizontal-by-municipio/{id}', [DevelopmentsHorizontalController::class, 'getDevsHorizontalByMunicipio']);

Route::get('ep/getDevelopmentsHorizontalImagesCards', [DevelopmentsHorizontalController::class, 'getDevelopmentsHorizontalImagesCards'])->name('epDevelopmentsHorizontalImagesCards.get');

Route::get('ep/getDevelopmentsHorizontalImagesDetail/{id}', [DevelopmentsHorizontalController::class, 'getDevelopmentsHorizontalImagesDetail'])->name('epDevelopmentsHorizontalImagesDetail.get');

Route::get('ep/getDevelopmentHorizontal/{id}', [DevelopmentsHorizontalController::class, 'getDevelopmentHorizontal'])->name('epDevelopmentHorizontal.get');

Route::get('ep/getDevelopmentsHorizontalRelated/{id}', [DevelopmentsHorizontalController::class, 'getDevelopmentsHorizontalRelated'])->name('epDevelopmentsHorizontalRelated.get');

Route::get('ep/getDevHorizontalCard/{id}', [DevelopmentsHorizontalController::class, 'getDevHorizontalCard'])->name('epDevHorizontalCard.get');

Route::get('ep/getMultiDevHorizontalCard/{id}', [DevelopmentsHorizontalController::class, 'getMultiDevHorizontalCard'])->name('epMultiDevHorizontalCard.get');

Route::get('ep/getDevHorizontalSearch/{estado?}/{municipio?}/{colonia?}/{status?}/{min?}/{max?}', [DevelopmentsHorizontalController::class, 'getDevHorizontalSearch'])->name('epDevHorizontalSearch.get');

Route::post('ep/storedevhorizontal', [DevelopmentsHorizontalController::class, 'storeDevHorizontal'])->name('epDevHorizontal.store');

Route::get('ep/deletedevhorizontal/{id}', [DevelopmentsHorizontalController::class, 'deleteDevHorizontal'])->name('epDevHorizontal.delete');

Route::post('ep/editdevhorizontal', [DevelopmentsHorizontalController::class, 'editDevHorizontal'])->name('epDevHorizontal.edit');

Route::delete('ep/devHorizontal/{developmentId}/{imageId}', [DevelopmentsHorizontalController::class, 'deleteImage'])->name('developmentHorizontal.images.delete');

/* OPCIONES DESARROLLOS HORIZONTALES */
Route::get('ep/getApartmentsFromDevHorizontal/{id}', [DevelopmentsHorizontalApartmentsController::class, 'getApartmentsFromDevHorizontal'])->name('epApartmentsFromDevHorizontal.get');

Route::get('ep/getApartmentsImagesHorizontal/{id}', [DevelopmentsHorizontalApartmentsController::class, 'getApartmentsImagesHorizontal'])->name('epApartmentsImagesHorizontal.get');

/* LOTES */

Route::get('ep/getAllLots', [LotsController::class, 'getAll'])->name('epAllLots.get');

Route::post('ep/storelot', [LotsController::class, 'storeLot'])->name('epLot.store');

Route::get('ep/deletelot/{id}', [LotsController::class, 'deleteLot'])->name('epLot.delete');

Route::post('ep/editlot', [LotsController::class, 'editLot'])->name('epLot.edit');

Route::delete('ep/editlot/{lotId}/{imageId}', [LotsController::class, 'deleteImage'])->name('lot.images.delete');

Route::get('ep/get-lots-by-municipio/{id}', [LotsController::class, 'getLotsByMunicipio']);

Route::get('ep/getLotsHightlights', [LotsController::class, 'getLotsHightlights'])->name('epLotsHightlights.get');

Route::get('ep/getLotsHightlightFromMunicipio/{id}', [LotsController::class, 'getLotsHightlightFromMunicipio'])->name('epLotsHightlightFromMunicipio.get');

Route::get('ep/getLotsImagesDetail/{id}', [LotsController::class, 'getLotsImagesDetail'])->name('epLotsImagesDetail.get');

Route::get('ep/getLotsImagesCards', [LotsController::class, 'getLotsImagesCards'])->name('epLotsImagesCards.get');

Route::get('ep/getLot/{id}', [LotsController::class, 'getLot'])->name('epLot.get');

Route::get('ep/getLotsRelated/{id}', [LotsController::class, 'getLotsRelated'])->name('epLotsRelated.get');

Route::get('ep/getLotCard/{id}', [LotsController::class, 'getLotCard'])->name('epLotCard.get');

Route::get('ep/getMultiLotCard/{id}', [LotsController::class, 'getMultiLotCard'])->name('epMultiLotCard.get');

Route::get('ep/getLotSearch/{estado?}/{municipio?}/{colonia?}/{status?}/{min?}/{max?}', [LotsController::class, 'getLotSearch'])->name('epLotSearch.get');

/* INFORMACIÓN */
Route::get('ep/getEstado/{id}', [InfoController::class, 'getEstado'])->name('estado.get');

Route::get('ep/getAllEstados', [InfoController::class, 'getEstados']);

Route::get('ep/getMunicipiosFromEstado/{id}', [InfoController::class, 'getMunicipiosFromEstado']);

Route::get('ep/getMunicipio/{id}', [InfoController::class, 'getMunicipio']);

Route::get('ep/getColoniasFromMunicipio/{id}', [InfoController::class, 'getColoniasFromMunicipio']);

Route::get('ep/getColonia/{id}', [InfoController::class, 'getColonia']);

Route::get('ep/getEstadoMunicipioColonia/{estado}/{municipio}/{colonia}', [InfoController::class, 'getEstadoMunicipioColonia']);

/* FAVORITOS */

Route::get('ep/allFavoritesUsuarioData/{id}', [FavoritesController::class, 'allFavoritesUsuarioData']);

Route::get('ep/checkIfFav/{id}/{idproperty}/{type}', [FavoritesController::class, 'checkIfFav']);

Route::get('ep/allFavNoListUser/{id}', [FavoritesController::class, 'allFavNoListUser']);

Route::post('ep/postPropertiesFavUser', [FavoritesController::class, 'postPropertiesFavUser']);

Route::post('ep/postDevFavUser', [FavoritesController::class, 'postDevFavUser']);

Route::post('ep/postDevHorizontalFavUser', [FavoritesController::class, 'postDevHorizontalFavUser']);

Route::post('ep/postLotFavUser', [FavoritesController::class, 'postLotFavUser']);

Route::post('ep/postApartmentFavUser', [FavoritesController::class, 'postApartmentFavUser']);

Route::get('ep/deletePropertyFavUser/{id_list}/{id_property}/{type_property}', [FavoritesController::class, 'deletePropertyFavUser']);

Route::get('ep/listsFavUser/{id}', [FavoritesController::class, 'listsUser']);

Route::get('ep/deleteListUser/{id_user}/{id_list}', [FavoritesController::class, 'deleteListUser']);

Route::get('ep/propertiesFromList/{id}', [FavoritesController::class, 'propertiesFromList']);

Route::post('ep/createListUser', [FavoritesController::class, 'createListUser']);

Route::post('ep/savePropertyInList', [FavoritesController::class, 'savePropertyInList']);

/* Agenda */
Route::get('ep/getAgenda/{id}', [ContactsController::class, 'getAgendaUser'])->name('agenda.get');

Route::get('ep/getAgendaById/{id}', [ContactsController::class, 'getAgenda'])->name('agendaById.get');

Route::post('ep/saveAgenda', [ContactsController::class, 'saveAgendaUser'])->name('agenda.post');

Route::get('ep/deleteAgenda/{id}', [ContactsController::class, 'deleteAgendaUser'])->name('agenda.delete');

Route::post('ep/updateAgenda', [ContactsController::class, 'updateAgendaUser'])->name('agenda.update');

Route::post('ep/saveContactDocs', [ContactsController::class, 'saveContactDocs'])->middleware('web')->name('contactDocs.post');

Route::get('ep/getDocsAgenda/{id}', [ContactsController::class, 'getDocsAgenda'])->name('contactDocs.get');

Route::get('ep/statusContact/{id_agenda}/{etapa}', [ContactsController::class, 'statusContact'])->name('contactStatus.get');

Route::get('ep/saveAgendaDocsNotes/{id_docs}/{note}', [ContactsController::class, 'saveAgendaDocsNotes'])->name('contactNotes.get');

Route::get('ep/MarcarLeido/{id_agenda}', [ContactsController::class, 'marcarComoLeido'])->name('marcarComoLeido');

/* Tickets */
Route::get('ep/getTicket/{id}', [ContactsController::class, 'getTicket'])->name('ticket.get');
Route::post('ep/saveTicket', [ContactsController::class, 'saveTicket'])->name('ticket.post');
Route::get('ep/getTicketsUser/{id}', [ContactsController::class, 'getTicketsUser'])->name('ticketsUser.get');
Route::get('ep/getTicketsSendUser/{id}', [ContactsController::class, 'getTicketsSendUser'])->name('ticketsSendUser.get');
Route::get('ep/deleteTicket/{id}', [ContactsController::class, 'deleteTicket'])->name('ticket.delete');
Route::post('ep/editStatusTicket', [ContactsController::class, 'editStatusTicket'])->name('editStatusTicket.post');

/* highlights edit */

Route::get('ep/deleteHighlight/{id}', [PropertiesController::class, 'deletePropertyHightlight'])->name('Highlight.delete');

Route::post('ep/addHighlight', [PropertiesController::class, 'addPropertyHightlight'])->name('Highlight.add');

Route::post('ep/orderHighlight', [PropertiesController::class, 'orderPropertyHightlight'])->name('Highlight.order');

/*---------*/
Route::get('ep/deleteHighlightTerrain/{id}', [TerrainsController::class, 'deleteTerrainHightlight'])->name('HighlightTerrain.delete');

Route::post('ep/addHighlightTerrain', [TerrainsController::class, 'addTerrainHightlight'])->name('HighlightTerrain.add');

Route::post('ep/orderHighlightTerrain', [TerrainsController::class, 'orderTerrainHightlight'])->name('HighlightTerrain.order');

/*---------*/

Route::get('ep/deleteHighlightdevHorizontal/{id}', [DevelopmentsHorizontalController::class, 'deleteDevHorizontalHightlight'])->name('HighlightdevHorizontal.delete');

Route::post('ep/addHighlightdevHorizontal', [DevelopmentsHorizontalController::class, 'addDevHorizontalHightlight'])->name('HighlightdevHorizontal.add');

Route::post('ep/orderHighlightdevHorizontal', [DevelopmentsHorizontalController::class, 'orderDevHorizontalHightlight'])->name('HighlightdevHorizontal.order');
/*---------*/

Route::get('ep/deleteHighlightlot/{id}', [LotsController::class, 'deleteLotHightlight'])->name('highlightLot.delete');

Route::post('ep/addHighlightlot', [LotsController::class, 'addLotHightlight'])->name('highlightLot.add');

Route::post('ep/orderHighlightlot', [LotsController::class, 'orderLotHightlight'])->name('highlightLot.order');

/*----EMAIL---*/

Route::post('ep/bepartnerEP', [MailsController::class, 'bepartnerEP'])->name('bepartner.post');
Route::post('ep/contactAgentMail', [MailsController::class, 'contactAgent'])->name('contactAgent.post');
Route::post('ep/salesAdvisor', [MailsController::class, 'salesAdvisor'])->name('salesAdvisor.post');

//Comissions
Route::get('ep/commissions/{type}', [CommissionsController::class, 'getCommissionsEP'])->name('ep.commissions');
