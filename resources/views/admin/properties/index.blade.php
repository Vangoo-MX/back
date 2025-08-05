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

<!-- Filtros -->
<div class="card">
    <h2>Filtros y Búsqueda</h2>
    <div class="filters-grid">
        <!-- Búsqueda -->
        <div>
            <label for="search">Buscar por título</label>
            <input type="text" id="search" placeholder="Buscar propiedades..." oninput="applyFilters()" />
        </div>

        <!-- Colonia -->
        <div>
            <label for="colonia">Colonia</label>
            <select id="colonia" onchange="applyFilters()">
                <option value="all">Todas las colonias</option>
                @foreach($coloniasFiltradas as $idColonia)
                <option value="{{ $idColonia }}">{{ limitString(colonia($idColonia), 30) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Municipio -->
        <div>
            <label for="municipio">Municipio</label>
            <select id="municipio" onchange="applyFilters()">
                <option value="all">Todos los municipios</option>
                @foreach($municipiosFiltrados as $idMunicipio)
                <option value="{{ $idMunicipio }}">{{ municipio($idMunicipio) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Estado -->
        <div>
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
<div class="card">
    <h2>Propiedades (<span id="total-results"></span> resultados)</h2>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Precio</th>
                <th>Colonia</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Fecha</th>
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
                <td>{{ limitString($estate->title, 37) }} @if(!$estate->active)<span class="badge">Inactivo</span>@endif</td>
                <td>{{ moneyFormat($estate->price) }}</td>
                <td>{{ limitString(colonia($estate->id_colonia), 30) }}</td>
                <td>{{ municipio($estate->id_municipio) }}</td>
                <td>{{ estado($estate->id_estado) }}</td>
                <td><a href="/user/{{ $estate->id_user }}">{{ username($estate->id_user) }}</a></td>
                <td>{{ convertDate($estate->created_at) }}</td>
                <td>
                    <button onclick="handleAction('details', {{ $estate->id }})">Detalles</button>
                    <button onclick="handleAction('copy', {{ $estate->id }})">Copiar</button>
                    <button onclick="handleAction('toggle', {{ $estate->id }})">{{ $estate->active ? 'Desactivar' : 'Activar' }}</button>
                    <button onclick="handleAction('delete', {{ $estate->id }})">Eliminar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div id="pagination" class="pagination"></div>
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

    // Inicializar
    applyFilters()
</script>

<!-- Estilos básicos -->
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", sans-serif;
        background: #f9f9f9;
        margin: 0;
        padding: 2rem;
    }

    .container {
        max-width: 1200px;
        margin: auto;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .input-icon {
        position: relative;
    }

    .input-icon input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: white;
    }

    .table-wrapper {
        overflow-x: auto;
        margin-top: 1rem;
    }

    .property-table {
        width: 100%;
        border-collapse: collapse;
    }

    .property-table th,
    .property-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e5e5;
        vertical-align: middle;
    }

    .property-table td .badge {
        background: #e4e4e7;
        padding: 2px 6px;
        font-size: 0.75rem;
        border-radius: 6px;
        margin-left: 0.5rem;
    }

    .pagination-container {
        display: flex;
        justify-content: flex-end;
        gap: 0.25rem;
        margin-top: 1rem;
    }

    .pagination-container button {
        padding: 6px 10px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pagination-container button:hover:not(:disabled) {
        background: #f0f0f0;
    }

    .pagination-container button.active {
        background: black;
        color: white;
        font-weight: bold;
        border-color: black;
    }

    .pagination-container button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

@endsection()