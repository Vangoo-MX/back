@extends('layouts.adminLayout')

@section('breadcrumb','Desarrollos')

@section('title','Developments')

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
            @foreach($desarrollos as $p)
                <tr>
                    <td>{{$p->id}}</td>
                    <td>{{$p->title}}</td>
                    <td>{{moneyFormat($p->price_max)}}</td>
                    <td>{{moneyFormat($p->price_min)}}</td>
                    <td>@if($p->id_colonia) {{colonia($p->id_colonia)}} @endif</td>
                    <td>@if($p->id_municipio) {{municipio($p->id_municipio)}} @endif</td>
                    <td>@if($p->id_estado) {{estado($p->id_estado)}} @endif</td>
                    <td><a href="user/{{$p->id_user}}">{{$p->id_user}}</a></td>
                    <td>{{$p->created_at}}</td>
                    <td class="d-flex gap-3">
                        <i class="fa-solid fa-file-lines d-none"></i>
                        <a href="{{route('admin.editdev',$p->id)}}">
                            <i class="fa-solid fa-pen-to-square text-info mx-1"></i>
                        </a>
                        <a href="{{route('epDev.delete',$p->id)}}">
                            <i class="fa-solid fa-circle-xmark text-danger mx-1"></i>
                        </a>
                        <a href="https://vangoo.mx/details/desarrollo/{{$p->id}}" target="_blank">
                            <i class="fa-solid fa-link mx-1"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
  $(document).ready(function() {
        $('#propertiesTable').DataTable({
            language: {
                processing:     "Procesando..",
                search:         "Buscar:&nbsp;",
                lengthMenu:    "Ver _MENU_ Elementos",
                info:           "Mostrando de _START_ a _END_ de _TOTAL_ Elementos",
                infoFiltered:   "(filtrando de _MAX_ elementos en total)",
                infoPostFix:    "",
                loadingRecords: "Cargando registros...",
                zeroRecords:    "No hay registros",
                emptyTable:     "No hay datos para mostrar",
                paginate: {
                    first:      "Primero",
                    previous:   "Anterior",
                    next:       "Siguiente",
                    last:       "Último"
                },
                aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    } );
 </script>

 <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        color: inherit !important;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius:50px;
        background-color: transparent;
        background: transparent;
    }
 </style>

@endsection()
