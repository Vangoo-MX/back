@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades destacadas')

@section('title','Highlights')

@section('titleContent','Propiedades destacadas')

@section('content')

<h3>Nueva propiedad destacada</h3>
<br>
<div class="filters-container">
    <div class="filters-grid">
        <div class="form-group">
            <form method="post" action="{{ route('properties.highlights.store') }}">
                @csrf
                <label for="municipio">Municipio</label>
                <select id="municipiosh-select" data-table="#hlTable" name="id_municipio">
                    <option selected value="0" data-municipio-id="0">Todas las propiedades destacadas</option>
                    @foreach($municipios as $municipio)
                    <option value="{{$municipio->id}}" data-municipio-id="{{$municipio->id}}">{{$municipio->nombre}}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>
<form method="post" action="{{ route('properties.highlights.store') }}">
    @csrf
    <div class="d-flex gap-2">
        <select class="form-select equal-width" id="municipiosh-select" data-table="#hlTable" name="id_municipio">
            <option selected value="0" data-municipio-id="0">Todas las propiedades destacadas</option>
            @foreach($municipios as $municipio)
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
                @foreach($estates as $estate)
                <tr class="municipio-{{$estate->id_municipio}}">
                    <td>{{$estate->id_property}}</td>
                    <td>{{property($estate->id_property)[0]['title']}}</td>
                    <td>{{estado($estate->id_estado)}}</td>
                    <td>{{$estate->id_municipio}}</td>
                    <td>{{municipio($estate->id_municipio)}}</td>
                    <td>{{$estate->num_order}}</td>
                    <td class="d-flex gap-3">
                        <form action="{{ route('properties.highlights.destroy', $estate->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </form>
                        <a href="https://vangoo.mx/details/propiedad/{{$estate->id}}" target="_blank">
                            <i class="fa-solid fa-link mx-1"></i>
                        </a>
                        <form id="orden-form{{$estate->id_property}}" action="{{ route('properties.highlights.update', $estate->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <span class="d-flex gap-1">
                                <input type="hidden" name="id" value="{{$estate->id_property}}">
                                <select class="form-select" name="num_order" onchange="ordenSelect({{$estate->id_property}})">
                                    <option value="" selected hidden>Orden</option>
                                    @for($i = 1; $i <= count($estates); $i++)
                                        <option value="{{ $i }}" {{ $estate->num_order == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                        </option>
                                        @endfor
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
        var municipioId = this.value;
        var url = '/properties/municipio/' + municipioId;

        fetch(url)
            .then(response => response.json())
            .then(properties => {
                let html = '<select class="form-select larger-width" name="id_property" required>';
                properties.forEach(property => {
                    html += `<option value="${property.id}">${property.id} - ${property.title}</option>`;
                });
                html += '</select>';

                html += '<button type="submit" class="btn btn-primary ms-2">Asignar</button>';

                document.getElementById('properties-by-municipio').innerHTML = html;
                document.getElementById('submit-btn').style.display = 'inline-block';
            })
            .catch(error => console.error('Error:', error));
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

    /* Nuevo */
    .filters-container {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .filters-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        color: #2d3748;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 0.4rem;
        color: #4a5568;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background-color: #f9fafb;
        font-size: 0.9rem;
        transition: border 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #3b82f6;
        outline: none;
        background-color: #fff;
    }

    .table-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
        margin-top: 2rem;
    }

    .table-container,
    table,
    tbody,
    tr,
    td {
        overflow: visible !important;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .custom-table thead {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
        color: #374151 !important;
        text-align: left;
    }

    .custom-table thead th {
        padding: 12px 16px !important;
        font-weight: 600 !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 14px !important;
        vertical-align: middle !important;
        border: none !important;
        background-color: #ffffff !important;
        color: #374151 !important;
    }

    .custom-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
        font-size: 13.5px;
    }

    .custom-table tbody tr:hover {
        background-color: #f2f2f2;
        transition: background-color 0.2s ease-in-out;
        cursor: pointer;
    }

    .badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 6px;
        display: inline-block;
        font-weight: bold;
    }

    .badge.active {
        background-color: #daf5dc;
        color: #2e7d32;
    }

    .badge.inactive {
        background-color: #fbdada;
        color: #c62828;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        background: none;
        border: none;
        font-size: 13.5px;
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 170px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
        border-radius: 8px;
        overflow: hidden;
        padding: 5px 0;
    }

    .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        background: none;
        width: 100%;
        border: none;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f2f2f2;
    }

    .dropdown-menu .dropdown-item.text-danger:hover {
        background-color: #ffe6e6;
        color: #d32f2f;
    }

    .dropdown-menu .dropdown-item i {
        margin-right: 8px;
        min-width: 16px;
        text-align: center;
    }
</style>

@endsection()
