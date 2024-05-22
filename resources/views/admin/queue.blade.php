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

                @foreach($propiedadesqueue as $p)
                <tr>

                    <td>{{$p->id}}</td>
                    <td>{{$p->title}}</td>
                    <td>{{moneyFormat($p->price)}}</td>
                    <td>{{colonia($p->id_colonia)}}</td>
                    <td>{{municipio($p->id_municipio)}}</td>
                    <td>{{estado($p->id_estado)}}</td>
                    <td><a href="user/{{$p->id_user}}">{{$p->id_user}}</a></td>
                    <td>{{$p->created_at}}</td>
                    <td>
                        <div class="d-flex gap-1 btn-aproved justify-content-start">
                            <form method="post" action="{{route('epPropertyQueue.aproved')}}">
                                @csrf
                                <input type="hidden" id="id" name="id" value="{{$p->id}}">
                                <button class="btnSuccess" type="submit">Aprobar</button>
                            </form>
                            <a href="{{route('epPropertyQueue.reject', $p->id)}}">
                                <button class="btnDanger">Rechazar</button>
                            </a>
                            <a href="{{route('epPropertyQueue.revision', $p->id)}}">
                                <button class="btnWarning">Revisar</button>
                            </a>
                        </div>
                    </td>
                    <td>
                        <!---
                            <a href="{route('epPropertyQueue.delete',$p->id)}}">
                                <i class="fa-solid fa-circle-xmark text-danger mx-1"></i>
                            </a>
                            <a href="https://vangoo.mx/details/propertyqueue/{$p->id}}" target="_blank">
                                <i class="fa-solid fa-link mx-1"></i>
                            </a>--->

                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$p->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$p->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyQueueDeleteModal" onclick="propertyQueueDeleteModalData({{$p->id}})" id="propertyQueueDeleteConfirmBtn{{$p->id}}" data-url="{{route('epPropertyQueue.delete',$p->id)}}">
                                        <img src="{{url('./img/icon/trash.png')}}" />
                                        Borrar
                                    </a>
                                </li> -->
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

                @foreach($propiedadesrejected as $p)
                <tr>

                    <td>{{$p->id}}</td>
                    <td>{{$p->title}}</td>
                    <td>{{moneyFormat($p->price)}}</td>
                    <td>{{colonia($p->id_colonia)}}</td>
                    <td>{{municipio($p->id_municipio)}}</td>
                    <td>{{estado($p->id_estado)}}</td>
                    <td><a href="user/{{$p->id_user}}">{{$p->id_user}}</a></td>
                    <td>{{$p->created_at}}</td>
                    <td>
                        <!---
                            <i class="fa-solid fa-file-lines mx-1 d-none"></i>
                            <i class="fa-solid fa-pen-to-square text-info mx-1 d-none"></i>
                            <a href="{route('epPropertyQueue.delete',$p->id)}}">
                                <i class="fa-solid fa-circle-xmark text-danger mx-1"></i>
                            </a>
                            <a href="https://vangoo.mx/details/propertyqueue/{$p->id}}" target="_blank">
                                <i class="fa-solid fa-link mx-1"></i>
                            </a>--->

                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$p->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$p->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyQueueDeleteModal" onclick="propertyQueueDeleteModalData({{$p->id}})" id="propertyQueueDeleteConfirmBtn{{$p->id}}" data-url="{{route('epPropertyQueue.delete',$p->id)}}">
                                        <img src="{{url('./img/icon/trash.png')}}" />
                                        Borrar
                                    </a>
                                </li> -->
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

                @foreach($propiedadesrevision as $p)
                <tr>

                    <td>{{$p->id}}</td>
                    <td>{{$p->title}}</td>
                    <td>{{moneyFormat($p->price)}}</td>
                    <td>{{colonia($p->id_colonia)}}</td>
                    <td>{{municipio($p->id_municipio)}}</td>
                    <td>{{estado($p->id_estado)}}</td>
                    <td><a href="user/{{$p->id_user}}">{{$p->id_user}}</a></td>
                    <td>{{$p->created_at}}</td>
                    <td>
                        <div class="d-flex gap-1 btn-aproved justify-content-start">
                            <form method="post" action="{{route('epPropertyQueue.aproved')}}">
                                @csrf
                                <input type="hidden" id="id" name="id" value="{{$p->id}}">
                                <button class="btnSuccess" type="submit">Aprobar</button>
                            </form>
                            <a href="{{route('epPropertyQueue.reject', $p->id)}}">
                                <button class="btnDanger">Rechazar</button>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="https://vangoo.mx/details/propertyqueue/{{$p->id}}" target="_blank">
                                        <img src="{{url('./img/icon/info.png')}}" />
                                        Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cursor-pointer" onclick="copyToClipboard('https://vangoo.mx/details/propertyqueue/{{$p->id}}')">
                                        <img src="{{url('./img/icon/link.png')}}" />
                                        Copiar link
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#propertyQueueDeleteModal" onclick="propertyQueueDeleteModalData({{$p->id}})" id="propertyQueueDeleteConfirmBtn{{$p->id}}" data-url="{{route('epPropertyQueue.delete',$p->id)}}">
                                        <img src="{{url('./img/icon/trash.png')}}" />
                                        Borrar
                                    </a>
                                </li> -->
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