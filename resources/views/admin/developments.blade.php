@extends('layouts.adminLayout')

@section('breadcrumb','Desarrollos')

@section('title','Desarrollos')

@section('titleContent','Desarrollos')

@section('content')

<!-- Content Row -->
<div class="row">
    <div class="container mt-3 px-4">
        <form method="GET" action="{{ route('admin.developments') }}" class="mb-4">
            <div class="form-group">
                <label for="modeSelect">Filtrar por tipo de desarrollo:</label>
                <select name="mode" id="modeSelect" class="form-control" onchange="this.form.submit()">
                    <option value="all" {{ $selectedMode == 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="horizontal" {{ $selectedMode == 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                    <option value="vertical" {{ $selectedMode == 'vertical' ? 'selected' : '' }}>Vertical</option>
                </select>
            </div>
        </form>
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
                @foreach($desarrollos as $p)
                <tr>
                    <td>{{$p->id}}</td>
                    <td>{{limitString($p->title,37)}}</td>
                    <td>{{moneyFormat($p->price_max)}}</td>
                    <td>{{moneyFormat($p->price_min)}}</td>
                    <td>@if($p->id_colonia) {{limitString(colonia($p->id_colonia),30)}} @endif</td>
                    <td>@if($p->id_municipio) {{municipio($p->id_municipio)}} @endif</td>
                    <td>@if($p->id_estado) {{estado($p->id_estado)}} @endif</td>
                    <td><a href="user/{{$p->id_user}}">{{username($p->id_user)}}</a></td>
                    <td>{{ convertDate($p->created_at) }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" target="_blank" href="https://vangoo.mx/details/desarrollo/{{$p->id}}">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/desarrollo/{{$p->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('admin.editdev',$p->id)}}">
                                        <img src="{{url('./img/icon/update.png')}}" />
                                        Editar
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#devDeactivateModal" onclick="devDeactiveModalData({{$p->id}})" id="devDeactiveConfirmBtn{{$p->id}}" data-url="">
                                        <img src="{{url('./img/icon/desactive.png')}}" />
                                        Desactivar
                                    </a>
                                </li> -->
                                <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#devDeleteModal" onclick="devDeleteModalData({{$p->id}})" id="devDeleteConfirmBtn{{$p->id}}" data-url="{{route('epDev.delete',$p->id)}}">
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
<!----Deactivate----->
<div class="modal fade" id="devDeactivateModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">¿Estás seguro que deseas desactivar este desarrollo?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Mientras el desarrollo esté desactivado no podrá ser visualizado en el portal de Vangoo.mx</p>
                <div class="d-flex justify-content-end">
                    <input type="hidden" id="devDeactivateId">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn1" data-bs-dismiss="modal" onclick="devDeactiveSend()">Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function devDeleteModalData(id) {
        $("#devDeleteId").val(id);
    }

    function devDeactiveModalData(id) {
        $("#devDeactivateId").val(id);
    }

    function devDeleteSend() {
        var id = $("#devDeleteId").val();
        var url = $("#devDeleteConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
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

    function devDeactiveSend() {
        var id = $("#devDeactivateId").val();
        var url = $("#devDeactiveConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                message('success', 'Desarrollo ' + id + ' desactivado. Actualizando tabla... <div class="spinner-border text-success"></div>');
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
