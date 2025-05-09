@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades destacadas')

@section('title','Highlights')

@section('titleContent','Propiedades destacadas')

@section('content')

<h3>Nueva propiedad destacada</h3>
<br>
<form method="post" action="{{ route('admin.addHighlightProperties') }}">
    @csrf
    <div class="d-flex gap-2">
        <select class="form-select equal-width" id="municipiosh-select" data-table="#hlTable" name="id_municipio">
            <option selected value="0" data-municipio-id="0">Todas las propiedades destacadas</option>
            @foreach($municipiosh as $e)
            <option value="{{$e->id}}" data-municipio-id="{{$e->id}}">{{$e->nombre}}</option>
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
                @foreach($propertieshl as $p)
                <tr class="municipio-{{$p->id_municipio}}">
                    <td>{{$p->id_property}}</td>
                    <td>{{property($p->id_property)[0]['title']}}</td>
                    <td>{{estado($p->id_estado)}}</td>
                    <td>{{$p->id_municipio}}</td>
                    <td>{{municipio($p->id_municipio)}}</td>
                    <td>{{$p->num_order}}</td>
                    <td class="d-flex gap-3">
                        <a href="{{ route('admin.deleteHighlightProperties', $p->id) }}" class="btn btn-danger">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </a>
                        <a href="https://vangoo.mx/details/propiedad/{{$p->id}}" target="_blank">
                            <i class="fa-solid fa-link mx-1"></i>
                        </a>
                        <form id="orden-form{{$p->id_property}}" action="{{ route('admin.orderHighlightProperties') }}" method="POST">
                            @csrf
                            <span class="d-flex gap-1">
                                <input type="hidden" name="id" value="{{$p->id_property}}">
                                <select class="form-select" name="num_order" onchange="ordenSelect({{$p->id_property}})">
                                    <option selected hidden>Orden</option>
                                    @foreach($propertieshl as $key => $q)
                                    @if($key == $p->num_order)
                                    <option value="{{$key+1}}" selected>{{$key+1}}</option>
                                    @else
                                    <option value="{{$key+1}}">{{$key+1}}</option>
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
        var url = '/ep/get-properties-by-municipio/' + municipioId;
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
