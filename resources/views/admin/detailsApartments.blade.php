@extends('layouts.adminLayout')

@section('breadcrumb')
Propiedades
<img src="{{url('./img/icon/icon-logo-mini.png')}}" />
Detalle
@endsection()

@section('title')
{{$apartment->title}}
@endsection()

@section('titleContent','Detalles del apartamento')

@section('content')

<!-- Content Row -->

<div class="d-flex justify-content-end gap-2 mb-2">
    <a href="{{route('admin.editApartmentPage', $apartment->id)}}"><button class="btn1">Editar apartamento</button></a>
    <a href="{{route('admin.apartments')}}"><button class="btn2">volver</button></a>
</div>

<?php if ($apartment->status == 0) { ?>
    <div class="alert alert-danger">
        Esta propiedad está deshabilitada
    </div>
<?php } ?>

<div class="row d-flex justify-content-center w-100">
    <div class="col-12 col-lg-4 px-2 px-lg-5 d-flex flex-column align-items-center justify-content-center w-100">
        <div class="w-100">
            <form method="post" class="w-100" enctype="multipart/form-data">

                @csrf
                <input type="hidden" name="id" value="{{$apartment->id}}">

                <div class="d-flex gap-5 w-100 flex-column flex-lg-row">

                    <div class="w-100">

                        <div class="mb-3 mt-3">
                            <label for="title" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="title" value="{{$apartment->title}}" placeholder="Ingresa un titulo" name="title" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="operation_type" class="form-label">Tipo de operación:</label>
                            <select class="form-select" name="operation_type" disabled>
                                <option value="venta" <?php if ($apartment->operation_type == 'venta') {
                                                            echo 'selected';
                                                        } ?>>Venta</option>
                                <option value="renta" <?php if ($apartment->operation_type == 'renta') {
                                                            echo 'selected';
                                                        } ?>>Renta</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="type" class="form-label">Tipo:</label>
                            <select class="form-select" name="type" disabled>
                                <option value="casa" <?php if ($apartment->type == 'casa') {
                                                            echo 'selected';
                                                        } ?>>casa</option>
                                <option value="departamento" <?php if ($apartment->type == 'departamento') {
                                                                    echo 'selected';
                                                                } ?>>departamento</option>
                                <option value="terreno" <?php if ($apartment->type == 'terreno') {
                                                            echo 'selected';
                                                        } ?>>terreno</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price" class="form-label">Precio:</label>
                            <input type="number" class="form-control" step="0.01" id="price" value="{{$apartment->price}}" placeholder="Precio de venta/renta" name="price" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price_maintenance" class="form-label">Precio de mantenimiento:</label>
                            <input type="number" class="form-control" id="price_maintenance" value="{{$apartment->price_maintenance}}" placeholder="Precio de mantenimiento" name="price_maintenance" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control" rows="5" id="description" name="description" disabled>{{$apartment->description}}</textarea>
                        </div>

                        <div class="mb-3 mt-3">
                            <div class="d-flex gap-5">
                                <div>
                                    <label for="rooms" class="form-label">Cuartos:</label>
                                    <input type="number" class="form-control" step="1" id="rooms" value="{{$apartment->rooms}}" placeholder="Cuartos" name="rooms" disabled>
                                </div>
                                <div>
                                    <label for="bathrooms" class="form-label">Baños:</label>
                                    <input type="number" class="form-control" step="1" id="bathrooms" value="{{$apartment->bathrooms}}" placeholder="Cuartos" name="bathrooms" disabled>
                                </div>
                            </div>
                        </div>

                        <?php if ($apartment->type == "departamento") { ?>
                            <div class="mb-3 mt-3">
                                <label for="floor" class="form-label">Piso en el que se encuentra:</label>
                                <input type="number" class="form-control" id="floor" value="{{$apartment->floor}}" placeholder="Piso en el que se encuentra" name="floor" disabled>
                            </div>
                        <?php } ?>

                        <div class="mb-3 mt-3">
                            <label for="parkings" class="form-label">Lugares de estacionamiento:</label>
                            <input type="number" class="form-control" step="1" id="parkings" value="{{$apartment->parkings}}" placeholder="Lugares de estacionamiento" name="parkings" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="map" class="form-label">Mapa:</label>
                            <input type="text" class="form-control" id="map" value="{{$apartment->map}}" placeholder="Ingresa el link de google maps" name="map" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="area" class="form-label">Area:</label>
                            <input type="number" step="0.01" class="form-control" id="area" value="{{$apartment->area}}" placeholder="Ingresa el area del inmueble" name="area" disabled>
                        </div>

                        <?php if ($apartment->type == "terreno") { ?>
                            <div class="mb-3 mt-3">
                                <label for="area" class="form-label">Area del terreno:</label>
                                <input type="number" class="form-control" id="area_terrain" value="{{$apartment->area_terrain}}" placeholder="Ingresa el area del terreno" name="area_terrain" disabled>
                            </div>
                        <?php } ?>

                        <?php if ($apartment->type == "departamento") { ?>
                            <div class="mb-3 mt-3">
                                <label for="dev_type" class="form-label">Tipo de desarrollo:</label>
                                <input type="number" class="form-control" id="dev_type" value="{{$apartment->dev_type}}" placeholder="tipo de desarrollo en el que se encuentra" name="dev_type" disabled>
                            </div>
                        <?php } ?>

                    </div>


                    <div class="w-100">

                        <input type="hidden" id="id_estado" name="id_estado" value="19">

                        <div class="mb-3 mt-3">
                            <label for="id_municipio" class="form-label">Municipio:</label>
                            <select class="form-select" name="id_municipio" id="id_municipio" disabled>
                                <option hidden>Selecciona un municipio</option>
                                @foreach($municipios as $e)
                                <option value="{{$e->id}}" data-id="{{$e->id}}" <?php if ($apartment->id_municipio == $e->id) {
                                                                                    echo 'selected';
                                                                                } ?>>{{$e->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="id_colonia" class="form-label">Colonia:</label>
                            <input type="text" class="form-control" id="id_colonia" value="{{colonia($apartment->id_colonia)}}" name="id_colonia" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="street" class="form-label">Calle:</label>
                            <input type="text" class="form-control" id="street" value="{{$apartment->street}}" placeholder="Ingresa la calle" name="street" disabled>
                        </div>

                        <div class="mb-3 mt-3">

                            <div class="d-flex gap-5">
                                <div>
                                    <label for="num_ext" class="form-label">Número exterior:</label>
                                    <input type="number" class="form-control" id="num_ext" value="{{$apartment->num_ext}}" placeholder="Ingresa el número exterior" name="num_ext" disabled>
                                </div>
                                <div>
                                    <label for="num_int" class="form-label">Número interior:</label>
                                    <input type="number" class="form-control" id="num_int" value="{{$apartment->num_int}}" placeholder="Ingresa el número interior" name="num_int" disabled>
                                </div>
                            </div>

                        </div>

                        <div class="mb-3 mt-3">
                            <label for="cp" class="form-label">CP:</label>
                            <input type="number" class="form-control" id="cp" value="{{$apartment->cp}}" placeholder="Ingresa el código postal" name="cp" disabled>
                        </div>


                        <div class="mb-3 mt-3">
                            <label for="amenities" class="form-label">Amenidades:</label>
                            <input type="text" class="form-control" id="amenities" value="{{$apartment->amenities}}" placeholder="Separe con comas y sin espacios" name="amenities" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="services" class="form-label">Servicios:</label>
                            <input type="text" class="form-control" id="services" value="{{$apartment->services}}" placeholder="Separe con comas y sin espacios" name="services" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="sell_type" class="form-label">Tipo de venta:</label>
                            <input type="text" class="form-control" id="sell_type" value="{{$apartment->sell_type}}" placeholder="Tipo de venta" name="sell_type" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="share-conditions">Condiciones para compartir:</label>
                            <textarea class="form-control" rows="5" id="share_conditions" name="share_conditions" disabled>{{$apartment->share_conditions}}</textarea>
                        </div>

                        <?php if ($apartment->price_m2) { ?>
                            <div class="mb-3 mt-3">
                                <label for="price_m2" class="form-label">Precio basado en m2:</label>
                                <input type="text" class="form-control" id="price_m2" value="{{$apartment->price_m2}}" placeholder="Precio basado en m2" name="price_m2" disabled>
                            </div>
                        <?php } ?>

                        <div class="mb-3 mt-3">
                            <label for="antiquity" class="form-label">Antiguedad:</label>
                            <input type="text" class="form-control" id="antiquity" value="{{$apartment->antiquity}}" placeholder="Antiguedad del inmueble" name="antiquity" disabled>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="no_exact_location">Mostrar locación exacta:</label>
                            <?php if ($apartment->no_exact_location == 1) {
                                echo "si";
                            } else {
                                echo "no";
                            } ?>
                        </div>
                    </div>
                </div>

                <span>Imagenes:</span>
                <div class="d-flex gap-2 mt-2">
                    @for ($i = 1; $i <= $apartment->images; $i++)
                        <div class="d-flex flex-column align-items-center">
                            @php
                            $jpgExists = file_exists(public_path('storage/img/posts/apartments/' . $apartment->id . '/' . $i . '.jpg'));
                            $jpegExists = file_exists(public_path('storage/img/posts/apartments/' . $apartment->id . '/' . $i . '.jpeg'));
                            $imageUrl = $jpgExists ? asset('storage/img/posts/apartments/' . $apartment->id . '/' . $i . '.jpg') : ($jpegExists ? asset('storage/img/posts/apartments/' . $apartment->id . '/' . $i . '.jpeg') : null);
                            @endphp

                            @if($imageUrl)
                            <a href="{{ $imageUrl }}" target="_blank">
                                <img class="pe-2" src="{{ $imageUrl . '?' . uniqid() }}" width="90px" height="90px">
                            </a>
                            @endif

                        </div>
                        @endfor
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    button.bg-gradient-info {
        background-color: #2E93EF;
        background-size: cover;
        color: white;
        border-radius: 25px;
    }

    button.bg-gradient-info:hover {
        background-color: #2E93EF;
        background-size: cover;
        opacity: 0.7;
        color: white;
    }

    #preview {
        display: flex;
        gap: 10px;
        padding: 10px;
    }

    #preview img {
        max-width: 100%;
        max-height: 200px;
    }

    .option-appartment {
        display: flex;
        gap: 10px;
    }

    .app-file {
        width: 25%;
    }

    .image-container {
        position: relative;
        display: inline-block;
    }

    .delete-icon {
        position: absolute;
        top: 0px;
        right: 0px;
        color: red;
        cursor: pointer;
        z-index: 1;
        margin-top: 3px;
        margin-right: 10px;
    }

    .delete-option-btn-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
    }

    .delete-icon-option {
        color: red;
        cursor: pointer;
        z-index: 1;
        margin-bottom: 5px
    }

    .input-group-icon {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        width: 1%;
    }

    /*-----------RESPONSIVE--------------*/
    @media only screen and (max-width: 600px) {
        .option-appartment {
            flex-direction: column;
        }

        .delete-option-btn-container {
            justify-content: center;
        }

        .app-file,
        .app-input {
            width: 100% !important;
        }
    }
</style>

@endsection()