@extends('layouts.adminLayout')

@section('breadcrumb')
Lotes
<img src="{{url('./img/icon/icon-logo-mini.png')}}" />
Editar
@endsection()

@section('title','Editar lote')

@section('titleContent','Editar lote')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center w-100">
    <div class="col-12 col-lg-4 px-2 px-lg-5 d-flex flex-column align-items-center justify-content-center w-100">

        <h3>{{$lot->title}}</h3>

        <div class="w-100">
            <form method="post" class="w-100" enctype="multipart/form-data" action="{{ route('admin.updateLot') }}">

                @csrf
                <input type="hidden" name="id" value="{{$lot->id}}">

                <div class="d-flex gap-5 w-100 flex-column flex-lg-row">
                    <div class="w-100">

                        <div class="mb-3 mt-3">
                            <label for="title" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="title" placeholder="Ingresa un titulo" name="title" value="{{old('title', $lot->title)}}" required>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="type_lots" class="form-label">Tipo de terreno:</label>
                            <select class="form-select" name="type_lots">
                                <option value="residencial" <?php if ($lot->type_lots == 'residencial') {
                                                                echo 'selected';
                                                            } ?>>Residencial</option>
                                <option value="comercial" <?php if ($lot->type_lots == 'comercial') {
                                                                echo 'selected';
                                                            } ?>>Comercial</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="developers" class="form-label">Desarrolladora:</label>
                            <input type="text" class="form-control" id="developers" placeholder="Ingresa el nombre de la desarrolladora" name="developers" value="{{old('developers', $lot->developers)}}" required>
                            @error('developers')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="status" class="form-label">Estado de venta:</label>
                            <select class="form-select" name="status">
                                <option value="presale" <?php if ($lot->status == 'presale') {
                                                            echo 'selected';
                                                        } ?>>Preventa</option>
                                <option value="sale" <?php if ($lot->status == 'sale') {
                                                            echo 'selected';
                                                        } ?>>Venta</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="number_lots" class="form-label">Cantidad total de lotes:</label>
                            <input type="number" class="form-control" id="number_lots" placeholder="Ingrese el numero total de lotes disponibles" name="number_lots" value="{{old('number_lots', $lot->number_lots)}}">
                            @error('number_lots')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="lots_min" class="form-label">Lotes desde:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" id="lots_min" placeholder="Área mínima del lote" name="lots_min" value="{{ old('lots_min', $lot->lots_min) }}" required>
                                    <span class="ms-2">m²</span>
                                </div>
                                @error('lots_min')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lots_max" class="form-label">Hasta:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" id="lots_max" placeholder="Área máxima del lote" name="lots_max" value="{{ old('lots_max', $lot->lots_max) }}" required>
                                    <span class="ms-2">m²</span>
                                </div>
                                @error('lots_max')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="price_min" class="form-label">Precios de lotes desde:</label>
                                <input type="number" class="form-control" id="price_min" placeholder="Precio mínimo" name="price_min" value="{{ old('price_min', $lot->price_min) }}" required>
                                @error('price_min')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="price_max" class="form-label">Hasta:</label>
                                <input type="number" class="form-control" id="price_max" placeholder="Precio máximo" name="price_max" value="{{ old('price_max', $lot->price_max) }}" required>
                                @error('price_max')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control" rows="5" id="description" name="description">{{old('description', $lot->description)}}</textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="availability" class="form-label">Disponibilidad:</label>
                            <input type="date" class="form-control" id="availability" placeholder="Fecha en que estará disponible" name="availability" value="{{old('availability', $lot->availability)}}">
                            @error('availability')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="type_terrain" class="form-label">Tipo de terreno del lote:</label>
                                <select class="form-select" name="type_terrain">
                                    <option value="regular" <?php if ($lot->type_terrain == 'regular') {
                                                                echo 'selected';
                                                            } ?>>Regular</option>
                                    <option value="irregular" <?php if ($lot->type_terrain == 'irregular') {
                                                                    echo 'selected';
                                                                } ?>>Irregular</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="slope" class="form-label">Se encuentra sobre una pendiente?:</label>
                                <select class="form-select" name="slope">
                                    <option value="si" <?php if ($lot->slope == 'si') {
                                                            echo 'selected';
                                                        } ?>>Si</option>
                                    <option value="no" <?php if ($lot->slope == 'no') {
                                                            echo 'selected';
                                                        } ?>>No</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="w-100">

                        <!---
                            <div class="mb-3 mt-3">
                                <label for="id_estado" class="form-label">Estado:</label>
                                <select class="form-select" name="id_estado" id="id_estado">
                                    @ foreach($estados as $e)
                                        <option value="$e->id" data-id="$e->id">$e->nombre</option>
                                    @ endforeach
                                </select>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="id_municipio" class="form-label">Municipio:</label>
                                <span id="municipioshtml"></span>
                            </div>--->
                        <input type="hidden" id="id_estado" name="id_estado" value="19">

                        <div class="mb-3 mt-3">
                            <label for="id_municipio" class="form-label">Municipio:</label>
                            <select class="form-select" name="id_municipio" id="id_municipio" required>
                                <option value="" hidden selected>Selecciona un municipio</option>
                                @foreach($municipios as $e)
                                <option value="{{$e->id}}" data-id="{{$e->id}}" <?php if ($lot->id_municipio == $e->id) {
                                                                                    echo 'selected';
                                                                                } ?>>{{$e->nombre}}</option>
                                @endforeach
                                @error('id_municipio')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="id_colonia" class="form-label">Colonia:</label>
                            <span id="coloniashtml"></span>
                        </div>

                        <!-- <div class="mb-3 mt-3">
                            <label for="street" class="form-label">Calle:</label>
                            <input type="text" class="form-control" id="street" placeholder="Ingresa la calle" name="street" value="{{old('street', $lot->street)}}">
                            @error('street')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> -->

                        <div class="mb-3 mt-3">
                            <label for="num_ext" class="form-label">Número exterior:</label>
                            <input type="number" class="form-control" id="num_ext" placeholder="Ingresa el número exterior" name="num_ext" value="{{old('num_ext', $lot->num_ext)}}">
                            @error('num_ext')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="cp" class="form-label">Codigo Postal:</label>
                            <input type="number" class="form-control" id="cp" placeholder="Ingresa el código postal" name="cp" value="{{old('cp', $lot->cp)}}">
                            @error('cp')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="mb-3 mt-3">
                                <label for="map" class="form-label">Mapa:</label>
                                <input type="text" class="form-control" id="map" name="map">
                            </div> --}}

                        <div class="mb-3 mt-3">
                            <label for="map_lat" class="form-label">Coordenadas Latitud:</label>
                            <input type="text" class="form-control" id="map_lat" name="map_lat" value="{{old('map_lat', $lot->map_lat)}}">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="map_long" class="form-label">Coordenadas Longitud:</label>
                            <input type="text" class="form-control" id="map_long" name="map_long" value="{{old('map_long', $lot->map_long)}}">
                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="broad" class="form-label">Ancho:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" id="broad" placeholder="Ancho del lote" name="broad" value="{{ old('broad', $lot->broad) }}">
                                    <span class="ms-2">m</span>
                                </div>
                                @error('broad')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="largue" class="form-label">Largo:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" id="largue" placeholder="Largo del lote" name="largue" value="{{ old('largue', $lot->largue) }}">
                                    <span class="ms-2">m</span>
                                </div>
                                @error('largue')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price_mt2" class="form-label">Precio del metro cuadrado:</label>
                            <input type="number" step="0.01" class="form-control" id="price_mt2" placeholder="Ingresa el area del inmueble" name="price_mt2" value="{{old('price_mt2', $lot->price_mt2)}}">
                            @error('price_mt2')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="amenities" class="form-label">Amenidades:</label>
                            <input type="text" class="form-control" id="amenities" placeholder="Separe con comas y sin espacios" name="amenities" value="{{old('amenities', $lot->amenities)}}">
                            @error('amenities')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="financing" class="form-label">Financiación:</label>
                            <input type="text" class="form-control" id="financing" placeholder="Financiado" name="financing" value="{{old('financing', $lot->financing)}}">
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="initial_fee" class="form-label">Enganche:</label>
                                <input type="number" class="form-control" id="initial_fee" placeholder="Cuota inicial del lote" name="initial_fee" value="{{old('initial_fee', $lot->initial_fee)}}">
                                @error('initial_fee')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="commission_percentage" class="form-label">Porcentaje de comisión de venta:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" id="commission_percentage" placeholder="Porcentaje en números sin signos" name="commission_percentage" value="{{old('commission_percentage', $lot->commission_percentage)}}">
                                    <span class="ms-2">%</span>
                                </div>
                                @error('commission_percentage')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <span>Imagenes:</span>
                <div class="d-flex gap-2 mt-2">
                    @for ($i = 1; $i <= $lot->images; $i++)
                        <div class="d-flex flex-column align-items-center image-container">
                            @php
                            $imageUrl = asset('storage/img/posts/lots/' . $lot->id . '/' . $i . '.webp');
                            @endphp

                            @if($imageUrl)
                            <a href="{{ $imageUrl }}" target="_blank">
                                <img class="pe-2" src="{{ $imageUrl . '?' . uniqid() }}" width="90px" height="90px">
                            </a>
                            @endif

                            <div class="mt-1">
                                <select class="form-control reorder-select" name="orderimg[{{$i}}]" style="width:100%" required>
                                    @for($j = 1; $j <= $lot->images; $j++)
                                        <option value="{{ $j }}" {{ $j == $i ? 'selected' : '' }}>
                                            {{ $j }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                            <span class="delete-icon" onclick="confirmDelete(event, {{$i}})">❌</span>
                        </div>
                        @endfor
                </div>

                <div class="images mb-3 mt-3">
                    <label for="image" class="form-label">Agregar más imágenes:</label>
                    <input type="file" name="images[]" id="imagen" class="form-control" accept="image/jpeg" multiple onchange="previewImage()">
                    <div id="preview"></div>
                </div>

                <hr>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn bg-gradient-info btn-lg">Editar</button>
                </div>

            </form>
            <form id="delete-form" action="{{ route('admin.deleteImageLot', ['lotId' => $lot->id, 'imageId' => ':imageId']) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>


    </div>
</div>

<br><br><br>

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

<script>
    //imagenes
    const imagenInput = document.getElementById('imagen');
    const previewContainer = document.getElementById('preview');
    const previewImage = previewContainer.querySelector('.preview-image');

    imagenInput.addEventListener('change', function() {
        const files = Array.from(this.files);

        if (previewImage) {
            previewImage.remove();
        }

        files.forEach(file => {
            const reader = new FileReader();

            reader.addEventListener('load', function() {
                const image = new Image();
                image.src = this.result;

                const previewImage = document.createElement('div');
                previewImage.classList.add('preview-image');
                previewImage.appendChild(image);
                previewContainer.appendChild(previewImage);
            });

            reader.readAsDataURL(file);
        });
    });

    function confirmDelete(event, imageId) {
        event.preventDefault();
        if (confirm('¿Estás seguro de eliminar esta imagen?')) {
            var form = document.getElementById('delete-form');
            form.action = form.action.replace(':imageId', imageId);
            form.submit();
        }
    }

    function changeMuninicio() {
        var municipioId = document.getElementById('id_municipio').options[document.getElementById('id_municipio').selectedIndex].getAttribute('data-id');
        var url = '/ep/getColoniasFromMunicipio/' + municipioId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var colonias = JSON.parse(xhr.responseText);
                var coloniasHtml = '';
                var selected = '';
                for (var i = 0; i < colonias.length; i++) {
                    if (colonias[i].id == <?php echo $lot->id_colonia; ?>) {
                        selected = 'selected';
                    } else {
                        selected = '';
                    }
                    coloniasHtml += '<option value="' + colonias[i].id + '" ' + selected + '>' + colonias[i].nombre + '</option>';
                }
                var selectHtml = '';
                if (municipioId != 0) {
                    selectHtml = '<select class="form-select" name="id_colonia" id="id_colonia" required>' + coloniasHtml + '</select>';
                }
                document.getElementById('coloniashtml').innerHTML = selectHtml;
            } else {
                console.log('Error');
            }
        };
        xhr.send();
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        changeMuninicio();
    });

    document.getElementById('id_municipio').addEventListener('change', function() {
        changeMuninicio();
    });

    document.querySelectorAll('.reorder-select').forEach(select => {
        select.addEventListener('change', () => {
            const currentValue = select.value;

            document.querySelectorAll('.reorder-select').forEach(otherSelect => {
                if (otherSelect !== select && otherSelect.value === currentValue) {
                    otherSelect.value = '';
                }
            });
        });
    });
</script>

@endsection()
