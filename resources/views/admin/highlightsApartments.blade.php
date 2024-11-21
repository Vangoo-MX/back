@extends('layouts.adminLayout')

@section('breadcrumb','Apartamentos destacados')

@section('title','Highlights')

@section('titleContent','Apartamentos destacados')

@section('content')

<h3>Nuevo apartamento destacado</h3>
<br>
<form method="post" action="{{ route('HighlightApartment.add') }}">
    @csrf
    <div class="d-flex gap-2">
        <select class="form-select equal-width" id="municipiosh-select" data-table="#hlTable" name="id_municipio">
            <option selected value="0" data-municipio-id="0">Todos los apartamentos destacados</option>
            @foreach($municipiosh as $municipio)
            <option value="{{$municipio->id}}" data-municipio-id="{{$municipio->id}}">{{$municipio->nombre}}</option>
            @endforeach
        </select>

        <div id="properties-by-municipio" class="d-flex gap-2 larger-width"></div>
    </div>
</form>


<br>

<!-- Content Row -->
<div class="row">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="hlTable">
            <thead>
                <tr>
                    <th>Propiedad id</th>
                    <th>Titulo</th>
                    <th>Estado</th>
                    <th>Id Municipio</th>
                    <th>Municipio</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apartmentshl as $apartment)
                <tr class="municipio-{{$apartment->id_municipio}}">
                    <td>{{$apartment->id_property}}</td>
                    <td>{{$apartment->id_property->title}}</td>
                    <td>{{estado($apartment->id_estado)}}</td>
                    <td>{{$apartment->id_municipio}}</td>
                    <td>{{municipio($apartment->id_municipio)}}</td>
                    <td>{{$apartment->num_order}}</td>
                    <td class="d-flex gap-3">
                        <a href="{{ route('HighlightApartment.delete', $apartment->id) }}" class="btn btn-danger">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </a>
                        <a href="https://www.vangoo.mx/detailsDepa/apartments/{{$apartment->id}}" target="_blank">
                            <i class="fa-solid fa-link mx-1"></i>
                        </a>
                        <form id="orden-form{{$apartment->id_property}}" action="{{ route('HighlightApartment.order') }}" method="POST">
                            @csrf
                            <span class="d-flex gap-1">
                                <input type="hidden" name="id" value="{{$apartment->id_property}}">
                                <select class="form-select" name="num_order" onchange="ordenSelect({{$apartment->id_property}})">
                                    <option selected hidden>Orden</option>
                                    @foreach($apartmentshl as $key => $q)
                                    @if($key == $apartment->num_order)
                                    <option value="{{$key}}" selected>{{$key}}</option>
                                    @else
                                    <option value="{{$key}}">{{$key}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </span>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<br><br>

<script>
    $(document).ready(function() {
        $('#hlTable').DataTable({
            language: {
                processing: "Procesando..",
                search: "Buscar:&nbsp;",
                lengthMenu: "Ver _MENU_ Elementos",
                info: "Mostrando de _START_ a _END_ de _TOTAL_ Elementos",
                infoFiltered: "(filtrando de _MAX_ elementos en total)",
                infoPostFix: "",
                loadingRecords: "Cargando registros...",
                zeroRecords: "No hay registros",
                emptyTable: "No hay datos para mostrar",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });

        $('document').on('change', '#municipiosh-select', function() {
            var table = $($(this).data('table')).DataTable();
            var municipioId = $(this).val();
            if (municipioId == 0 || municipioId == "") {
                table.column(3).search("").draw();
            } else {
                table.column(3).search(municipioId).draw();
            }
        });

    });

    function ordenSelect(id) {
        console.log(id);
        $('#orden-form' + id).submit();
    }

    document.getElementById('municipiosh-select').addEventListener('change', function() {
        var municipioId = this.options[this.selectedIndex].getAttribute('data-municipio-id');
        var url = '../ep/get-apartment-by-municipio/' + municipioId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var properties = JSON.parse(xhr.responseText);
                var propertiesHtml = '';
                for (var i = 0; i < properties.length; i++) {
                    propertiesHtml += '<option value="' + properties[i].id + '">' + properties[i].id + ' - ' + properties[i].title + '</option>';
                }
                var selectHtml = '';
                if (municipioId != 0) {
                    selectHtml = '<select class="form-select larger-width" name="id_property">' + propertiesHtml + '</select><button class="btn btn-primary" type="submit">Asignar</button>';
                }
                document.getElementById('properties-by-municipio').innerHTML = selectHtml;
            } else {
                console.log('Error');
            }
        };
        xhr.send();
    });
</script>

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        color: inherit !important;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 50px;
        background-color: transparent;
        background: transparent;
    }

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

    a {
        text-decoration: none;
    }

    .equal-width {
        width: 100%;
        max-width: 300px;
    }

    .larger-width {
        width: 100%;
        max-width: 600px;
    }
</style>

@endsection()
