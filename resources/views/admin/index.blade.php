@extends('layouts.adminLayout')

@section('breadcrumb','Tablero')

@section('title','Tablero de control')

@section('titleContent','Dashboard admin')

@section('content')

<h5>Hola de nuevo, {{auth()->user()->name}}</h5>

<div class="info-cards">
    <div class="info-card info-card-pink">
        <div>
            <p>Publicaciones activas</p>
            <h1>{{ $activePropertiesCount }}</h1>
        </div>
        <div>
            <img src="{{ url('./img/icon/pub-active.png') }}" />
        </div>
    </div>
    <div class="info-card info-card-yellow">
        <div>
            <p>Publicaciones pendientes</p>
            <h1>{{ $pendingPropertiesCount }}</h1>
        </div>
        <div>
            <img src="{{ url('./img/icon/pub-pend.png') }}" />
        </div>
    </div>
    <div class="info-card info-card-green">
        <div>
            <p>Desarrollos activos</p>
            <h1>{{ $activeDevelopmentsCount }}</h1>
        </div>
        <div>
            <img src="{{ url('./img/icon/cot-active.png') }}" />
        </div>
    </div>
</div>
<br>
<h5>Propiedades recientes</h5>

<div class="content-table">
    <table class="table table-striped table-bordered" id="dashboard-table" data-order='[[ 0, "asc" ]]' data-page-length='8'>
        <thead>
            <tr>
                <th class="start">id</th>
                <th>Titulo</th>
                <th>Precio</th>
                <th>Colonia</th>
                <th>Municipio</th>
                <th>Usuario</th>
                <th class="end">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td>{{ $property->id }}</td>
                <td>{{ $property->title }}</td>
                <td>{{ moneyFormat($property->price) }}</td>
                <td>{{ colonia($property->id_colonia) }}</td>
                <td>{{ municipio($property->id_municipio) }}</td>
                <td>{{ username($property->id_user) }}</td>
                <td>{{ convertDate($property->created_at) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br>
<div class="d-flex justify-content-between align-items-center">
    <h5>Visitantes Vangoo</h5>
    <!-- <button class="btn1">Ver todas</button> -->
</div>

<div class="content-chart">
    <div>
        <canvas id="chart-visitas"></canvas>
    </div>
    <div>
        <p class="text-center">¿Cómo te enteraste de nosotros?</p>
        <canvas id="chart-enterado"></canvas>
    </div>
</div>

<!------JS------>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!------/JS------>

@endsection()