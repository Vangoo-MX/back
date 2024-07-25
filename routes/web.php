<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\DevelopmentsController;
use App\Http\Controllers\DevelopmentsApartmentsController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\LotsController;

Route::get('/', HomeController::class)->name('home');

/*---------------------------------------------------------------------*/
/* PAGES */

Route::get('overview/home', [AdminController::class, 'index'])->name('admin.index');

Route::get('overview/create', [AdminController::class, 'create'])->name('admin.create');

Route::get('overview/user/{id?}', [AdminController::class, 'show'])->name('admin.user');

Route::get('overview/edit/{user}', [AdminController::class, 'edit'])->name('admin.edit');

Route::post('admin/edit/{user}', [AdminController::class, 'update'])->name('admin.update');

Route::get('overview/password/{user}', [AdminController::class, 'password'])->name('admin.passChange');

Route::post('admin/password/{user}', [AdminController::class, 'updatePassword'])->name('admin.passUpdate');

Route::get('overview/properties', [AdminController::class, 'properties'])->name('admin.properties');

Route::get('overview/details/{id}', [AdminController::class, 'details'])->name('admin.details');

Route::get('overview/details/edit/{propiedad}', [AdminController::class, 'showProperties'])->name('admin.showProperties');

Route::get('/getColonias', [AdminController::class, 'getColonias'])->name('getColonias');

Route::post('admin/details/edit/{propiedad}', [AdminController::class, 'updateProperties'])->name('admin.propertiesUpdate');

Route::get('overview/developments', [AdminController::class, 'developments'])->name('admin.developments');

Route::get('overview/lots', [AdminController::class, 'lots'])->name('admin.lots');

Route::get('overview/queue', [AdminController::class, 'queue'])->name('admin.queue');

Route::get('overview/users', [AdminController::class, 'allusers'])->name('admin.users');

Route::delete('overview/delete/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');

Route::get('overview/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');

Route::get('overview/files', [AdminController::class, 'files'])->name('admin.files');

Route::get('overview/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');

Route::get('overview/settingsinfo', [AdminController::class, 'settingsinfo'])->name('admin.settingsinfo');

Route::get('overview/properties-highlights', [AdminController::class, 'highlights'])->name('admin.highlights.properties');

Route::get('overview/developments-highlights', [AdminController::class, 'highlightsdev'])->name('admin.highlights.developments');

Route::get('overview/lots-highlights', [AdminController::class, 'highlightsLot'])->name('admin.highlights.lots');

Route::post('admin/store', [AdminController::class, 'store'])->name('admin.storeuser');

Route::get('overview/createdev', [AdminController::class, 'createdev'])->name('admin.createdev');

Route::get('overview/editdev/{id}', [AdminController::class, 'editdev'])->name('admin.editdev');

Route::get('overview/createlot', [AdminController::class, 'createLot'])->name('admin.createLot');

Route::get('emailconfirm', [AdminController::class, 'email_confirm'])->name('emails.confirm');

Route::get('emailtemplate', [AdminController::class, 'email_template'])->name('emails.template');

/* USER ENDPOINTS*/

Route::get('ep/user/getNameUser/{id}', [UserController::class, 'getNameUser'])->name('username.get');

Route::post('ep/user/register', [UserController::class, 'register'])->name('user.register');

Route::post('ep/user/login', [UserController::class, 'login'])->name('user.login');

Route::get('ep/user/logout', [UserController::class, 'logout'])->name('user.logout');

Route::get('ep/user/getAllInfoUser/{id}', [UserController::class, 'getAllInfoUser'])->name('getAllInfoUser.get');

/* USER ENDPOINTS FRONTEND */

Route::get('ep/csrf-token', function () {
    return json_encode(csrf_token());
});

Route::post('ep/user/registerep', [UserController::class, 'registerEP']);

Route::post('ep/user/loginep', [UserController::class, 'loginEP']);

Route::get('ep/user/logoutep', [UserController::class, 'logoutEP']);

Route::get('ep/user/checkauth', [UserController::class, 'checkAuthEP'])->middleware("cors");

/*update user*/

Route::post('ep/updateUserEP', [UserController::class, 'updateUserEP']);

Route::post('ep/updateUserEPp2', [UserController::class, 'updateUserEPp2']);

Route::get('ep/statusUser/{userid}/{status}', [UserController::class, 'statusUser'])->name('user.changestatus');

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

/* DESARROLLOS */
Route::get('ep/getAllDevelopments', [DevelopmentsController::class, 'getAll'])->name('epAllDevelopments.get');

Route::get('ep/getDevelopmentsHightlights', [DevelopmentsController::class, 'getDevelopmentsHightlights'])->name('epDevelopmentsHightlights.get');

Route::get('ep/getDevelopmentsHightlightFromMunicipio/{id}', [DevelopmentsController::class, 'getDevelopmentsHightlightFromMunicipio'])->name('epDevelopmentsHightlightFromMunicipio.get');

Route::get('ep/get-devs-by-municipio/{id}', [DevelopmentsController::class, 'getdevsbymunicipio']);

Route::get('ep/getDevelopmentsImagesCards', [DevelopmentsController::class, 'getDevelopmentsImagesCards'])->name('epDevelopmentsImagesCards.get');

Route::get('ep/getDevelopmentsImagesDetail/{id}', [DevelopmentsController::class, 'getDevelopmentsImagesDetail'])->name('epDevelopmentsImagesDetail.get');

Route::get('ep/getDevelopment/{id}', [DevelopmentsController::class, 'getDevelopment'])->name('epDevelopment.get');

Route::get('ep/getDevelopmentsRelated/{id}', [DevelopmentsController::class, 'getDevelopmentsRelated'])->name('epDevelopmentsRelated.get');

Route::get('ep/getDevCard/{id}', [DevelopmentsController::class, 'getDevCard'])->name('epDevCard.get');

Route::get('ep/getMultiDevCard/{id}', [DevelopmentsController::class, 'getMultiDevCard'])->name('epMultiDevCard.get');

Route::get('ep/getDevSearch/{estado?}/{municipio?}/{colonia?}/{status?}/{min?}/{max?}', [DevelopmentsController::class, 'getDevSearch'])->name('epDevSearch.get');

Route::post('ep/storedev', [DevelopmentsController::class, 'storeDev'])->name('epDev.store');

Route::get('ep/deletedev/{id}', [DevelopmentsController::class, 'deleteDev'])->name('epDev.delete');

Route::get('ep/editdevelopments/{id}', [AdminController::class, 'editdevpage'])->name('dev.edit');

Route::post('ep/editdev', [DevelopmentsController::class, 'editdev'])->name('epDev.edit');

Route::delete('ep/dev/{developmentId}/{imageId}', [DevelopmentsController::class, 'deleteImage'])->name('development.images.delete');

/* OPCIONES DESARROLLOS */
Route::get('ep/getApartmentsFromDev/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsFromDev'])->name('epApartmentsFromDev.get');

Route::get('ep/getApartmentsImages/{id}', [DevelopmentsApartmentsController::class, 'getApartmentsImages'])->name('epApartmentsImages.get');

/* LOTES */
Route::post('ep/storelot', [LotsController::class, 'storeLot'])->name('epLot.store');
Route::get('ep/deletelot/{id}', [LotsController::class, 'deleteLot'])->name('epLot.delete');
Route::get('ep/editlots/{id}', [AdminController::class, 'editLotPage'])->name('lot.edit');
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
Route::get('ep/getLotsCommissions/{type}', [LotsController::class, 'getCommissionsEP'])->name('epLot.commissions');
/* INFORMACIÓN */
Route::get('ep/getEstado/{id}', [InfoController::class, 'getEstado'])->name('estado.get');
Route::get('ep/getAllEstados', [InfoController::class, 'getEstados']);
Route::get('ep/getMunicipiosFromEstado/{id}', [InfoController::class, 'getMunicipiosFromEstado']);
Route::get('ep/getMunicipio/{id}', [InfoController::class, 'getMunicipio']);
Route::get('ep/getColoniasFromMunicipio/{id}', [InfoController::class, 'getColoniasFromMunicipio']);
Route::get('ep/getColonia/{id}', [InfoController::class, 'getColonia']);
Route::get('ep/getEstadoMunicipioColonia/{estado}/{municipio}/{colonia}', [InfoController::class, 'getEstadoMunicipioColonia']);

/* FAVORITOS */

//Route::get('ep/allFavoritesUsuario/{id}', [FavoritesController::class,'allFavoritesUsuario']);

Route::get('ep/allFavoritesUsuarioData/{id}', [FavoritesController::class, 'allFavoritesUsuarioData']);

Route::get('ep/checkIfFav/{id}/{idproperty}/{type}', [FavoritesController::class, 'checkIfFav']);

Route::get('ep/allFavNoListUser/{id}', [FavoritesController::class, 'allFavNoListUser']);

Route::post('ep/postPropertiesFavUser', [FavoritesController::class, 'postPropertiesFavUser']);

Route::post('ep/postDevFavUser', [FavoritesController::class, 'postDevFavUser']);

Route::get('ep/deletePropertyFavUser/{id_user}/{id_property}/{type_property}', [FavoritesController::class, 'deletePropertyFavUser']);

Route::get('ep/listsFavUser/{id}', [FavoritesController::class, 'listsUser']);

Route::get('ep/deleteListUser/{id_user}/{id_list}', [FavoritesController::class, 'deleteListUser']);

Route::get('ep/propertiesFromList/{id}', [FavoritesController::class, 'propertiesFromList']);

Route::post('ep/createListUser', [FavoritesController::class, 'createListUser']);

Route::post('ep/savePropertyInList', [FavoritesController::class, 'savePropertyInList']);

/* Agenda */
Route::get('ep/getAgenda/{id}', [FavoritesController::class, 'getAgendaUser'])->name('agenda.get');

Route::get('ep/getAgendaById/{id}', [FavoritesController::class, 'getAgenda'])->name('agendaById.get');

Route::post('ep/saveAgenda', [FavoritesController::class, 'saveAgendaUser'])->name('agenda.post');

Route::get('ep/deleteAgenda/{id}', [FavoritesController::class, 'deleteAgendaUser'])->name('agenda.delete');

Route::post('ep/updateAgenda', [FavoritesController::class, 'updateAgendaUser'])->name('agenda.update');

Route::post('ep/saveContactDocs', [FavoritesController::class, 'saveContactDocs'])->middleware('web')->name('contactDocs.post');

Route::get('ep/getDocsAgenda/{id}', [FavoritesController::class, 'getDocsAgenda'])->name('contactDocs.get');

Route::get('ep/statusContact/{id_agenda}/{etapa}', [FavoritesController::class, 'statusContact'])->name('contactStatus.get');

Route::get('ep/saveAgendaDocsNotes/{id_docs}/{note}', [FavoritesController::class, 'saveAgendaDocsNotes'])->name('contactNotes.get');

/* Tickets */
Route::get('ep/getTicket/{id}', [FavoritesController::class, 'getTicket'])->name('ticket.get');
Route::post('ep/saveTicket', [FavoritesController::class, 'saveTicket'])->name('ticket.post');
Route::get('ep/getTicketsUser/{id}', [FavoritesController::class, 'getTicketsUser'])->name('ticketsUser.get');
Route::get('ep/getTicketsSendUser/{id}', [FavoritesController::class, 'getTicketsSendUser'])->name('ticketsSendUser.get');
Route::get('ep/deleteTicket/{id}', [FavoritesController::class, 'deleteTicket'])->name('ticket.delete');
Route::post('ep/editStatusTicket', [FavoritesController::class, 'editStatusTicket'])->name('editStatusTicket.post');

/* highlights edit */

Route::get('ep/deleteHighlight/{id}', [PropertiesController::class, 'deletePropertyHightlight'])->name('Highlight.delete');

Route::post('ep/addHighlight', [PropertiesController::class, 'addPropertyHightlight'])->name('Highlight.add');

Route::post('ep/orderHighlight', [PropertiesController::class, 'orderPropertyHightlight'])->name('Highlight.order');

/*---------*/

Route::get('ep/deleteHighlightdev/{id}', [DevelopmentsController::class, 'deleteDevHightlight'])->name('Highlightdev.delete');

Route::post('ep/addHighlightdev', [DevelopmentsController::class, 'addDevHightlight'])->name('Highlightdev.add');

Route::post('ep/orderHighlightdev', [DevelopmentsController::class, 'orderDevHightlight'])->name('Highlightdev.order');

/*---------*/

Route::get('ep/deleteHighlightlot/{id}', [LotsController::class, 'deleteLotHightlight'])->name('highlightLot.delete');

Route::post('ep/addHighlightlot', [LotsController::class, 'addLotHightlight'])->name('highlightLot.add');

Route::post('ep/orderHighlightlot', [LotsController::class, 'orderLotHightlight'])->name('highlightLot.order');

/*----EMAIL---*/

Route::post('ep/bepartnerEP', [UserController::class, 'bepartnerEP'])->name('bepartner.post');

Route::post('ep/contactAgentMail', [FavoritesController::class, 'contactAgent'])->name('contactAgent.post');

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

//Comissions
Route::get('ep/commissions/{type}', [CommissionsController::class, 'getCommissionsEP'])->name('ep.commissions');
