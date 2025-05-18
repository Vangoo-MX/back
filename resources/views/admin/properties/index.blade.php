@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades')

@section('title','Propiedades')

@section('titleContent','Propiedades publicadas')

@section('content')

<!-- Content Row -->
<div class="row">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="propertiesTable">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Titulo</th>
                    <th>Precio</th>
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
                    <td>{{moneyFormat($estate->price)}}</td>
                    <td>{{limitString(colonia($estate->id_colonia),30)}}</td>
                    <td>{{municipio($estate->id_municipio)}}</td>
                    <td>{{estado($estate->id_estado)}}</td>
                    <td><a href="user/{{$estate->id_user}}">{{username($estate->id_user)}}</a></td>
                    <td>{{ convertDate($estate->created_at) }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{route('properties.show',$estate->id)}}">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propiedad/{{$estate->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <li>
                                    @if ($estate->status == 1)
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyDeactivateModal" onclick="propertyDeactiveModalData({{$estate->id}})" id="propertyDeactiveConfirmBtn{{$estate->id}}" data-url="{{route('admin.deactiveProperty',$estate->id)}}">
                                        <img src="{{url('./img/icon/desactive.png')}}" />
                                        Desactivar
                                    </a>
                                    @else
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyActivateModal" onclick="propertyActiveModalData({{$estate->id}})" id="propertyActivateConfirmBtn{{$estate->id}}" data-url="{{route('admin.activeProperty',$estate->id)}}">
                                        <img src="{{url('./img/icon/desactive.png')}}" />
                                        Activar
                                    </a>
                                    @endif
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyDeleteModal" onclick="propertyDeleteModalData({{$estate->id}})" id="propertyDeleteConfirmBtn{{$estate->id}}" data-url="{{route('properties.destroy',$estate->id)}}">
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
<div class="modal fade" id="propertyDeleteModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">¿Estás seguro que deseas eliminar esta propiedad?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Una vez eliminada la propiedad no podrá ser recuperada, por favor verifica.</p>
                <div class="d-flex justify-content-end">
                    <input type="hidden" id="propertyDeleteId">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn1" data-bs-dismiss="modal" onclick="propertyDeleteSend()"> Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>
<!----Deactivate----->
<div class="modal fade" id="propertyDeactivateModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">¿Estás seguro que deseas desactivar esta propiedad?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Mientras la propiedad esté desactivado no podrá ser visualizado en el portal de Vangoo.mx</p>
                <div class="d-flex justify-content-end">
                    <input type="hidden" id="propertyDeactivateId">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn1" data-bs-dismiss="modal" onclick="propertyDeactiveSend()">Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!----Activate----->
<div class="modal fade" id="propertyActivateModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">¿Estás seguro que deseas volver a activar esta propiedad?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>La propiedad volvera a visualizarse en el portal de Vangoo.mx</p>
                <div class="d-flex justify-content-end">
                    <input type="hidden" id="propertyActivateId">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn1" data-bs-dismiss="modal" onclick="propertyActiveSend()">Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function propertyDeleteModalData(id) {
        $("#propertyDeleteId").val(id);
    }

    function propertyDeactiveModalData(id) {
        $("#propertyDeactivateId").val(id);
    }

    function propertyActiveModalData(id) {
        $("#propertyActivateId").val(id);
    }

    function propertyDeleteSend() {
        var id = $("#propertyDeleteId").val();
        var url = $("#propertyDeleteConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                message('success', 'Propiedad ' + id + ' eliminada. Actualizando tabla... <div class="spinner-border text-success"></div>');
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

    function propertyDeactiveSend() {
        var id = $("#propertyDeactivateId").val();
        var url = $("#propertyDeactiveConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                message('success', 'Propiedad ' + id + ' desactivada. Actualizando tabla... <div class="spinner-border text-success"></div>');
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

    function propertyActiveSend() {
        var id = $("#propertyActivateId").val();
        var url = $("#propertyActivateConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                message('success', 'Propiedad ' + id + ' activada. Actualizando tabla... <div class="spinner-border text-success"></div>');
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
