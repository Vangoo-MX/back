@extends('layouts.adminLayout')

@section('breadcrumb','Editar desarrollo')

@section('title','Editar desarrollo')

@section('titleContent','Editar desarrollo')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center w-100">
    <div class="col-12 col-lg-4 px-5 d-flex flex-column align-items-center justify-content-center w-100">

        <h3>Editar desarrollo id:{{$dev[0]->id}}</h3>

        <div class="w-100">
            <form method="post" class="w-100" enctype="multipart/form-data" action="{{ route('epDev.edit') }}">

                @csrf
                <input type="hidden" name="id" value="{{$dev[0]->id}}">

                <div class="d-flex gap-5 w-100">
                    <div class="w-100">

                        <div class="mb-3 mt-3">
                            <label for="title" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="title" value="{{$dev[0]->title}}" placeholder="Ingresa un titulo" name="title" required>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="status" class="form-label">Estado de venta:</label>
                            <select class="form-select" name="status">
                                <option value="presale" <?php if ($dev[0]->status == 'presale') {
                                                            echo 'selected';
                                                        } ?>>Preventa</option>
                                <option value="sale" <?php if ($dev[0]->status == 'sale') {
                                                            echo 'selected';
                                                        } ?>>Venta</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price_min" class="form-label">Precio mínimo:</label>
                            <input type="number" class="form-control" step="0.01" id="price_min" value="{{$dev[0]->price_min}}" placeholder="Precio mínimo" name="price_min" required>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price_max" class="form-label">Precio máximo:</label>
                            <input type="number" class="form-control" step="0.01" id="price_max" value="{{$dev[0]->price_max}}" placeholder="Precio máximo" name="price_max" required>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control" rows="5" id="description" name="description">{{$dev[0]->description}}</textarea>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="availability" class="form-label">Disponibilidad:</label>
                            <input type="text" class="form-control" id="availability" value="{{$dev[0]->availability}}" placeholder="Fecha en que estará disponible" name="availability">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="financing" class="form-label">Financiación:</label>
                            <input type="text" class="form-control" id="financing" alue="{{$dev[0]->financing}}" placeholder="Financiado" name="financing">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="mode" class="form-label">Modo:</label>
                            <select class="form-select" name="mode">
                                <option value="vertical" <?php if ($dev[0]->mode == 'vertical') {
                                                                echo 'selected';
                                                            } ?>>Vertical</option>
                                <option value="horizontal" <?php if ($dev[0]->mode == 'horizontal') {
                                                                echo 'selected';
                                                            } ?>>Horizontal</option>
                            </select>
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
                                <option hidden>Selecciona un municipio</option>
                                @foreach($municipios as $e)
                                <option value="{{$e->id}}" data-id="{{$e->id}}" <?php if ($dev[0]->id_municipio == $e->id) {
                                                                                    echo 'selected';
                                                                                } ?>>{{$e->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="id_colonia" class="form-label">Colonia:</label>
                            <span id="coloniashtml"></span>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="street" class="form-label">Calle:</label>
                            <input type="text" class="form-control" id="street" value="{{$dev[0]->street}}" placeholder="Ingresa la calle" name="street">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="num_ext" class="form-label">Número exterior:</label>
                            <input type="number" class="form-control" id="num_ext" value="{{$dev[0]->num_ext}}" placeholder="Ingresa el número exterior" name="num_ext">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="cp" class="form-label">CP:</label>
                            <input type="number" class="form-control" id="cp" value="{{$dev[0]->cp}}" placeholder="Ingresa el código postal" name="cp">
                        </div>

                        {{-- <div class="mb-3 mt-3">
                                <label for="map" class="form-label">Mapa:</label>
                                <input type="text" class="form-control" id="map" value="{{$dev[0]->map}}" placeholder="Ingresa el link de google maps" name="map">
                    </div> --}}

                    <div class="mb-3 mt-3">
                        <label for="maplat" class="form-label">Coordenadas Longitud:</label>
                        <input type="text" class="form-control" id="maplat" name="maplat" value="{{$dev[0]->map_lat}}">
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="maplong" class="form-label">Coordenadas Latitud:</label>
                        <input type="text" class="form-control" id="maplong" name="maplong" value="{{$dev[0]->map_long}}">
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="area" class="form-label">Area:</label>
                        <input type="number" step="0.01" class="form-control" id="area" value="{{$dev[0]->area}}" placeholder="Ingresa el area del inmueble" name="area">
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="amenities" class="form-label">Amenidades:</label>
                        <input type="text" class="form-control" id="amenities" value="{{$dev[0]->amenities}}" placeholder="Separe con comas y sin espacios" name="amenities">
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="commission_percentage" class="form-label">Porcentaje de comisión de venta:</label>
                        <input type="number" class="form-control" id="commission_percentage" value="{{$dev[0]->commission_percentage}}" placeholder="Porcentaje en números sin signos" name="commission_percentage">
                    </div>

                    <input type="hidden" name="num_images" value="{{$dev[0]->images}}">

                </div>
        </div>

        <span>Imagenes:</span>
        <div class="d-flex gap-2 mt-2">
            @for ($i = 1; $i <= $dev[0]->images; $i++)
                <div class="d-flex flex-column align-items-center">
                    <a href="https://dashboard.vangoo.mx/img/posts/developments/{{$dev[0]->id}}/{{$i}}.jpg" target="_blank">
                        <img class="pe-2" src="https://dashboard.vangoo.mx/img/posts/developments/{{$dev[0]->id}}/{{$i}}.jpg?<?php echo rand(); ?>" width="90px" height="90px">
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

        <hr>

        <div class="mt-3 mb-3">
            <label class="form-label">Opciones de departamentos:</label>

            <div class="options-container">
                @foreach($app as $a)
                <div class="option-appartment" id="option-appartment-{{$loop->index+1}}">
                    <a href="" class="mt-2 d-none" style="text-decoration:none;">
                        <i class="fa-solid fa-circle-xmark text-danger mx-1"></i>
                    </a>
                    <div class="input-group mb-3 option-appartment">
                        <span class="input-group-text">#{{$loop->index+1}}</span>
                        <input type="hidden" value="{{$a->id}}" name="optionapp[{{$loop->index+1}}][id]">
                        <input type="number" step="0.01" class="form-control" placeholder="Precio" value="{{$a->price}}" name="optionapp[{{$loop->index+1}}][price]" required>
                        <input type="number" step="0.01" class="form-control" placeholder="Area" value="{{$a->area}}" name="optionapp[{{$loop->index+1}}][area]" required>
                        <input type="number" step="0.01" class="form-control" placeholder="Habitaciones" value="{{$a->rooms}}" name="optionapp[{{$loop->index+1}}][rooms]" required>
                        <input type="number" step="0.01" class="form-control" placeholder="Baños" value="{{$a->bathrooms}}" name="optionapp[{{$loop->index+1}}][bathrooms]" required>
                        <input type="number" step="0.01" class="form-control" placeholder="Estacionamientos" value="{{$a->parkings}}" name="optionapp[{{$loop->index+1}}][parkings]" required>
                        <input type="number" step="0.01" class="form-control" placeholder="Num disponibles" value="{{$a->num_available}}" name="optionapp[{{$loop->index+1}}][num_available]" required>
                    </div>
                    <div class="input-group mb-3 w-50 option-appartment">
                        <a href="https://dashboard.vangoo.mx/img/posts/developments/{{$dev[0]->id}}/plans/1.jpg" target="_blank">
                            <img class="pe-2" src="https://dashboard.vangoo.mx/img/posts/developments/{{$dev[0]->id}}/plans/{{$loop->index+1}}.jpg?<?php echo rand(); ?>" width="35px" height="35px" onerror="{this.src='{{url('./img/img404.jpg')}}'}">
                        </a>
                        <input type="file" class="form-control" name="imageoption[{{$loop->index+1}}]" accept="image/jpeg">
                    </div>
                </div>
                @endforeach
            </div>

            <!--nueva opción-->

            <span class="btn btn-secondary" id="add-option-btn">Agregar opción</span>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif

        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn btn-info btn-lg">Editar</button>
        </div>

        </form>
    </div>


</div>
</div>

<br><br><br>

<style>
    button.bg-gradient-info {
        background-color: var(--info);
        background-size: cover;
        color: white;
        border-radius: 25px;
    }

    button.bg-gradient-info:hover {
        background-color: var(--info);
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
        gap: 5px;
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
    /*
    document.getElementById('id_estado').addEventListener('change', function() {

        var estadoId = this.options[this.selectedIndex].getAttribute('data-id');
        var url = '../ep/getMunicipiosFromEstado/' + estadoId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var municipios = JSON.parse(xhr.responseText);
                var municipiosHtml = '';
                for (var i = 0; i < municipios.length; i++) {
                    municipiosHtml += '<option value="' + municipios[i].id + '" data-id="' + municipios[i].id + '">' + municipios[i].nombre + '</option>';
                }
                var selectHtml = '';
                if (estadoId != 0) {
                    selectHtml = '<select class="form-select" name="id_municipio" id="id_municipio">' + municipiosHtml + '</select>';
                }
                document.getElementById('municipioshtml').innerHTML = selectHtml;
            } else {
                console.log('Error');
            }
        };
        xhr.send();
    });
*/
    function changeMuninicio() {
        var municipioId = document.getElementById('id_municipio').options[document.getElementById('id_municipio').selectedIndex].getAttribute('data-id');
        var url = '../../ep/getColoniasFromMunicipio/' + municipioId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var colonias = JSON.parse(xhr.responseText);
                var coloniasHtml = '';
                var selected = '';
                for (var i = 0; i < colonias.length; i++) {
                    if (colonias[i].id == <?php echo $dev[0]->id_colonia; ?>) {
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



    //opciones de apartamentos
    let optionCount = <?php if ($app) {
                            echo sizeof($app);
                        } else {
                            echo '0';
                        } ?>;
    const addOptionBtn = document.getElementById('add-option-btn');
    const optionsContainer = document.querySelector('.options-container');

    addOptionBtn.addEventListener('click', function() {
        optionCount++;
        const newOption = document.createElement('div');
        newOption.classList.add('option-appartment');
        newOption.id = `option-appartment-${optionCount}`;

        const inputs = `
            <span class="input-group-text">Opción ${optionCount}</span>
            <input type="number" class="form-control" placeholder="Precio" name="option[${optionCount}][price]" required>
            <input type="number" class="form-control" placeholder="Area" name="option[${optionCount}][area]" required>
            <input type="number" class="form-control" placeholder="Habitaciones" name="option[${optionCount}][rooms]" required>
            <input type="number" class="form-control" placeholder="Baños" name="option[${optionCount}][bathrooms]" required>
            <input type="number" class="form-control" placeholder="Estacionamientos" name="option[${optionCount}][parkings]" required>
            <input type="number" class="form-control" placeholder="Num disponibles" name="option[${optionCount}][num_available]" required>
        `;

        const fileInput = `
            <span class="input-group-text">Plano</span>
            <input type="file" class="form-control" name="imageoption[${optionCount}]" accept="image/jpeg">
        `;

        newOption.innerHTML = `
            <div class="input-group mb-3">${inputs}</div>
            <div class="input-group mb-3 w-50">${fileInput}</div>
        `;

        optionsContainer.appendChild(newOption);
    });
</script>

@endsection()
