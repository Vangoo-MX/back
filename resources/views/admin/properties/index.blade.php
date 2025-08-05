@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades')

@section('title','Propiedades')

@section('titleContent','Propiedades publicadas')

@section('content')

<!-- Filtros y búsqueda -->
@php
$coloniasFiltradas = $estates->pluck('id_colonia')->unique();
$municipiosFiltrados = $estates->pluck('id_municipio')->unique();
$estadosFiltrados = $estates->pluck('id_estado')->unique();
@endphp

<div class="filters-container">
    <h2 class="filters-title">Filtros y búsqueda</h2>
    <div class="filters-grid">
        <!-- Buscar por título -->
        <div class="form-group">
            <label for="search">Buscar título</label>
            <input type="text" id="search" placeholder="Buscar propiedades..." oninput="applyFilters()" />
        </div>

        <!-- Colonia -->
        <div class="form-group">
            <label for="colonia">Colonia</label>
            <select id="colonia" onchange="applyFilters()">
                <option value="all">Todas las colonias</option>
                @foreach($coloniasFiltradas as $idColonia)
                <option value="{{ $idColonia }}">{{ limitString(colonia($idColonia), 30) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Municipio -->
        <div class="form-group">
            <label for="municipio">Municipio</label>
            <select id="municipio" onchange="applyFilters()">
                <option value="all">Todos los municipios</option>
                @foreach($municipiosFiltrados as $idMunicipio)
                <option value="{{ $idMunicipio }}">{{ municipio($idMunicipio) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Estado -->
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

<!-- Tabla de propiedades -->
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
        <tbody>
            @foreach($estates as $estate)
            <tr>
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
                <td>{{ $estate->user->name }}</td>
                <td>
                    <div class="dropdown">
                        <button class="dropdown-toggle">Opciones</button>
                        <div class="dropdown-menu">
                            <a target="_blank" href="https://www.vangoo.mx/details/propiedad/{{ $estate->id }}">Detalles</a>
                            <a href="#">Copiar</a>
                            <a href="#">{{ $estate->status === 1 ? 'Inactivar' : 'Activar' }}</a>
                            <a href="#" class="danger">Eliminar</a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Scripts -->
<script>
    const rows = Array.from(document.querySelectorAll('.estate-row'))
    const itemsPerPage = 5
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

    function handleAction(action, id) {
        switch (action) {
            case 'details':
                alert(`Ver detalles de propiedad ${id}`)
                break
            case 'copy':
                navigator.clipboard.writeText(`${window.location.origin}/property/${id}`)
                alert(`Link copiado para propiedad ${id}`)
                break
            case 'toggle':
                alert(`Activar/Desactivar propiedad ${id}`)
                break
            case 'delete':
                if (confirm('¿Seguro que deseas eliminar esta propiedad?')) {
                    alert(`Eliminar propiedad ${id}`)
                }
                break
        }
    }

    document.addEventListener('click', function(e) {
        // Cerrar cualquier otro menú abierto
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // Mostrar el menú si se hace clic en el botón
        if (e.target.matches('.dropdown-toggle')) {
            const menu = e.target.nextElementSibling;
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    });

    // Inicializar
    applyFilters()
</script>

<!-- Estilos básicos -->
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
        min-width: 150px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
        border-radius: 8px;
        overflow: hidden;
    }

    .dropdown-menu a {
        padding: 10px 15px;
        display: block;
        color: #333;
        text-decoration: none;
        font-size: 14px;
    }

    .dropdown-menu a:hover {
        background-color: #f2f2f2;
    }

    .dropdown-menu a.danger:hover {
        background-color: #ffe6e6;
        color: #d32f2f;
    }
</style>

@endsection()