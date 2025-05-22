@extends('layouts.adminLayout')

@section('breadcrumb','Cola de aprobación')

@section('title','Cola de aprobación')

@section('titleContent','Cola de aprobación de propiedades')

@section('content')

<!-- Content Row -->
<div class="btn-tables d-flex justify-content-start gap-2" id="btn-tables">
    <button class="btn3 active" id="btnespera" onclick="queueAproved('espera')">Pendientes</button>
    <button class="btn3" id="btnrechazados" onclick="queueAproved('rechazados')">Rechazadas</button>
    <button class="btn3" id="btnrevision" onclick="queueAproved('revision')">En revisión</button>
</div>
<!-- En espera de aprobación -->
<div class="row" id="espera">
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
                    <th>Aprobación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                @foreach($estatesQueue as $estateQueue)
                <tr>
                    <td>{{$estateQueue->id}}</td>
                    <td>{{$estateQueue->title}}</td>
                    <td>{{moneyFormat($estateQueue->price)}}</td>
                    <td>{{colonia($estateQueue->id_colonia)}}</td>
                    <td>{{municipio($estateQueue->id_municipio)}}</td>
                    <td>{{estado($estateQueue->id_estado)}}</td>
                    <td><a href="user/{{$estateQueue->id_user}}">{{$estateQueue->id_user}}</a></td>
                    <td>{{$estateQueue->created_at}}</td>
                    <td>
                        <div class="d-flex gap-1 btn-aproved justify-content-start">
                            <form method="post" action="{{route('properties.queue.store')}}">
                                @csrf
                                <input type="hidden" id="id" name="id" value="{{$estateQueue->id}}">
                                <button class="btnSuccess" type="submit">Aprobar</button>
                            </form>
                            <form method="post" action="{{route('properties.queue.reject', $estateQueue->id)}}">
                                @csrf
                                @method('PUT')
                                <button class="btnDanger" type="submit">Rechazar</button>
                            </form>
                            <form method="post" action="{{route('properties.queue.update', $estateQueue->id)}}">
                                @csrf
                                @method('PUT')
                                <button class="btnWarning" type="submit">Revisar</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$estateQueue->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$estateQueue->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
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
<!-- Content Row -->
{{-- Rechazados --}}
<div class="row table-queue" id="rechazados">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="propertiesTable2">
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

                @foreach($estatesRejected as $estateRejected)
                <tr>

                    <td>{{$estateRejected->id}}</td>
                    <td>{{$estateRejected->title}}</td>
                    <td>{{moneyFormat($estateRejected->price)}}</td>
                    <td>{{colonia($estateRejected->id_colonia)}}</td>
                    <td>{{municipio($estateRejected->id_municipio)}}</td>
                    <td>{{estado($estateRejected->id_estado)}}</td>
                    <td><a href="user/{{$estateRejected->id_user}}">{{$estateRejected->id_user}}</a></td>
                    <td>{{$estateRejected->created_at}}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$estateRejected->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$estateRejected->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
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
<!---------------->
{{-- Revisión --}}

<div class="row table-queue" id="revision">
    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="propertiesTable3">
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
                    <th>Aprobación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                @foreach($estatesRevision as $estateRevision)
                <tr>

                    <td>{{$estateRevision->id}}</td>
                    <td>{{$estateRevision->title}}</td>
                    <td>{{moneyFormat($estateRevision->price)}}</td>
                    <td>{{colonia($estateRevision->id_colonia)}}</td>
                    <td>{{municipio($estateRevision->id_municipio)}}</td>
                    <td>{{estado($estateRevision->id_estado)}}</td>
                    <td><a href="user/{{$estateRevision->id_user}}">{{$estateRevision->id_user}}</a></td>
                    <td>{{$estateRevision->created_at}}</td>
                    <td>
                        <div class="d-flex gap-1 btn-aproved justify-content-start">
                            <form method="post" action="{{route('properties.queue.store')}}">
                                @csrf
                                <input type="hidden" id="id" name="id" value="{{$estateRevision->id}}">
                                <button class="btnSuccess" type="submit">Aprobar</button>
                            </form>
                            <form method="post" action="{{route('properties.queue.reject', $estateRevision->id)}}">
                                @csrf
                                @method('PUT')
                                <button class="btnDanger" type="submit">Rechazar</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$estateRevision->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$estateRevision->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
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

<!---------------->
<br><br>

<script>
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
        $('#propertiesTable2').DataTable({
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
        $('#propertiesTable3').DataTable({
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
