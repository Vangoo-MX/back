@extends('layouts.adminLayout')

@section('breadcrumb','Desarrollos destacadas')

@section('title','Highlights')

@section('titleContent','Desarrollos destacadas')

@section('content')

<h3>Nuevo desarrollo destacado</h3>
<br>
<form method="post" action="{{ route('verticals.highlights.store') }}">
    @csrf
    <div class="d-flex gap-2">
        <select class="form-select equal-width" id="municipiosh-select" data-table="#hlTable" name="id_municipio">
            <option selected value="0" data-municipio-id="0">Todas los desarrollos destacados</option>
            @foreach($municipios as $municipio)
            <option value="{{$municipio->id}}" data-municipio-id="{{$municipio->id}}">{{$municipio->nombre}}</option>
            @endforeach
        </select>

        <div id="devs-by-municipio" class="d-flex gap-2 larger-width"></div>
    </div>
</form>


<br>

<!-- Content Row -->
<div class="row">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="hlTable">
            <thead>
                <tr>
                    <th>Id</th>
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
                    <td>{{$estate->id_development}}</td>
                    <td>{{development($estate->id_development)[0]['title']}}</td>
                    <td>{{estado($estate->id_estado)}}</td>
                    <td>{{$estate->id_municipio}}</td>
                    <td>{{municipio($estate->id_municipio)}}</td>
                    <td>{{$estate->num_order}}</td>
                    <td class="d-flex gap-3">
                        <form action="{{ route('verticals.highlights.destroy', $estate->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </form>
                        <a href="https://vangoo.mx/details/desarrollo/{{$estate->id}}" target="_blank">
                            <i class="fa-solid fa-link mx-1"></i>
                        </a>
                        <form id="orden-form{{$estate->id_property}}" action="{{ route('verticals.highlights.update', $estate->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <span class="d-flex gap-1">
                                <input type="hidden" name="id" value="{{$estate->id_development}}">
                                <select class="form-select" name="num_order" onchange="ordenSelect({{$estate->id_development}})">
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
        var url = '/developments/vertical/municipio/' + municipioId;

        fetch(url)
            .then(response => response.json())
            .then(properties => {
                let html = '<select class="form-select larger-width" name="id_development" required>';
                properties.forEach(property => {
                    html += `<option value="${property.id}">${property.id} - ${property.title}</option>`;
                });
                html += '</select>';

                html += '<button type="submit" class="btn btn-primary ms-2">Asignar</button>';

                document.getElementById('devs-by-municipio').innerHTML = html;
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
</style>

@endsection()
