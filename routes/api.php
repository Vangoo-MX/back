<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Csrf\CsrfController,
    Agendas\AgendaApiController,
    Agendas\DocumentApiController,
    Apartments\ApartmentApiController,
    DevelopmentsHorizontals\DevelopmentHorizontalApiController,
    DevelopmentsVerticals\DevelopmentVerticalApiController,
    Favorites\FavoriteApiController,
    Locations\LocationApiController,
    Lots\LotApiController,
    MailsController,
    Properties\PropertyApiController,
    Terrains\TerrainApiController,
    Users\UserApiController,
    CommissionsController,
};
use App\Http\Controllers\DevelopmentsVerticals\ApartmentApiController as DevelopmentsVerticalsApartmentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('csrf-token', [CsrfController::class, 'show'])
    ->middleware(['web', 'throttle:10,1']);

Route::prefix('apartment')->group(function () {
    Route::get('search', [ApartmentApiController::class, 'getApartmentsSearch']);
    Route::get('queue/{id}', [ApartmentApiController::class, 'getApartmentQueue']);
    Route::post('queue/store', [ApartmentApiController::class, 'storeApartmentQueue']);
    Route::put('queue/update', [ApartmentApiController::class, 'updateApartmentQueue']);
    Route::delete('queue/delete/{id}', [ApartmentApiController::class, 'deleteApartmentQueue']);
    Route::post('images/upload', [ApartmentApiController::class, 'imagesUpload']);
    Route::post('images/delete', [ApartmentApiController::class, 'imagesDelete']);
    Route::get('detail/{id}', [ApartmentApiController::class, 'getApartment']);
    Route::get('related/{id}', [ApartmentApiController::class, 'getApartmentRelated']);
    Route::delete('delete/{id}', [ApartmentApiController::class, 'deleteApartment']);
    Route::get('user/{id}', [ApartmentApiController::class, 'getUserApartments']);
    Route::get('user/queue/{id}', [ApartmentApiController::class, 'getUserApartmentsQueue']);
});

Route::prefix('development-horizontal')->group(function () {
    Route::get('search', [DevelopmentHorizontalApiController::class, 'getDevelopmentsHorizontalSearch']);
    Route::get('detail/{id}', [DevelopmentHorizontalApiController::class, 'getDevelopmentHorizontal']);
    Route::get('related/{id}', [DevelopmentHorizontalApiController::class, 'getDevelopmentHorizontalRelated']);
});

Route::prefix('development-vertical')->group(function () {
    Route::get('search', [DevelopmentVerticalApiController::class, 'getDevelopmentsVerticalSearch']);
    Route::get('detail/{id}', [DevelopmentVerticalApiController::class, 'getDevelopmentVertical']);
    Route::get('related/{id}', [DevelopmentVerticalApiController::class, 'getDevelopmentVerticalRelated']);
    Route::get('apartments/{id}', [DevelopmentsVerticalsApartmentApiController::class, 'getApartments']);
});

Route::prefix('lot')->group(function () {
    Route::get('search', [LotApiController::class, 'getLotsSearch']);
    Route::get('detail/{id}', [LotApiController::class, 'getLot']);
    Route::get('related/{id}', [LotApiController::class, 'getLotRelated']);
});

Route::prefix('property')->group(function () {
    Route::get('search', [PropertyApiController::class, 'getPropertiesSearch']);
    Route::get('queue/{id}', [PropertyApiController::class, 'getPropertyQueue']);
    Route::post('queue/store', [PropertyApiController::class, 'storePropertyQueue']);
    Route::put('queue/update', [PropertyApiController::class, 'updatePropertyQueue']);
    Route::delete('queue/delete/{id}', [PropertyApiController::class, 'deletePropertyQueue']);
    Route::post('images/upload', [PropertyApiController::class, 'imagesUpload']);
    Route::post('images/delete', [PropertyApiController::class, 'imagesDelete']);
    Route::get('detail/{id}', [PropertyApiController::class, 'getProperty']);
    Route::get('related/{id}', [PropertyApiController::class, 'getPropertyRelated']);
    Route::delete('delete/{id}', [PropertyApiController::class, 'deleteProperty']);
    Route::get('user/{id}', [PropertyApiController::class, 'getUserProperties']);
    Route::get('user/queue/{id}', [PropertyApiController::class, 'getUserPropertiesQueue']);
});

Route::prefix('terrain')->group(function () {
    Route::get('search', [TerrainApiController::class, 'getTerrainsSearch']);
    Route::get('queue/{id}', [TerrainApiController::class, 'getTerrainQueue']);
    Route::post('queue/store', [TerrainApiController::class, 'storeTerrainQueue']);
    Route::put('queue/update', [TerrainApiController::class, 'updateTerrainQueue']);
    Route::delete('queue/delete/{id}', [TerrainApiController::class, 'deleteTerrainQueue']);
    Route::post('images/upload', [TerrainApiController::class, 'imagesUpload']);
    Route::post('images/delete', [TerrainApiController::class, 'imagesDelete']);
    Route::get('detail/{id}', [TerrainApiController::class, 'getTerrain']);
    Route::get('related/{id}', [TerrainApiController::class, 'getTerrainRelated']);
    Route::delete('delete/{id}', [TerrainApiController::class, 'deleteTerrain']);
    Route::get('user/{id}', [TerrainApiController::class, 'getUserTerrains']);
    Route::get('user/queue/{id}', [TerrainApiController::class, 'getUserTerrainsQueue']);
});

Route::prefix('highlight')->group(function () {
    Route::get('apartment/{municipioId?}', [ApartmentApiController::class, 'getApartmentsHighlights']);
    Route::get('property/{municipioId?}', [PropertyApiController::class, 'getPropertiesHighlights']);
    Route::get('lot/{municipioId?}', [LotApiController::class, 'getLotsHightlights']);
    Route::get('development/horizontal/{municipioId?}', [DevelopmentHorizontalApiController::class, 'getDevelopmentsHorizontalHighlights']);
    Route::get('development/vertical/{municipioId?}', [DevelopmentVerticalApiController::class, 'getDevelopmentsVerticalHighlights']);
    Route::get('terrain/{municipioId?}', [TerrainApiController::class, 'getTerrainsHighlights']);
});

Route::prefix('info')->group(function () {
    Route::get('municipios/{municipio}/highlights/{tipo}', [LocationApiController::class, 'getHighlightsByMunicipio'])
        ->where('tipo', 'apartments|terrains|developments-vertical|developments-horizontal|lots|properties');
    Route::get('municipios/{estadoId}', [LocationApiController::class, 'getMunicipios']);
    Route::get('municipios/estado/{estadoId}', [LocationApiController::class, 'getMunicipiosFromEstado']);
    Route::get('estados', [LocationApiController::class, 'getAllEstados']);
    Route::get('estados/search', [LocationApiController::class, 'getEstados']);
    Route::get('colonias/{municipioId}', [LocationApiController::class, 'getColonias']);
    Route::get('colonias/municipio/{municipioId}', [LocationApiController::class, 'getColoniasFromMunicipio']);
});

Route::prefix('user')->group(function () {
    Route::get('checkauth', [UserApiController::class, 'checkAuth'])
        ->middleware(['web', 'auth']);;
    Route::get('check-refresh', [UserApiController::class, 'forceCheck']);
    Route::get('logout', [UserApiController::class, 'logout']);
    Route::post('update', [UserApiController::class, 'updateUser']);
    route::get('info/{id}', [UserApiController::class, 'getInfoUser'])
        ->middleware('auth');
});

Route::prefix('favorite')->group(function () {
    Route::get('list/{id}', [FavoriteApiController::class, 'listsUser']);
    Route::post('create', [FavoriteApiController::class, 'createList']);
    Route::delete('delete/{id_user}/{id_list}', [FavoriteApiController::class, 'deleteList']);
    Route::get('data/{id}', [FavoriteApiController::class, 'dataFromList']);
    Route::delete('delete-item/{type_property}/{id_list}/{id_property}')
        ->where('type_property', 'property|development|developmentHorizontal|lot|apartment|terrain');
    Route::post('save', [FavoriteApiController::class, 'saveFavoriteItem']);
});

Route::prefix('agenda')->group(function () {
    Route::get('user/{userId}', [AgendaApiController::class, 'byUser']);
    Route::resource('/', AgendaApiController::class)
        ->parameters(['' => 'agenda'])
        ->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('document/{agendaId}', [DocumentApiController::class, 'getDocuments']);
    Route::put('document/update/{agendaDoc}', [DocumentApiController::class, 'updateNotes']);
});

Route::prefix('mails')->group(function () {
    Route::post('contact-agent', [MailsController::class, 'contactAgent'])
        ->middleware('throttle:10,1');
    Route::post('be-partner', [MailsController::class, 'bePartner'])
        ->middleware('throttle:10,1');
    Route::post('sales-advisor', [MailsController::class, 'salesAdvisor'])
        ->middleware('throttle:10,1');
});

Route::get('commissions/{type}', [CommissionsController::class, 'getCommissions'])
    ->where('type', 'dev|devHorizontal|property|lot|apartment|terrain');
