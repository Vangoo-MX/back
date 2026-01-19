@extends('layouts.adminLayout')

@section('breadcrumb','Lotes')

@section('title','Lotes')

@section('titleContent','Lotes')

@section('content')

@include('admin.partials.properties-filters', ['estates' => $estates])

<div class="table-container">
    <table class="custom-table w-full text-sm text-left">
        <thead class="custom-header">
            <tr>
                <th>Título</th>
                <th>Precio Minimo</th>
                <th>Precio Máximo</th>
                <th>Colonia</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="estate-table-body">
            @foreach($estates as $estate)
            <tr class="estate-row"
                data-title="{{ strtolower($estate->title) }}"
                data-colonia="{{ $estate->id_colonia }}"
                data-municipio="{{ $estate->id_municipio }}"
                data-estado="{{ $estate->id_estado }}">
                <td>{{ limitString($estate->title,37) }}</td>
                <td>{{ moneyFormat($estate->price_min) }}</td>
                <td>{{ moneyFormat($estate->price_max) }}</td>
                <td>{{ limitString(colonia($estate->id_colonia),30) }}</td>
                <td>{{ municipio($estate->id_municipio) }}</td>
                <td>{{ estado($estate->id_estado) }}</td>
                <td><a href="https://dashboard.vangoo.mx/users/{{$estate->user->uuid}}">{{ $estate->user->name }}</td>
                <td>
                    <div class="dropdown" data-scope="table-dropdown">
                        <button class="dropdown-toggle">Opciones</button>
                        <div class="dropdown-menu">
                            <a target="_blank" href="https://www.vangoo.mx/detailslots/lots/{{ $estate->id }}" class="dropdown-item">
                                <i class="fas fa-eye me-2"></i> Detalles
                            </a>
                            <a
                                href="{{ route('lots.edit', $estate->id) }}"
                                class="dropdown-item">
                                <i class="fas fa-edit me-2"></i> Editar
                            </a>
                            <button
                                type="button"
                                class="dropdown-item text-danger"
                                onclick="propertyDeleteSend({{ $estate->id }})"
                                data-url="{{ route('lots.destroy', $estate->id) }}"
                                id="propertyDeleteBtn{{ $estate->id }}">
                                <i class="fas fa-trash-alt me-2"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.partials.properties-scripts')

@endsection()
