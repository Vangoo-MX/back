<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Administrador Vangoo |  @yield('title')</title>
        <meta name="description" content="">
        <link rel="icon" type="image/png" href="{{url('./require/resources/img/favicon.png')}}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{url('./require/resources/css/app.css')}}" rel="stylesheet">
        <link href="{{url('./require/resources/css/admin.css')}}" rel="stylesheet">
        <!--------FONTAWESOME--------->
        <script src="https://kit.fontawesome.com/e0df5df9e9.js" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    </head>
    <body>

        <!-- Page Wrapper -->
        <div id="wrapper">

            <!-- Sidebar -->
            <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.index')}}">
                    <div class="sidebar-brand-icon logo">
                        <img src="{{url('./require/resources/img/logo2.png')}}" width="70%">
                    </div>

                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Dashboard -->

                <li class="nav-item {{ (request()->is('overview/home*')) ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('admin.index')}}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>

                <li class="nav-item {{ (request()->is('overview/properties*')) ? 'active' : '' }} {{ (request()->is('overview/queue*')) ? 'active' : '' }}">
                    <a class="nav-link collapsed cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>Propiedades</span>
                    </a>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item {{ (request()->is('overview/properties*')) ? 'active' : '' }}" href="{{route('admin.properties')}}">Todas las propiedades</a>
                            <a class="collapse-item {{ (request()->is('overview/queue*')) ? 'active' : '' }}" href="{{route('admin.queue')}}">Cola de aprobación</a>
                            <a class="collapse-item" href="{{route('admin.highlights.properties')}}">Propiedades destacadas</a>
                        </div>
                    </div>
                </li>

                <!-- Nav Item -->
                <li class="nav-item {{ (request()->is('overview/developments*')) ? 'active' : '' }} {{ (request()->is('overview/createdev*')) ? 'active' : '' }} {{ (request()->is('overview/highlightsdev*')) ? 'active' : '' }}">
                    <a class="nav-link collapsed cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseDev">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>Desarrollos</span>
                    </a>
                    <div id="collapseDev" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item {{ (request()->is('overview/developments*')) ? 'active' : '' }}" href="{{route('admin.developments')}}">Todas los desarrollos</a>
                            <a class="collapse-item {{ (request()->is('overview/createdev*')) ? 'active' : '' }}" href="{{route('admin.createdev')}}">Crear desarrollo</a>
                            <a class="collapse-item" href="{{route('admin.highlights.developments')}}">Desarrollos destacados</a>
                        </div>
                    </div>
                </li>

                <!-- Nav Item -->
                <li class="nav-item {{ (request()->is('overview/user*')) ? 'active' : '' }} {{ (request()->is('overview/users*')) ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('admin.users')}}">
                        <i class="fa-solid fa-user"></i>
                        <span>Usuarios</span></a>
                </li>

                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item d-none {{ (request()->is('overview/settingsinfo*')) ? 'active' : '' }}">
                    <a class="nav-link collapsed cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item {{ (request()->is('overview/settingsinfo*')) ? 'active' : '' }}" href="{{route('admin.settingsinfo')}}">Información</a>
                        </div>
                    </div>
                </li>

                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item {{ (request()->is('overview/statistics*')) ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('admin.statistics')}}">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Estadisticas</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow d-flex justify-content-between justify-content-md-end">

                        <!-- Sidebar Toggle (Topbar) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                            <i class="fa fa-bars"></i>
                        </button>

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">

                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-menu dropdown-menu-end p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light border-0 small"
                                                placeholder="Search for..." aria-label="Search"
                                                aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </li>

                            <!-- Nav Item - Alerts -->
                            <li class="nav-item dropdown no-arrow mx-1 d-none">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-bs-expanded="false">
                                    <i class="fas fa-bell fa-fw"></i>
                                    <!-- Counter - Alerts -->
                                    <span class="badge bg-danger rounded-pill">3</span>
                                </a>
                                <!-- Dropdown - Alerts -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                    aria-labelledby="alertsDropdown">
                                    <h5 class="dropdown-header">
                                        Notificaciones
                                    </h5>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div>
                                            <div class="small text-gray-500">December 12, 2019</div>
                                            <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                        </div>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div>
                                            <div class="small text-gray-500">December 7, 2019</div>
                                            $290.29 has been deposited into your account!
                                        </div>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div>
                                            <div class="small text-gray-500">December 2, 2019</div>
                                            Spending Alert: We've noticed unusually high spending for your account.
                                        </div>
                                    </a>
                                    <a class="dropdown-item text-center small text-gray-500" href="#">Ver todo</a>
                                </div>
                            </li>

                            <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-bs-expanded="false">
                                    <span class="me-2 d-none d-lg-inline text-gray-600 small">Bienvenid@ {{auth()->user()->name}}</span>
                                    <div class="img-profile rounded-circle" style="width:40px;height:40px;">
                                        <img
                                        src='{{url('./img/users/'.auth()->user()->profile_image)}}' onerror="{this.src='{{url('./require/resources/img/img404.jpg')}}'}" style="width:100%;height: 100%;object-fit:cover;border-radius:50%;">
                                    </div>
                                    
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-end me-1 shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{route('admin.user',auth()->user()->id)}}">
                                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                        Perfil
                                    </a>
                                    <a class="dropdown-item" href="https://vangoo.mx/profile/publications">
                                        <i class="fa-regular fa-folder-open fa-fw me-2 text-gray-400"></i>
                                        Publicaciones
                                    </a>
                                    <a class="dropdown-item" href="https://vangoo.mx/profile/lists">
                                        <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
                                        Listas
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('user.logout')}}">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>

                        </ul>

                    </nav>
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                        <div class="px-1 px-lg-3 d-sm-flex align-items-center justify-content-between mb-4">
                            <span class="mb-0 text-gray-800">Inicio > @yield('titleContent')</span>
                        </div>

                        <div class="px-1 px-lg-3">

                            @yield('content')

                        </div>


                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="login.html">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!------JS------>
        <script src="{{url('./require/resources/js/jquery-easing/jquery.easing.min.js')}}"></script>
        <script src="{{url('./require/resources/js/admin.js')}}"></script>
        <!------/JS------>
        
    </body>
</html>