@extends('layouts.adminLayout')

@section('breadcrumb','Contactos')

@section('title','Contactos')

@section('titleContent','Contactos')

@section('content')

<!-- Content Row -->
<div class="row">

    <div class="container mt-3 px-4">
        <form method="GET" action="{{ route('agenda.index') }}" class="mb-4">
            <div class="form-group">
                <label for="userSelect">Seleccionar Usuario:</label>
                <select name="user_id" id="userSelect" class="form-control" onchange="this.form.submit()">
                    @foreach($users as $id => $name)
                    <option value="{{ $id }}" {{ $id == $selectedUserID ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
        <table class="table table-striped table-bordered" id="contactsTable">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Crédito</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agenda as $agend)
                <tr>
                    <td>
                        <a href="{{route('users.show', $agend->user->uuid)}}">
                            {{$agend->user->name}}
                            @if(!$agend->mensaje_leido)
                            <span class="badge bg-danger">Nuevo Mensaje</span>
                            @endif
                        </a>
                    </td>
                    <td>{{$agend->name}}</td>
                    <td>{{$agend->email}}</td>
                    <td>{{$agend->phone}}</td>
                    <td>{{$agend->credit_score}}</td>
                    <td>
                        <div class="dropdown">
                            <span type="button" class="{{$agend->etapa == 4 ? 'card-status-green' : 'card-status-grey'}} dropdown-toggle" data-bs-toggle="dropdown">{{$agend->etapa == 4 ? 'Listo' : 'Etapa '.$agend->etapa}}</span>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('agenda.statusContact', ['agenda' => $agend->id,'etapa' => '1']) }}">
                                        Etapa 1
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('agenda.statusContact', ['agenda' => $agend->id,'etapa' => '2']) }}">
                                        Etapa 2
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('agenda.statusContact', ['agenda' => $agend->id,'etapa' => '3']) }}">
                                        Etapa 3
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('agenda.statusContact', ['agenda' => $agend->id,'etapa' => '4']) }}">
                                        Listo
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class="d-flex gap-3">
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#documentsModal" onclick="documentsModalData({{$agend->id}})" id="documentsConfirmBtn{{$agend->id}}" data-url="{{route('agenda.showDocuments',$agend->id)}}">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Documentos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" href="{{ route('agenda.marcarLeido', $agend->id) }}">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Marcar como leído
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

<!----Documents----->
<div class="modal fade" id="documentsModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Documentos del contacto <span id="contactIdText"></span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="contactDocsForm" method="post" action="{{route('contactDocs.post')}}">
                    @csrf
                    <input type="hidden" name="id" id="contactId">
                    <div>
                        <h4 class="my-2">Datos - Primera Etapa</h4>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="contacto_cliente" id="contacto_cliente">
                                <label class="form-check-label">Contacto con el Cliente</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="papeleria" id="papeleria">
                                <label class="form-check-label">Recopilación de Papelería</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visita_casas" id="visita_casas">
                                <label class="form-check-label">Selección de Casa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="seleccion_tipo_credito" id="seleccion_tipo_credito">
                                <label class="form-check-label">Selección de Tipo de Crédito</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingreso_papeleria" id="ingreso_papeleria">
                                <label class="form-check-label">Ingreso de Papelería</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="respuesta_bancos" id="respuesta_bancos">
                                <label class="form-check-label">Respuesta de Bancos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="seleccion_financiamiento" id="seleccion_financiamiento">
                                <label class="form-check-label">Selección de Financiamiento</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="firma_carta_promesa" id="firma_carta_promesa">
                                <label class="form-check-label">Firma de Carta Promesa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="firma_carta_comision" id="firma_carta_comision">
                                <label class="form-check-label">Firma de Carta Comisión</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="my-2">Datos - Segunda Etapa</h4>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="seleccion_notaria" id="seleccion_notaria">
                                <label class="form-check-label">Selección de Notaría</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="solicitud_avaluo" id="solicitud_avaluo">
                                <label class="form-check-label">Solicitud de Avalúo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="revision_papeleria" id="revision_papeleria">
                                <label class="form-check-label">Revisión de Papelería</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingreso_pre_preventivo" id="ingreso_pre_preventivo">
                                <label class="form-check-label">Ingreso de Pre-Preventivo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recepcion_avaluo" id="recepcion_avaluo">
                                <label class="form-check-label">Recepción de Avalúo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recepcion_pre_preventivo" id="recepcion_pre_preventivo">
                                <label class="form-check-label">Recepción de Pre-Preventivo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingreso_infonavit" id="ingreso_infonavit">
                                <label class="form-check-label">Ingreso de Infonavit</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="my-2">Datos - Tercera Etapa</h4>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recepcion_infonavit" id="recepcion_infonavit">
                                <label class="form-check-label">Recepción de Infonavit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cierre_numeros" id="cierre_numeros">
                                <label class="form-check-label">Cierre de Números</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="realizacion_contratos" id="realizacion_contratos">
                                <label class="form-check-label">Realización de Contratos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="autorizacion_contratos" id="autorizacion_contratos">
                                <label class="form-check-label">Autorización de Contratos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="firma_contrato" id="firma_contrato">
                                <label class="form-check-label">Firma de Contrato</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="pago_comision" id="pago_comision">
                                <label class="form-check-label">Pago de Comisión</label>
                            </div>
                            <div class="my-3">
                                <label for="nota_admin">Notas admin:</label>
                                <textarea class="form-control" rows="3" name="nota_admin" id="nota_admin"></textarea>
                            </div>
                            <div class="my-3">
                                <label for="nota_vendedor">Notas vendedor:</label>
                                <textarea class="form-control" rows="3" name="nota_vendedor" id="nota_vendedor" readonly></textarea>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn1">Guardar</button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<style>
    .badge.bg-danger {
        background-color: #dc3545;
        color: #fff;
    }
</style>

<script>
    function documentsModalData(id) {
        $("#contactId").val(id);
        $("#contactIdText").html(id);
        var url = $("#documentsConfirmBtn" + id).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function(resp) {
                $('input[type="checkbox"]').prop('checked', false);
                $("#nota_admin").val('');
                $("#nota_vendedor").val('');
                var res = resp[0];
                for (var key in res) {
                    if (res.hasOwnProperty(key) && key != 'id') {
                        if (res[key] == 1 || res[key] == '1') {
                            $("#" + key).prop("checked", true);
                        }
                    }
                }
                if (res['nota_admin']) {
                    $("#nota_admin").val(res['nota_admin']);
                }
                if (res['nota_vendedor']) {
                    $("#nota_vendedor").val(res['nota_vendedor']);
                }
            },
            error: function(xhr) {
                message('danger', 'No se pudo obtener los datos del contacto');
                console.error('Error en la solicitud. Código de estado: ' + xhr.status);
            }
        });
    }

    $(document).ready(function() {
        $('#contactsTable').DataTable({
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

@endsection()
