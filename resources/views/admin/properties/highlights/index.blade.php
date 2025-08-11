@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades destacadas')

@section('title','Highlights')

@section('titleContent','Propiedades destacadas')

@section('content')

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Propiedades Destacadas</h3>
    </div>

    {{-- Filtros y botón para nueva propiedad --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('properties.highlights.store') }}" class="row g-3 align-items-end">
                @csrf

                {{-- Filtro municipio --}}
                <div class="col-md-4">
                    <label for="filterMunicipio" class="form-label">Municipio</label>
                    <select id="filterMunicipio" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($municipios as $municipio)
                        <option value="{{ $municipio->id }}">{{ $municipio->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro propiedad --}}
                <div class="col-md-4">
                    <label for="filterProperty" class="form-label">Propiedad</label>
                    <select id="filterProperty" class="form-select" name="property_id">
                        <option value="">Seleccione municipio primero</option>
                    </select>
                </div>

                {{-- Botón agregar --}}
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i> Asignar Propiedad
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body">
            <table id="verticalsTable" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Municipio</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estates as $estate)
                    <tr>
                        <td>{{ $estate->id }}</td>
                        <td>{{ $estate->title }}</td>
                        <td>{{ $estate->municipio->name ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('properties.highlights.update', $estate->id) }}">
                                @csrf
                                @method('PATCH')
                                <select name="order" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $estate->order == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                        </option>
                                        @endfor
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="btn-group">
                                {{-- Ver --}}
                                <a href="https://www.vangoo.mx/details/propiedad/{{ $estate->id }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-info"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('properties.highlights.destroy', $estate->id) }}"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este registro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .filters-container {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .filters-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        color: #2d3748;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 0.4rem;
        color: #4a5568;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background-color: #f9fafb;
        font-size: 0.9rem;
        transition: border 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #3b82f6;
        outline: none;
        background-color: #fff;
    }

    .table-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
        margin-top: 2rem;
    }

    .table-container,
    table,
    tbody,
    tr,
    td {
        overflow: visible !important;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .custom-table thead {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
        color: #374151 !important;
        text-align: left;
    }

    .custom-table thead th {
        padding: 12px 16px !important;
        font-weight: 600 !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 14px !important;
        vertical-align: middle !important;
        border: none !important;
        background-color: #ffffff !important;
        color: #374151 !important;
    }

    .custom-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
        font-size: 13.5px;
    }

    .custom-table tbody tr:hover {
        background-color: #f2f2f2;
        transition: background-color 0.2s ease-in-out;
        cursor: pointer;
    }

    .badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 6px;
        display: inline-block;
        font-weight: bold;
    }

    .badge.active {
        background-color: #daf5dc;
        color: #2e7d32;
    }

    .badge.inactive {
        background-color: #fbdada;
        color: #c62828;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        background: none;
        border: none;
        font-size: 13.5px;
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 170px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
        border-radius: 8px;
        overflow: hidden;
        padding: 5px 0;
    }

    .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        background: none;
        width: 100%;
        border: none;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f2f2f2;
    }

    .dropdown-menu .dropdown-item.text-danger:hover {
        background-color: #ffe6e6;
        color: #d32f2f;
    }

    .dropdown-menu .dropdown-item i {
        margin-right: 8px;
        min-width: 16px;
        text-align: center;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .custom-pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }

    .custom-pagination .custom-page {
        display: flex;
        width: 40px;
        height: 40px;
        background-color: #f3f4f6;
        border-radius: 8px;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        font-size: 0.9rem;
        color: #374151;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }

    .custom-pagination .custom-page a {
        all: unset;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: inherit;
        font-weight: inherit;
        text-decoration: none;
    }

    .custom-pagination .custom-page:hover {
        background-color: #e5e7eb;
    }

    .custom-pagination .custom-page.active {
        background-color: #6366f1;
        color: white;
        font-weight: bold;
    }

    .custom-pagination .custom-page.disabled {
        opacity: 0.4;
        pointer-events: none;
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar DataTable
        let table = $('#verticalsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
            }
        });

        // Filtro por municipio
        $('#filterMunicipio').on('change', function() {
            let municipioId = $(this).val();

            // Filtrar en DataTable
            table.column(2).search(municipioId ? '^' + $('#filterMunicipio option:selected').text() + '$' : '', true, false).draw();

            // Cargar propiedades vía AJAX
            $('#filterProperty').html('<option value="">Cargando...</option>');
            if (municipioId) {
                fetch(`/api/municipios/${municipioId}/properties`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">Seleccione una propiedad</option>';
                        data.forEach(prop => {
                            options += `<option value="${prop.id}">${prop.title}</option>`;
                        });
                        $('#filterProperty').html(options);
                    });
            } else {
                $('#filterProperty').html('<option value="">Seleccione municipio primero</option>');
            }
        });
    });
</script>
@endpush
