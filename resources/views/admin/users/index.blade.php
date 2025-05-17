@extends('layouts.adminLayout')

@section('breadcrumb','Usuarios')

@section('title','Usuarios')

@section('titleContent','Usuarios')

@section('content')

<a href="{{route('admin.users.create')}}">
    <button class="btn1"> Crear nuevo usuario </button>
</a>

<!-- Content Row -->
<div class="row">

    <div class="container mt-3 px-4">
        <table class="table table-striped table-bordered" id="propertiesTable">
            <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Verificación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{$u->id}}</td>
                    <td><a href="{{route('admin.users.show',$u->id)}}">{{$u->name}}</a></td>
                    <td>{{$u->email}}</td>
                    <td>{{$u->tel}}</td>
                    <td>{{ $u->rol->title() }}</td>
                    <td>
                        <div class="dropdown">
                            <span type="button" class="{{$u->status == 1 ? 'card-status-green' : 'card-status-grey'}} dropdown-toggle" data-bs-toggle="dropdown">{{$u->status == 1 ? 'Aprobado' : 'En proceso'}}</span>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.changestatus', ['userid' => $u->id,'status' => '1']) }}">
                                        Aprobado
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.changestatus', ['userid' => $u->id,'status' => '0']) }}">
                                        En proceso
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
                            <form id="deleteForm" method="post">
                                @method("DELETE")
                                @csrf
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{route('admin.user',$u->id)}}" target="_blank">
                                            <img src="{{url('./img/icon/info.png')}}" />
                                            Detalles
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete" data-user-id="{{$u->id}}">
                                            <img src="{{url('./img/icon/trash.png')}}" />
                                            Borrar
                                        </button>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!----Delete----->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteConfirmationModalLabel">¿Estás seguro que deseas eliminar este usuario?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>Una vez eliminado el usuario no podrá ser recuperada, ¿estás seguro de que deseas eliminarlo?</p>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn1" id="confirmDeleteButton"> Confirmar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.dropdown-item.delete').click(function(event) {
            event.preventDefault();
            var userId = $(this).data('user-id');
            var formAction = "{{ route('admin.destroy', ':userId') }}".replace(':userId', userId);
            $('#deleteForm').attr('action', formAction);
            $('#deleteConfirmationModal').modal('show');
        });

        $('#confirmDeleteButton').click(function() {
            $('#deleteForm').submit();
        });
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
    })
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
</style>

@endsection()
