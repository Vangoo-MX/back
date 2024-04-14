<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrador Vangoo | @yield('title')</title>
    <link rel="shortcut icon" href="{{url('./img/icon/icon-logo.png')}}" type="image/PNG">
    <!--------FONTAWESOME--------->
    <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" href="./resources/css/app.css">
    <link rel="stylesheet" href="./resources/css/admin.css">
    <script src="/js/app.js"></script>

</head>

<body>

    <div class="main">

        <nav>
            <div class="nav-menu">
                <div class="logo">
                    <a href="{{route('admin.index')}}">
                        <img src="{{url('./img/icon/icon-logo.png')}}" />
                    </a>
                </div>
                <!---
                <div class="divider">
                    <i class="fa-solid fa-circle"></i>
                </div>-->
                <div class="menu">
                    <li>
                        <a href="{{route('admin.index')}}" class="{{ (request()->is('overview/home*')) ? 'active' : '' }}">
                            <img src="{{url('./img/icon/dashboard.png')}}" title="Dashboard" alt="Dashboard" />
                        </a>
                    </li>
                    <li class="dropdown dropdown-menu-end">
                        <a class="cursor-pointer {{ (request()->is('overview/properties*')) ? 'active' : '' }} {{ (request()->is('overview/queue*')) ? 'active' : '' }}" data-bs-toggle="dropdown">
                            <img src="{{url('./img/icon/properties.png')}}" title="Propiedades" alt="Properties" />
                        </a>
                        <ul class="dropdown-menu menu-primary-dropdown">
                            <li><a class="{{ (request()->is('overview/properties')) ? 'active' : '' }}" href="{{route('admin.properties')}}">Todas las propiedades</a></li>
                            <li><a class="{{ (request()->is('overview/queue*')) ? 'active' : '' }}" href="{{route('admin.queue')}}">Cola de aprobación</a></li>
                            <li><a class="{{ (request()->is('overview/properties-highlights*')) ? 'active' : '' }}" href="{{route('admin.highlights.properties')}}">Propiedades destacadas</a></li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-menu-end">
                        <a class="cursor-pointer {{ (request()->is('overview/developments*')) ? 'active' : '' }} {{ (request()->is('overview/createdev*')) ? 'active' : '' }} {{ (request()->is('overview/highlightsdev*')) ? 'active' : '' }}" data-bs-toggle="dropdown">
                            <img src="{{url('./img/icon/developments.png')}}" title="Desarrollos" alt="Developments" />
                        </a>
                        <ul class="dropdown-menu menu-primary-dropdown">
                            <li><a class="{{ (request()->is('overview/developments*')) ? 'active' : '' }}" href="{{route('admin.developments')}}">Todas los desarrollos</a></li>
                            <li><a class="{{ (request()->is('overview/createdev*')) ? 'active' : '' }}" href="{{route('admin.createdev')}}">Crear desarrollo</a></li>
                            <li><a class="" href="{{route('admin.highlights.developments')}}">Desarrollos destacados</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{route('admin.users')}}" class="{{ (request()->is('overview/user*')) ? 'active' : '' }} {{ (request()->is('overview/users*')) ? 'active' : '' }}">
                            <img src="{{url('./img/icon/users.png')}}" title="Usuarios" alt="Users" />
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.contacts')}}" class="{{ (request()->is('overview/contacts*')) ? 'active' : '' }}">
                            <img src="{{url('./img/icon/contacts.png')}}" title="Contactos" alt="Contacts" />
                        </a>
                    </li>
                    <li style="display:none;">
                        <a href="{{route('admin.statistics')}}" class="{{ (request()->is('overview/statistics*')) ? 'active' : '' }}">
                            <img src="{{url('./img/icon/metrics.png')}}" title="Estadisticas" alt="Metrics" />
                        </a>
                    </li>
                </div>
            </div>
            <div class="nav-footer">
                <li class="d-none">
                    <a href="#">
                        <img src="{{url('./img/icon/settings.png')}}" title="Ajustes" alt="Settings" />
                    </a>
                </li>
                <li class="dropdown dropdown-menu-end">
                    <a href="#" class="profile" data-bs-toggle="dropdown">
                        <img src="{{url('./public/img/users/'.auth()->user()->profile_image)}}" title="Profile" alt="Profile" />
                    </a>
                    <ul class="dropdown-menu menu-primary-dropdown">
                        <li>
                            <a class="dropdown-item" href="{{route('admin.user',auth()->user()->id)}}">
                                <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="https://vangoo.mx/profile/publications">
                                <i class="fa-regular fa-folder-open fa-fw me-2 text-gray-400"></i>
                                Publicaciones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="https://vangoo.mx/profile/lists">
                                <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
                                Listas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{route('user.logout')}}">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </div>
        </nav>

        <div class="content">

            <div class="breadcrumb">
                Inicio <img src="{{url('./img/icon/icon-logo-mini.png')}}" /> @yield('breadcrumb')
            </div>

            <h1>@yield('title')</h1>
            <br>

            <div id="messages"></div>

            @yield('content')

            <br><br><br>

            <footer>
                <span>@ Vangoo 2023</span>
                <div>
                    <img src="{{url('./img/logo.png')}}" />
                </div>
            </footer>

        </div>

    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Segur@ que quieres cerrar la sesión</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "Cerrar sesión" a continuación si está listo para finalizar su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>

    <!------JS------>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{url('/js/app.js')}}"></script>
    <!------/JS------>

</body>

</html>


<script>
    $(document).ready(function() {
        $('#dashboard-table').DataTable({
            paging: false,
            searching: false,
            info: false,
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

    const ctx = document.getElementById('chart-visitas');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Enero', 'Marzo', 'Mayo', 'Julio', 'Septiembre', 'Octubre'],
            backgroundColor: '#75D5C5',
            datasets: [{
                label: 'Estadística de visitas',
                data: [0, 10, 200, 100, 400, 600, 800],
                fill: false,
                borderColor: '#75D5C5',
                backgroundColor: '#75D5C5',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('chart-enterado');

    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: [
                'Referencia',
                'Redes sociales',
                'Publicidad'
            ],
            datasets: [{
                label: '¿Cómo te enteraste de nosotros?',
                data: [50, 200, 100],
                backgroundColor: [
                    '#FB6F8B',
                    '#F7E953',
                    '#4287E4'
                ],
                hoverOffset: 4
            }]
        },
        options: {

        }
    });
</script>
