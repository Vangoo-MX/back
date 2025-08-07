@extends('layouts.adminLayout')

@section('breadcrumb','Desarrollos')

@section('title','Desarrollos')

@section('titleContent','Desarrollos')

@section('content')

@php
$coloniasFiltradas = $estates->pluck('id_colonia')->unique();
$municipiosFiltrados = $estates->pluck('id_municipio')->unique();
$estadosFiltrados = $estates->pluck('id_estado')->unique();
@endphp

<div class="filters-container">
    <h2 class="filters-title">Filtros y búsqueda</h2>
    <div class="filters-grid">
        <div class="form-group">
            <label for="search">Buscar título</label>
            <input type="text" id="search" placeholder="Buscar propiedades..." oninput="applyFilters()" />
        </div>

        <div class="form-group">
            <label for="colonia">Colonia</label>
            <select id="colonia" onchange="applyFilters()">
                <option value="all">Todas las colonias</option>
                @foreach($coloniasFiltradas as $idColonia)
                <option value="{{ $idColonia }}">{{ limitString(colonia($idColonia), 30) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="municipio">Municipio</label>
            <select id="municipio" onchange="applyFilters()">
                <option value="all">Todos los municipios</option>
                @foreach($municipiosFiltrados as $idMunicipio)
                <option value="{{ $idMunicipio }}">{{ municipio($idMunicipio) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" onchange="applyFilters()">
                <option value="all">Todos los estados</option>
                @foreach($estadosFiltrados as $idEstado)
                <option value="{{ $idEstado }}">{{ estado($idEstado) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

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
                            <a target="_blank" href="https://www.vangoo.mx/details/desarrollo/{{ $estate->id }}" class="dropdown-item">
                                <i class="fas fa-eye me-2"></i> Detalles
                            </a>
                            <a
                                href="{{ route('verticals.edit', $estate->id) }}"
                                class="dropdown-item">
                                <i class="fas fa-edit me-2"></i> Editar
                            </a>
                            <button
                                type="button"
                                class="dropdown-item text-danger"
                                onclick="propertyDeleteSend({{ $estate->id }})"
                                data-url="{{ route('verticals.destroy', $estate->id) }}"
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

<script>
    const rows = Array.from(document.querySelectorAll('.estate-row'))
    const itemsPerPage = 100
    let currentPage = 1

    function applyFilters() {
        const search = document.getElementById('search').value.toLowerCase()
        const colonia = document.getElementById('colonia').value
        const municipio = document.getElementById('municipio').value
        const estado = document.getElementById('estado').value

        const filtered = rows.filter(row => {
            const title = row.dataset.title
            const col = row.dataset.colonia
            const mun = row.dataset.municipio
            const est = row.dataset.estado

            return title.includes(search) &&
                (colonia === 'all' || col === colonia) &&
                (municipio === 'all' || mun === municipio) &&
                (estado === 'all' || est === estado)
        })

        renderTable(filtered)
    }

    function renderTable(filteredRows) {
        const tbody = document.getElementById('estate-table-body')
        tbody.innerHTML = ''

        const total = filteredRows.length
        const totalPages = Math.ceil(total / itemsPerPage)
        const start = (currentPage - 1) * itemsPerPage
        const pageRows = filteredRows.slice(start, start + itemsPerPage)

        pageRows.forEach(row => tbody.appendChild(row))

        document.getElementById('total-results').textContent = total
        renderPagination(totalPages)
    }

    function renderPagination(totalPages) {
        const pagination = document.getElementById('pagination')
        pagination.innerHTML = ''

        if (totalPages <= 1) return

        const prev = document.createElement('button')
        prev.textContent = 'Anterior'
        prev.disabled = currentPage === 1
        prev.onclick = () => {
            currentPage = Math.max(currentPage - 1, 1)
            applyFilters()
        }

        const next = document.createElement('button')
        next.textContent = 'Siguiente'
        next.disabled = currentPage === totalPages
        next.onclick = () => {
            currentPage = Math.min(currentPage + 1, totalPages)
            applyFilters()
        }

        pagination.appendChild(prev)

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button')
            btn.textContent = i
            btn.className = i === currentPage ? 'active' : ''
            btn.onclick = () => {
                currentPage = i
                applyFilters()
            }
            pagination.appendChild(btn)
        }

        pagination.appendChild(next)
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('[data-scope="table-dropdown"] .dropdown-menu').forEach(menu => {
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        if (e.target.matches('[data-scope="table-dropdown"] .dropdown-toggle')) {
            const menu = e.target.nextElementSibling;
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    });

    function propertyDeleteSend(id) {
        const url = $("#propertyDeleteBtn" + id).data("url");

        Swal.fire({
            title: '¿Eliminar propiedad?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'La propiedad ha sido eliminada.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1600);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar la propiedad.',
                            icon: 'error'
                        });
                        console.error('Error en la solicitud. Código de estado: ' + xhr.status);
                    }
                });
            }
        });
    }

    applyFilters()
</script>

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

@endsection()
