@extends('layouts.adminLayout')

@section('breadcrumb','Editar propiedad')

@section('title','Editar propiedad')

@section('titleContent','Editar propiedad')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center w-100">

    <form method="post" class="w-100" enctype="multipart/form-data" action="{{ route('epProperty.edit') }}">
        @csrf
        <input type="hidden" name="id" value="">
        <!------------------------------------------->
        <div class="p-2">
            <div>
                <div>
                    <div class="d-flex">
                        <div class="w-md-100 w-lg-50 pe-5">

                            <label class="form-label">Tipo de propiedad</label>
                            <select class="form-select" formControlName="propertyType">
                                <option value="casa">Casa</option>
                                <option value="departamento">Departamento</option>
                                <option value="terreno">Terreno</option>
                            </select>
                            <div class="mb-3 mt-3">
                                <label class="form-label">Titulo del anuncio*</label>
                                <input type="text" class="form-control" placeholder="casa en..." maxlength="400">
                            </div>
                            <div class="mb-3 mt-3">
                                <label class="mb-2">Descripción del anuncio</label>
                                <textarea class="form-control" rows="8"></textarea>
                            </div>

                            <div class="d-flex numberDataTerrain gap-3 w-100">

                                <div class="mb-3">
                                    <label class="form-label">Área del terreno*</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control">
                                        <span class="input-group-text">M²</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Área de la construcción</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" formControlName="propertyAreaConstruction" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyAreaConstruction.invalid && editPropertyQueueForm.controls.propertyAreaConstruction.touched}">
                                        <span class="input-group-text">M²</span>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex numberDataTerrain gap-3 w-100">

                                <div class="mb-3 group">
                                    <label class="form-label">Año de construcción</label>
                                    <div>
                                        <input type="number" class="form-control" formControlName="propertyAgeConstruction" maxlength="4" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyAgeConstruction.invalid && editPropertyQueueForm.controls.propertyAgeConstruction.touched}">
                                    </div>
                                </div>

                                <div class="mb-3 group">
                                    <label class="form-label">Piso en el que se encuentra</label>
                                    <div>
                                        <input type="number" class="form-control" formControlName="propertyFloor" maxlength="4" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyFloor.invalid && editPropertyQueueForm.controls.propertyFloor.touched}">
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex numberDataTerrain gap-3 w-100">

                                <div class="mb-3 group">
                                    <label class="form-label">Tipo de desarrollo en el que se encuentra</label>
                                    <select class="form-select" formControlName="propertyDevType" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyDevType.invalid && editPropertyQueueForm.controls.propertyDevType.touched}">
                                        <option value="desconocido">Desconocido</option>
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>

                            </div>


                        </div>
                        <div class="w-md-100 w-lg-50 ps-5">

                            <div>
                                <label class="form-label">Tipo de operación</label>
                                <select class="form-select w-25" formControlName="propertyOperationType" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyOperationType.invalid && editPropertyQueueForm.controls.propertyOperationType.touched}">
                                    <option value="venta">Venta</option>
                                    <option value="preventa">Preventa</option>
                                    <option value="renta">Renta</option>
                                </select>
                            </div>

                            <div class="mb-3 mt-3 w-50">
                                <label class="form-label">Precio de venta</label>
                                <div class="input-group">
                                    <input type="text" class="form-control w-60" placeholder="3,000,000" formControlName="propertySellPrice" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertySellPrice.invalid && editPropertyQueueForm.controls.propertySellPrice.touched}">
                                    <select class="form-select end">
                                        <option valor="mxn">MXN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 mt-3 w-50">
                                <label class="form-label">Precio de mantenimiento</label>
                                <div class="input-group">
                                    <input type="text" class="form-control w-60" placeholder="3,000,000" formControlName="propertyMaintenancePrice" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyMaintenancePrice.invalid && editPropertyQueueForm.controls.propertyMaintenancePrice.touched}">
                                    <select class="form-select end">
                                        <option valor="mxn">MXN</option>
                                    </select>
                                </div>
                            </div>

                            <label class="form-label">Precio basado en</label>
                            <div class="d-flex gap-3 w-75">

                                <select class="form-select w-50" formControlName="priceBased">
                                    <option value="valortotal">Valor total</option>
                                    <option value="m2">M²</option>
                                </select>
                                <div class="input-group w-50">
                                    <input type="text" class="form-control" placeholder="$ 3,000,000" formControlName="propertyAmountPriceBasedM2" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyAmountPriceBasedM2.invalid && editPropertyQueueForm.controls.propertyAmountPriceBasedM2.touched}">
                                    <span class="input-group-text">M²</span>
                                </div>

                            </div>

                            <label class="form-label mt-3">Tipo de venta</label>
                            <div class="d-flex gap-2">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input type="radio" class="form-check-input" value="exclusiva" formControlName="propertySellType" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertySellType.invalid && editPropertyQueueForm.controls.propertySellType.touched}"> <span>Venta exclusiva</span>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input type="radio" class="form-check-input" value="compartida" formControlName="propertySellType" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertySellType.invalid && editPropertyQueueForm.controls.propertySellType.touched}"> <span>Venta compartida</span>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="mb-2">Condiciones para compartir</label>
                                <textarea class="form-control" rows="8" formControlName="propertySharedConditions" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertySharedConditions.invalid && editPropertyQueueForm.controls.propertySharedConditions.touched}"></textarea>
                            </div>

                        </div>
                    </div>

                </div>
            </div> <!---close box-->

            <div class="box">
                <div class="title d-flex justify-content-start">
                    <span>Amenidades</span>
                </div>
                <div class="py-4">

                    <div class="d-flex">
                        <div class="w-75 pe-5">

                            <label class="form-label mb-3">Favor de llenar las siguientes características con las que cumpla el inmueble*</label>

                            <div class="d-flex numberDataTerrain gap-3 w-100">

                                <div class="mb-3">
                                    <label class="form-label">Recámaras</label>
                                    <div>
                                        <input type="number" class="form-control" formControlName="propertyRooms" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyRooms.invalid && editPropertyQueueForm.controls.propertyRooms.touched}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Baños</label>
                                    <div>
                                        <input type="number" class="form-control" formControlName="propertyBathrooms" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyBathrooms.invalid && editPropertyQueueForm.controls.propertyBathrooms.touched}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cajones de estacionamiento</label>
                                    <div>
                                        <input type="number" class="form-control" formControlName="propertyParkings" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyParkings.invalid && editPropertyQueueForm.controls.propertyParkings.touched}">
                                    </div>
                                </div>

                            </div>

                            <div>

                                <label class="form-label">Amenidades</label>
                                <div class="d-flex gap-4 flex-wrap">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_gym">
                                        <label class="form-check-label" for="check1">Gimnasio</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_lobby">
                                        <label class="form-check-label" for="check1">Lobby</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_alberca">
                                        <label class="form-check-label" for="check1">Alberca</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_salon_de_eventos">
                                        <label class="form-check-label" for="check1">Salón de eventos</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_sport_bar">
                                        <label class="form-check-label" for="check1">Sport bar</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_asadores">
                                        <label class="form-check-label" for="check1">Asadores</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_cancha">
                                        <label class="form-check-label" for="check1">Cancha</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_ludoteca">
                                        <label class="form-check-label" for="check1">Ludoteca</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_juegos_infantiles">
                                        <label class="form-check-label" for="check1">Juegos infantiles</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_centro_de_negocios">
                                        <label class="form-check-label" for="check1">Centro de negocios</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_jacuzzi">
                                        <label class="form-check-label" for="check1">Jacuzzi</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_vigilancia">
                                        <label class="form-check-label" for="check1">Vigilancia</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_jardines">
                                        <label class="form-check-label" for="check1">Jardines</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="amenities_pista_de_atletismo">
                                        <label class="form-check-label" for="check1">Pista de atletismo</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center w-50">
                                        <input type="text" class="form-control w-100" formControlName="amenities_others" placeholder="Otras amenidades (separe por comas)" maxlength="100" [ngClass]="{borderdanger: editPropertyQueueForm.controls.amenities_others.invalid && editPropertyQueueForm.controls.amenities_others.touched}">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div> <!---close box-->

            <div class="box">
                <div class="title d-flex justify-content-start">
                    <span>Servicios</span>
                </div>
                <div class="py-4">

                    <div class="d-flex">
                        <div class="w-75 pe-5">

                            <label class="form-label mb-3">Favor de llenar las siguientes características con las que cumpla el inmueble*</label>

                            <div class="mt-3">

                                <div class="d-flex gap-4">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="services_drenaje">
                                        <label class="form-check-label" for="check1">Drenaje</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="services_agua">
                                        <label class="form-check-label" for="check1">Agua</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="services_luz">
                                        <label class="form-check-label" for="check1">Luz</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input" formControlName="services_gas">
                                        <label class="form-check-label" for="check1">Gas</label>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="title d-flex justify-content-start">
                    <span>Ubicación</span>
                </div>
                <div class="py-4">

                    <div class="d-flex">
                        <div class="w-100 d-flex flex-column">

                            <label class="form-label mb-3">Favor de llenar las siguientes características con las que cumpla el inmueble</label>

                            <div class="d-flex gap-3 w-100">
                                <!---------
                                    <div class="mb-3 flex-fill d-none">
                                        <label for="propertyType" class="form-label">Pais</label>
                                        <select class="form-select w-100" id="propertyType" name="propertyType">
                                            <option>México</option>
                                            <option>EUA</option>
                                        </select>
                                    </div>---->
                                <div class="mb-3 flex-fill d-none">
                                    <label for="propertyType" class="form-label">Estado*</label>
                                    <select class="form-select w-100" formControlName="propertyEstado">
                                        <option *ngFor="let estado of estados" value="{{estado.id}}">{{estado.nombre}}</option>
                                    </select>
                                </div>
                                <div class="mb-3 flex-fill d-flex flex-column">
                                    <label for="propertyType" class="form-label">Municipio*</label>
                                    <select class="form-select w-100" formControlName="propertyMunicipio" *ngIf="editPropertyQueueForm.get('propertyEstado').value" (change)="selectMunicipio()">
                                        <option *ngFor="let municipio of municipios" value="{{municipio.id}}">{{municipio.nombre}}</option>
                                    </select>
                                    <span *ngIf="!editPropertyQueueForm.get('propertyEstado').value">[Selecciona un estado]</span>
                                </div>
                                <div class="mb-3 flex-fill d-flex flex-column">
                                    <label for="propertyType" class="form-label">Colonia</label>
                                    <select class="form-select w-100" formControlName="propertyColonia" *ngIf="editPropertyQueueForm.get('propertyMunicipio').value" (change)="selectColonia()">
                                        <option *ngFor="let colonia of colonias" value="{{colonia.id}}">{{colonia.nombre}}</option>
                                    </select>
                                    <span *ngIf="!editPropertyQueueForm.get('propertyMunicipio').value">[Selecciona un municipio]</span>
                                </div>

                            </div>

                            <div class="d-flex gap-3 w-100">

                                <div class="mb-3 group w-100">
                                    <label for="street" class="form-label">Calle</label>
                                    <div>
                                        <input type="text" class="form-control" formControlName="propertyStreet" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyStreet.invalid && editPropertyQueueForm.controls.propertyStreet.touched}">
                                    </div>
                                </div>
                                <div class="d-flex w-100 gap-3">
                                    <div class="mb-3 group">
                                        <label for="intNumber" class="form-label">Número interior</label>
                                        <div>
                                            <input type="number" class="form-control" formControlName="propertyIntNumber" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyIntNumber.invalid && editPropertyQueueForm.controls.propertyIntNumber.touched}">
                                        </div>
                                    </div>
                                    <div class="mb-3 group">
                                        <label for="extNumber" class="form-label">Número exterior</label>
                                        <div>
                                            <input type="number" class="form-control" formControlName="propertyExtNumber" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyExtNumber.invalid && editPropertyQueueForm.controls.propertyExtNumber.touched}">
                                        </div>
                                    </div>
                                    <div class="mb-3 group">
                                        <label for="extNumber" class="form-label">Código postal</label>
                                        <div>
                                            <input type="number" class="form-control" formControlName="propertyCP" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyCP.invalid && editPropertyQueueForm.controls.propertyCP.touched}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex w-100 gap-3">

                                <div class="mb-3 group d-flex gap-3">


                                    <input type="checkbox" class="form-check-input" formControlName="propertyExactLocation" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyExactLocation.invalid && editPropertyQueueForm.controls.propertyExactLocation.touched}">
                                    <label class="form-label mt-1">Mostrar ubicación exacta</label>

                                </div>

                            </div>

                            <div class="pb-2" *ngIf="!editPropertyQueueForm.get('propertyExactLocation').value">
                                <label class="text-warning">NOTA: Si desactivas la opción de ubicación exacta, el numero exterior, codigo postal y la calle no se mostraran en la publicación final.</label>
                            </div>

                            <div class="mb-3 d-flex flex-column gap-3">

                                <label class="form-label mt-1">Coordenadas</label>

                                <label class="form-label mt-1">Latitud:</label>
                                <input type="text" class="form-control mb-3" formControlName="propertyMapLat" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyMapLat.invalid && editPropertyQueueForm.controls.propertyMapLat.touched}">

                                <label class="form-label mt-1">Longitud:</label>
                                <input type="text" class="form-control" formControlName="propertyMapLong" [ngClass]="{borderdanger: editPropertyQueueForm.controls.propertyMapLong.invalid && editPropertyQueueForm.controls.propertyMapLong.touched}">


                            </div>


                        </div>
                    </div>

                </div>

            </div>

            <div>
                <span>Imagenes:</span>
                <div class="d-flex gap-2 mt-2">
                    @for ($i = 1; $i <= $dev[0]->images; $i++)
                        <div class="d-flex flex-column align-items-center">
                            <a href="https://dashboard.vangoo.mx/storage/img/posts/properties/{{$propiedad->id}}/{{$i}}.jpg" target="_blank">
                                <img class="pe-2" src="{{asset('storage/img/posts/properties').'/'.$propiedad->id.'/'.$i.'.jpg?'}}<?php echo rand(); ?>" width="250px" height="250px">
                            </a>
                            <div class="mt-1">
                                <input class="form-control" type="number" name="orderimg[{{$i}}]" value="{{$i}}" max="{{$dev[0]->images}}" min="1" style="width:100%">
                            </div>
                        </div>
                        @endfor
                </div>

                <div class="images mb-3 mt-3">
                    <label for="image" class="form-label">Agregar más imágenes:</label>
                    <input type="file" name="images[]" id="imagen" class="form-control" accept="image/jpeg" multiple onchange="previewImage()">
                    <div id="preview"></div>
                </div>
            </div>

            <p class="text-danger text-end" *ngIf="editPropertyQueueForm.invalid">Por favor llena los datos requeridos y/o corrige los errores.</p>

            <div class="w-100 d-flex justify-content-center my-5">
                <a class="up" (click)="saveForm()">
                    <span class="btn btn-primary" data-bs-dismiss="modal">Guardar</span>
                </a>
            </div>

        </div>
        <!---------------------------------->
    </form>

</div>

@endsection()