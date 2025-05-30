@extends('layouts.adminLayout')

@section('breadcrumb')
Propiedades
<img src="{{url('./img/icon/icon-logo-mini.png')}}" />
Detalle
<img src="{{url('./img/icon/icon-logo-mini.png')}}" />
Editar propiedad
@endsection()

@section('title')
{{$estate->title}}
@endsection()

@section('titleContent','Detalles de propiedad')

@section('content')

<!-- Content Row -->


<?php if ($estate->status == 0) { ?>
    <div class="alert alert-danger">
        Esta propiedad está deshabilitada
    </div>
<?php } ?>

<div class="row d-flex justify-content-center w-100">
    <div class="col-12 col-lg-4 px-2 px-lg-5 d-flex flex-column align-items-center justify-content-center w-100">
        <div class="w-100">

            <form method="post" class="w-100" action="{{route('properties.update', $estate)}}" enctype="multipart/form-data">

                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$estate->id}}">

                <div class="d-flex gap-5 w-100 flex-column flex-lg-row">
                    <div class="w-100">

                        <div class="mb-3 mt-3">
                            <label for="title" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="title" value="{{old('title', $estate->title)}}" placeholder="Ingresa un titulo" name="title">
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="operation_type" class="form-label">Tipo de operación:</label>
                            <select class="form-select" name="operation_type">
                                <option value="venta" <?php if ($estate->operation_type == 'venta') {
                                                            echo 'selected';
                                                        } ?>>Venta</option>
                                <option value="renta" <?php if ($estate->operation_type == 'renta') {
                                                            echo 'selected';
                                                        } ?>>Renta</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price" class="form-label">Precio:</label>
                            <input type="number" class="form-control" step="0.01" id="price" value="{{old('price', $estate->price)}}" placeholder="Precio de venta/renta" name="price">
                            @error('price')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="price_maintenance" class="form-label">Precio de mantenimiento:</label>
                            <input type="number" class="form-control" id="price_maintenance" value="{{old('price_maintenance', $estate->price_maintenance)}}" placeholder="Precio de mantenimiento" name="price_maintenance">
                            @error('price_maintenance')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control" rows="5" id="description" name="description">{{old('description', $estate->description)}}</textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <div class="d-flex gap-5">
                                <div>
                                    <label for="rooms" class="form-label">Cuartos:</label>
                                    <input type="number" class="form-control" step="1" id="rooms" value="{{old('rooms', $estate->rooms)}}" placeholder="Cuartos" name="rooms">
                                    @error('rooms')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @php
                                $bathroomsFormatted = (intval($estate->bathrooms) == $estate->bathrooms) ? intval($estate->bathrooms) : $estate->bathrooms;
                                @endphp
                                <div>
                                    <label for="bathrooms" class="form-label">Baños:</label>
                                    <input type="number" class="form-control" step="0.01" id="bathrooms" value="{{old('bathrooms', $bathroomsFormatted)}}" placeholder="Cuartos" name="bathrooms">
                                    @error('bathrooms')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="parkings" class="form-label">Lugares de estacionamiento:</label>
                            <input type="number" class="form-control" step="1" id="parkings" value="{{old('parkings', $estate->parkings)}}" placeholder="Lugares de estacionamiento" name="parkings">
                            @error('parkings')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="map" class="form-label">Mapa:</label>
                            <input type="text" class="form-control" id="map" value="{{old('map', $estate->map)}}" placeholder="Ingresa el link de google maps" name="map">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="area" class="form-label">Area:</label>
                            <input type="number" step="0.01" class="form-control" id="area" value="{{old('area', $estate->area)}}" placeholder="Ingresa el area del inmueble" name="area">
                            @error('area')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="w-100">
                        <input type="hidden" id="id_estado" name="id_estado" value="19">
                        <div class="mb-3 mt-3">
                            <label for="id_municipio" class="form-label">Municipio:</label>
                            <select class="form-select" name="id_municipio" id="id_municipio">
                                <option hidden>Selecciona un municipio</option>
                                @foreach($municipios as $municipio)
                                <option value="{{$municipio->id}}" data-id="{{$municipio->id}}" <?php if ($estate->id_municipio == $municipio->id) {
                                                                                                    echo 'selected';
                                                                                                } ?>>{{$municipio->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="id_colonia" class="form-label">Colonia:</label>
                            <span id="coloniashtml"></span>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="street" class="form-label">Calle:</label>
                            <input type="text" class="form-control" id="street" value="{{old('street' ,$estate->street)}}" placeholder="Ingresa la calle" name="street">
                            @error('street')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">

                            <div class="d-flex gap-5">
                                <div>
                                    <label for="num_ext" class="form-label">Número exterior:</label>
                                    <input type="number" class="form-control" id="num_ext" value="{{old('num_ext', $estate->num_ext)}}" placeholder="Ingresa el número exterior" name="num_ext">
                                    @error('num_ext')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="num_int" class="form-label">Número interior:</label>
                                    <input type="number" class="form-control" id="num_int" value="{{old('num_int', $estate->num_int)}}" placeholder="Ingresa el número interior" name="num_int">
                                    @error('num_int')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="mb-3 mt-3">
                            <label for="map_lat" class="form-label">Coordenadas Latitud:</label>
                            <input type="text" class="form-control" id="map_lat" name="map_lat" value="{{old('map_lat', $estate->map_lat)}}">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="map_long" class="form-label">Coordenadas Longitud:</label>
                            <input type="text" class="form-control" id="map_long" name="map_long" value="{{old('map_long', $estate->map_long)}}">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="cp" class="form-label">Codigo postal:</label>
                            <input type="number" class="form-control" id="cp" value="{{old('cp', $estate->cp)}}" placeholder="Ingresa el código postal" name="cp" disabled>
                            @error('cp')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-3 mt-3">
                            <label for="amenities" class="form-label">Amenidades:</label>
                            <input type="text" class="form-control" id="amenities" value="{{old('amenities', $estate->amenities)}}" placeholder="Separe con comas y sin espacios" name="amenities">
                            @error('amenities')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="sell_type" class="form-label">Tipo de venta:</label>
                            <input type="text" class="form-control" id="sell_type" value="{{old('sell_type', $estate->sell_type)}}" placeholder="Tipo de venta" name="sell_type">
                            @error('sell_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="share-conditions">Condiciones para compartir:</label>
                            <textarea class="form-control" rows="5" id="share_conditions" name="share_conditions">{{old('share_conditions', $estate->share_conditions)}}</textarea>
                            @error('share_conditions')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <?php if ($estate->price_m2) { ?>
                            <div class="mb-3 mt-3">
                                <label for="price_m2" class="form-label">Precio basado en m2:</label>
                                <input type="text" class="form-control" id="price_m2" value="{{old('price_m2', $estate->price_m2)}}" placeholder="Precio basado en m2" name="price_m2">
                                @error('price_m2')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        <?php } ?>

                        <div class="mb-3 mt-3">
                            <label for="antiquity" class="form-label">Antiguedad:</label>
                            <input type="text" class="form-control" id="antiquity" value="{{old('antiquity', $estate->antiquity)}}" placeholder="Antiguedad del inmueble" name="antiquity">
                            @error('antiquity')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="no_exact_location">Mostrar locación exacta:</label>
                            <?php if ($estate->no_exact_location == 1) {
                                echo "si";
                            } else {
                                echo "no";
                            } ?>
                        </div>
                    </div>
                </div>

                <span>Imagenes:</span>
                <div class="d-flex gap-2 mt-2 flex-wrap" id="imageGallery">
                    @foreach($estate->images as $filename)
                    @php
                    $imageUrl = asset('storage/img/posts/properties/' . $estate->id . '/' . $filename);
                    @endphp
                    <div class="draggable-item" draggable="true" data-filename="{{ basename($filename) }}">
                        <div class="d-flex flex-column align-items-center image-container position-relative">
                            <a href="{{ $imageUrl }}" target="_blank">
                                <img src="{{ $imageUrl . '?' . uniqid() }}"
                                    width="150px"
                                    height="150px"
                                    class="pe-2 drag-image">
                            </a>

                            <span class="delete-icon position-absolute top-0 end-0"
                                onclick="confirmDelete(event, '{{ $filename }}')">❌</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="images mb-3 mt-3">
                    <label for="image" class="form-label">Agregar más imágenes:</label>
                    <input type="file" name="images[]" id="imagen" class="form-control" accept="image/jpeg" multiple onchange="previewImage()">
                    <div id="preview"></div>
                </div>
                <br><br>
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn1">Editar propiedad</button>
                </div>
            </form>
            @if(session('error'))
            <script>
                alert("{{ session('error') }}");
            </script>
            @endif
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

    #imageGallery {
        flex-wrap: nowrap !important;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .draggable-item {
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .draggable-item.dragging {
        opacity: 0.5;
        transform: scale(0.9);
    }

    .drag-over {
        border: 2px dashed #007bff;
        background: rgba(0, 123, 255, 0.1);
    }

    .removing {
        transform: scale(0);
        opacity: 0;
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    function confirmDelete(event, filename) {
        event.preventDefault();
        if (!confirm('¿Estás seguro de eliminar esta imagen?')) return;

        const imageContainer = event.target.closest('.draggable-item');

        imageContainer.classList.add('removing');

        fetch("{{ route('properties.deleteImage', ['propertyId' => $estate->id, 'filename' => ':filename']) }}"
                .replace(':filename', filename), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    imageContainer.classList.remove('removing');
                    alert(data.error || 'Error al eliminar');
                }
            })
            .catch(error => {
                imageContainer.classList.remove('removing');
                console.error('Error:', error);
                alert('Error de conexión');
            });
    }

    function changeMuninicio() {
        var municipioId = document.getElementById('id_municipio').options[document.getElementById('id_municipio').selectedIndex].getAttribute('data-id');
        var url = '/api/info/colonias/municipio/' + municipioId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var colonias = JSON.parse(xhr.responseText);
                var coloniasHtml = '';
                var hasSelected = false;
                for (var i = 0; i < colonias.length; i++) {
                    var selected = '';
                    if (colonias[i].id == <?php echo $estate->id_colonia; ?>) {
                        selected = 'selected';
                        hasSelected = true;
                    }
                    coloniasHtml += '<option value="' + colonias[i].id + '" ' + selected +
                        ' data-codigo-postal="' + colonias[i].codigo_postal + '">' +
                        colonias[i].nombre + '</option>';
                }
                if (!hasSelected && colonias.length > 0) {
                    coloniasHtml = '<option value="" selected disabled>Seleccionar colonia</option>' + coloniasHtml;
                }
                if (colonias.length === 0) {
                    coloniasHtml = '<option value="" disabled>No hay colonias disponibles</option>';
                }
                var selectHtml = '';
                if (municipioId != 0) {
                    selectHtml = '<select class="form-select" name="id_colonia" id="id_colonia" required>' + coloniasHtml + '</select>';
                }
                document.getElementById('coloniashtml').innerHTML = selectHtml;

                var coloniaSelect = document.getElementById('id_colonia');
                if (coloniaSelect) {
                    coloniaSelect.addEventListener('change', function() {
                        var codigoPostal = this.options[this.selectedIndex].getAttribute('data-codigo-postal');
                        document.getElementById('cp').value = codigoPostal;
                    });

                    var initialCp = coloniaSelect.options[coloniaSelect.selectedIndex].getAttribute('data-codigo-postal');
                    document.getElementById('cp').value = initialCp;
                }
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

    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('imageGallery');
        let draggedItem = null;

        document.querySelectorAll('.draggable-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                draggedItem = item;
                item.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', () => {
                draggedItem.classList.remove('dragging');
                draggedItem = null;
                updateImageOrder();
            });
        });

        container.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientX);

            if (!afterElement) {
                container.appendChild(draggedItem);
            } else {
                container.insertBefore(draggedItem, afterElement);
            }
        });

        function getDragAfterElement(container, x) {
            const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = x - box.left - box.width / 2;

                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }

        function updateImageOrder() {
            const newOrder = Array.from(container.querySelectorAll('.draggable-item'))
                .map(item => item.dataset.filename);

            console.log('Sending new order:', newOrder);

            fetch("{{ route('properties.reorder-images', $estate->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        new_order: newOrder
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Error updating order');
                    }
                }).then(data => {
                    if (data.success) {
                        console.log('Order updated successfully');
                    } else {
                        console.error('Server error:', data.error);
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error: ' + (error.error || error.message));
                });
        }
    });
</script>

@endsection()
