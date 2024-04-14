@extends('layouts.adminLayout')

@section('breadcrumb','Estadísticas')

@section('title','Estadisticas')

@section('titleContent','Estadisticas')

@section('content')


<!-- Content Row -->
<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Estadistica de visitas</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Acciones:</div>
                        <a class="dropdown-item" href="#">Ver estadisticas</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">¿Cómo te enteraste de nosotros?</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Acciones:</div>
                        <a class="dropdown-item" href="#">Ver estadisticas</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Referencia
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Redes sociales
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Publicidad
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{url('./js/chart.js/Chart.min.js')}}"></script>
<script src="{{url('./js/chart-area-demo.js')}}"></script>
<script src="{{url('./js/chart-pie-demo.js')}}"></script>

@endsection()
