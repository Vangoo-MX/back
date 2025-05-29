@extends('layouts.adminLayout')

@section('breadcrumb','Desarrollos')

@section('title','Desarrollos')

@section('titleContent','Desarrollos')

@section('content')

<!-- Content Row -->
<div class="row">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="propertiesTable">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Titulo</th>
                    <th>Precio máximo</th>
                    <th>Precio mínimo</th>
                    <th>Colonia</th>
                    <th>Municipio</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estates as $estate)
                <tr>
                    <td>{{$estate->id}}</td>
                    <td>{{limitString($estate->title,37)}}</td>
                    <td>{{moneyFormat($estate->price_max)}}</td>
                    <td>{{moneyFormat($estate->price_min)}}</td>
                    <td>@if($estate->id_colonia) {{limitString(colonia($estate->id_colonia),30)}} @endif</td>
                    <td>@if($estate->id_municipio) {{municipio($estate->id_municipio)}} @endif</td>
                    <td>@if($estate->id_estado) {{estado($estate->id_estado)}} @endif</td>
                    <td><a href="user/{{$estate->id_user}}">{{username($estate->id_user)}}</a></td>
                    <td>{{ convertDate($estate->created_at) }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" target="_blank" href="https://vangoo.mx/details/desarrollo/{{$estate->id}}">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/desarrollo/{{$estate->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('admin.editdev',$estate->id)}}">
                                        <img src="{{url('./img/icon/update.png')}}" />
                                        Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#devDeleteModal" onclick="devDeleteModalData({{$estate->id}})" id="devDeleteConfirmBtn{{$estate->id}}" data-url="{{route('admin.deleteDev',$estate->id)}}">
                                        <img src="{{url('./img/icon/trash.png')}}" />
                                        Borrar
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!----Delete----->
<div class="modal fade" id="devDeleteModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">¿Estás seguro que deseas eliminar este desarrollo?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Una vez eliminado el desarrollo no podrá ser recuperada, por favor verifica.</p>
                <div class="d-flex justify-content-end">
                    <input type="hidden" id="devDeleteId">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn1" data-bs-dismiss="modal" onclick="devDeleteSend()"> Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function devDeleteModalData(id) {
        $("#devDeleteId").val(id);
    }

    function devDeleteSend() {
        var id = $("#devDeleteId").val();
        var url = $("#devDeleteConfirmBtn" + id).data("url");

        $.ajax({
            url: url,
            type: "DELETE",
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(response) {
                message('success', 'Desarrollo ' + id + ' eliminado. Actualizando tabla... <div class="spinner-border text-success"></div>');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                message('danger', 'Algo salió mal');
                console.error('Error en la solicitud. Código de estado: ' + xhr.status);
            }
        });
    }

    $(document).ready(function() {
        $('#propertiesTable').DataTable({
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
</style>

@endsection()
