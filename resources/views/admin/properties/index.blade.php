@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades')

@section('title','Propiedades')

@section('titleContent','Propiedades publicadas')

@section('content')

@include('admin.partials.properties-filters', ['estates' => $estates])

<div class="table-container">
    <table class="custom-table w-full text-sm text-left">
        <thead class="custom-header">
            <tr>
                <th>Título</th>
                <th>Precio</th>
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
                <td>
                    {{ limitString($estate->title,37) }}
                    <span class="badge {{ $estate->status === 1 ? 'active' : 'inactive' }}">
                        {{ $estate->status === 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>{{ moneyFormat($estate->price) }}</td>
                <td>{{ limitString(colonia($estate->id_colonia),30) }}</td>
                <td>{{ municipio($estate->id_municipio) }}</td>
                <td>{{ estado($estate->id_estado) }}</td>
                <td><a href="https://dashboard.vangoo.mx/users/{{$estate->user->uuid}}">{{ $estate->user->name }}</td>
                <td>
                    <div class="dropdown" data-scope="table-dropdown">
                        <button class="dropdown-toggle">Opciones</button>
                        <div class="dropdown-menu">
                            <a href="{{route('properties.show', $estate->id)}}" class="dropdown-item">
                                <i class="fas fa-eye me-2"></i> Detalles
                            </a>
                            @if($estate->status === 0)
                            <button
                                type="button"
                                class="dropdown-item"
                                onclick="propertyActiveSend({{ $estate->id }})"
                                data-url="{{ route('properties.active', $estate->id) }}"
                                id="propertyActivateConfirmBtn{{ $estate->id }}">
                                <i class="fas fa-toggle-on me-2"></i> Activar
                            </button>
                            @else
                            <button
                                type="button"
                                class="dropdown-item"
                                onclick="propertyDeactiveSend({{ $estate->id }})"
                                data-url="{{ route('properties.deactive', $estate->id) }}"
                                id="propertyDeactiveConfirmBtn{{ $estate->id }}">
                                <i class="fas fa-toggle-off me-2"></i> Desactivar
                            </button>
                            @endif

                            <button
                                type="button"
                                class="dropdown-item text-danger"
                                onclick="propertyDeleteSend({{ $estate->id }})"
                                data-url="{{ route('properties.destroy', $estate->id) }}"
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
