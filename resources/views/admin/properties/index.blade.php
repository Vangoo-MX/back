@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades')

@section('title','Propiedades')

@section('titleContent','Propiedades publicadas')

@section('content')

<!-- Content Row -->
<style>
    /* Estilos específicos con prefijo para evitar conflictos */
    .em-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .em-space-y-6>*+* {
        margin-top: 24px;
    }

    .em-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .em-card-header {
        padding: 16px 24px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .em-card-title {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1f2937;
    }

    .em-card-content {
        padding: 20px 24px;
    }

    .em-grid {
        display: grid;
        gap: 16px;
    }

    .em-grid-cols-1 {
        grid-template-columns: repeat(1, 1fr);
    }

    @media (min-width: 768px) {
        .em-grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .em-grid-cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .em-space-y-2>*+* {
        margin-top: 8px;
    }

    .em-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #4b5563;
        margin-bottom: 4px;
    }

    .em-relative {
        position: relative;
    }

    .em-input {
        width: 100%;
        padding: 8px 12px 8px 32px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
        background-color: #fff;
    }

    .em-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .em-input-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 16px;
        height: 16px;
    }

    .em-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        background-color: white;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px;
        transition: all 0.2s;
    }

    .em-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .em-table-container {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        overflow-x: auto;
    }

    .em-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .em-table th,
    .em-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    .em-table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }

    .em-table tbody tr:last-child td {
        border-bottom: none;
    }

    .em-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .em-text-right {
        text-align: right;
    }

    .em-text-center {
        text-align: center;
    }

    .em-py-8 {
        padding-top: 32px;
        padding-bottom: 32px;
    }

    .em-text-muted {
        color: #6b7280;
    }

    .em-badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 20px;
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .em-badge-inactive {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .em-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .em-button-link {
        background: none;
        border: none;
        color: #3b82f6;
        padding: 0;
        height: auto;
        font-weight: normal;
    }

    .em-button-link:hover {
        text-decoration: underline;
    }

    .em-button-ghost {
        background: none;
        border: none;
        color: #4b5563;
    }

    .em-button-ghost:hover {
        background-color: #f3f4f6;
    }

    .em-button-outline {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .em-button-outline:hover {
        background-color: #f9fafb;
    }

    .em-button-primary {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #3b82f6;
    }

    .em-button-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .em-button-sm {
        padding: 4px 10px;
        font-size: 13px;
    }

    .em-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .em-dropdown {
        position: relative;
        display: inline-block;
    }

    .em-dropdown-content {
        position: absolute;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        z-index: 100;
        right: 0;
        margin-top: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        display: none;
    }

    .em-dropdown-item {
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }

    .em-dropdown-item:hover {
        background-color: #f3f4f6;
    }

    .em-dropdown-separator {
        height: 1px;
        background-color: #e5e7eb;
        margin: 4px 0;
    }

    .em-text-destructive {
        color: #ef4444;
    }

    .em-pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding: 20px 0 0;
        gap: 16px;
    }

    .em-pagination-info {
        font-size: 14px;
        color: #4b5563;
    }

    .em-pagination-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .em-pagination-pages {
        display: flex;
        gap: 4px;
    }

    .em-icon {
        width: 16px;
        height: 16px;
    }

    .em-flex {
        display: flex;
    }

    .em-items-center {
        align-items: center;
    }

    .em-gap-2 {
        gap: 8px;
    }

    .em-sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    .em-empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .em-empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 16px;
        color: #d1d5db;
    }
</style>
<div class="em-container">
    <div class="em-space-y-6">
        <!-- Tarjeta de Filtros -->
        <div class="em-card">
            <div class="em-card-header">
                <h2 class="em-card-title">
                    <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Filtros y Búsqueda
                </h2>
            </div>
            <div class="em-card-content">
                <div class="em-grid em-grid-cols-1 md:em-grid-cols-2 lg:em-grid-cols-4 em-gap-4">
                    <!-- Búsqueda -->
                    <div class="em-space-y-2">
                        <label class="em-label" for="em-search">Buscar por título</label>
                        <div class="em-relative">
                            <svg class="em-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="em-search" class="em-input" placeholder="Buscar propiedades..." />
                        </div>
                    </div>

                    <!-- Filtro por Colonia -->
                    <div class="em-space-y-2">
                        <label class="em-label">Colonia</label>
                        <select class="em-select em-colonia-filter">
                            <option value="all">Todas las colonias</option>
                            <!-- Opciones se llenarán con JavaScript -->
                        </select>
                    </div>

                    <!-- Filtro por Municipio -->
                    <div class="em-space-y-2">
                        <label class="em-label">Municipio</label>
                        <select class="em-select em-municipio-filter">
                            <option value="all">Todos los municipios</option>
                            <!-- Opciones se llenarán con JavaScript -->
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="em-space-y-2">
                        <label class="em-label">Estado</label>
                        <select class="em-select em-estado-filter">
                            <option value="all">Todos los estados</option>
                            <!-- Opciones se llenarán con JavaScript -->
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Propiedades -->
        <div class="em-card">
            <div class="em-card-header">
                <h2 class="em-card-title">Propiedades (<span id="em-results-count">0</span> resultados)</h2>
            </div>
            <div class="em-card-content">
                <div class="em-table-container">
                    <table class="em-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Precio</th>
                                <th>Colonia</th>
                                <th>Municipio</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th class="em-text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="em-estates-table-body">
                            <!-- Datos se llenarán con JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div id="em-pagination-container" class="em-pagination">
                    <!-- Contenido se llenará con JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos de ejemplo
    const mockEstates = [{
            id: 1,
            title: "Casa moderna en zona residencial",
            price: 2500000,
            id_colonia: 1,
            id_municipio: 1,
            id_estado: 1,
            id_user: 1,
            created_at: "2023-10-15",
            active: true
        },
        {
            id: 2,
            title: "Departamento con vista al mar",
            price: 1800000,
            id_colonia: 2,
            id_municipio: 1,
            id_estado: 1,
            id_user: 2,
            created_at: "2023-09-22",
            active: false
        },
        {
            id: 3,
            title: "Terreno comercial en avenida principal",
            price: 3500000,
            id_colonia: 3,
            id_municipio: 2,
            id_estado: 2,
            id_user: 3,
            created_at: "2023-11-05",
            active: true
        },
        {
            id: 4,
            title: "Casa de campo con amplio jardín",
            price: 3200000,
            id_colonia: 4,
            id_municipio: 3,
            id_estado: 3,
            id_user: 4,
            created_at: "2023-08-30",
            active: true
        },
        {
            id: 5,
            title: "Loft en zona industrial renovada",
            price: 1250000,
            id_colonia: 1,
            id_municipio: 1,
            id_estado: 1,
            id_user: 1,
            created_at: "2023-11-18",
            active: true
        },
        {
            id: 6,
            title: "Penthouse de lujo con terraza",
            price: 4800000,
            id_colonia: 2,
            id_municipio: 1,
            id_estado: 1,
            id_user: 2,
            created_at: "2023-07-12",
            active: true
        },
        {
            id: 7,
            title: "Oficinas en edificio corporativo",
            price: 2200000,
            id_colonia: 3,
            id_municipio: 2,
            id_estado: 2,
            id_user: 3,
            created_at: "2023-10-01",
            active: false
        },
        {
            id: 8,
            title: "Bodega en zona logística",
            price: 1900000,
            id_colonia: 4,
            id_municipio: 3,
            id_estado: 3,
            id_user: 4,
            created_at: "2023-09-05",
            active: true
        },
    ];

    const colonias = {
        1: "Centro Histórico",
        2: "Zona Dorada",
        3: "Valle de las Flores",
        4: "Lomas Verdes"
    };

    const municipios = {
        1: "Ciudad Capital",
        2: "Valle del Sol",
        3: "Costa Azul"
    };

    const estados = {
        1: "Estado Norte",
        2: "Estado Sur",
        3: "Estado Este"
    };

    const users = {
        1: "Ana Martínez",
        2: "Carlos Rodríguez",
        3: "Luisa Fernández",
        4: "Miguel Sánchez"
    };

    // Funciones de utilidad
    function limitString(str, maxLength) {
        return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
    }

    function moneyFormat(amount) {
        return '$' + amount.toLocaleString('es-MX');
    }

    function convertDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-MX');
    }

    // Estado de la aplicación
    let state = {
        searchTerm: "",
        selectedColonia: "all",
        selectedMunicipio: "all",
        selectedEstado: "all",
        currentPage: 1,
        itemsPerPage: 5
    };

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        // Llenar selects
        fillSelect('.em-colonia-filter', colonias);
        fillSelect('.em-municipio-filter', municipios);
        fillSelect('.em-estado-filter', estados);

        // Configurar eventos
        document.getElementById('em-search').addEventListener('input', handleSearch);
        document.querySelector('.em-colonia-filter').addEventListener('change', handleFilterChange);
        document.querySelector('.em-municipio-filter').addEventListener('change', handleFilterChange);
        document.querySelector('.em-estado-filter').addEventListener('change', handleFilterChange);

        // Renderizar vista inicial
        renderView();
    });

    // Funciones de ayuda
    function fillSelect(selector, data) {
        const select = document.querySelector(selector);
        Object.entries(data).forEach(([id, name]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            select.appendChild(option);
        });
    }

    function handleSearch(e) {
        state.searchTerm = e.target.value;
        state.currentPage = 1;
        renderView();
    }

    function handleFilterChange() {
        state.selectedColonia = document.querySelector('.em-colonia-filter').value;
        state.selectedMunicipio = document.querySelector('.em-municipio-filter').value;
        state.selectedEstado = document.querySelector('.em-estado-filter').value;
        state.currentPage = 1;
        renderView();
    }

    function handleAction(action, estateId) {
        switch (action) {
            case "details":
                alert(`Ver detalles de propiedad ${estateId}`);
                break;
            case "copy":
                navigator.clipboard.writeText(`${window.location.origin}/property/${estateId}`)
                    .then(() => alert(`Link copiado para propiedad ${estateId}`))
                    .catch(err => console.error('Error al copiar:', err));
                break;
            case "toggle":
                // Encontrar la propiedad y cambiar su estado
                const estateIndex = mockEstates.findIndex(e => e.id === estateId);
                if (estateIndex !== -1) {
                    mockEstates[estateIndex].active = !mockEstates[estateIndex].active;
                    renderView();
                    alert(`Propiedad ${estateId} ${mockEstates[estateIndex].active ? 'activada' : 'desactivada'}`);
                }
                break;
            case "delete":
                if (confirm(`¿Estás seguro de que quieres eliminar la propiedad ${estateId}?`)) {
                    const estateIndex = mockEstates.findIndex(e => e.id === estateId);
                    if (estateIndex !== -1) {
                        mockEstates.splice(estateIndex, 1);
                        renderView();
                        alert(`Propiedad ${estateId} eliminada`);
                    }
                }
                break;
        }
    }

    function handlePageChange(page) {
        state.currentPage = page;
        renderView();
    }

    function handlePrevPage() {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderView();
        }
    }

    function handleNextPage(totalPages) {
        if (state.currentPage < totalPages) {
            state.currentPage++;
            renderView();
        }
    }

    // Función principal para renderizar la vista
    function renderView() {
        // Filtrar propiedades
        const filteredEstates = mockEstates.filter(estate => {
            const matchesSearch = estate.title.toLowerCase().includes(state.searchTerm.toLowerCase());
            const matchesColonia = state.selectedColonia === "all" || estate.id_colonia.toString() === state.selectedColonia;
            const matchesMunicipio = state.selectedMunicipio === "all" || estate.id_municipio.toString() === state.selectedMunicipio;
            const matchesEstado = state.selectedEstado === "all" || estate.id_estado.toString() === state.selectedEstado;

            return matchesSearch && matchesColonia && matchesMunicipio && matchesEstado;
        });

        // Actualizar contador de resultados
        document.getElementById('em-results-count').textContent = filteredEstates.length;

        // Paginación
        const totalPages = Math.ceil(filteredEstates.length / state.itemsPerPage);
        const startIndex = (state.currentPage - 1) * state.itemsPerPage;
        const paginatedEstates = filteredEstates.slice(startIndex, startIndex + state.itemsPerPage);

        // Renderizar tabla
        const tableBody = document.getElementById('em-estates-table-body');
        tableBody.innerHTML = '';

        if (paginatedEstates.length === 0) {
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="8">
                            <div class="em-empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>No se encontraron propiedades con los filtros aplicados</p>
                            </div>
                        </td>
                    </tr>
                `;
        } else {
            paginatedEstates.forEach(estate => {
                const row = document.createElement('tr');

                // Título con posible badge de inactivo
                const titleCell = document.createElement('td');
                titleCell.innerHTML = `
                        <div class="em-flex em-items-center em-gap-2">
                            <div>${limitString(estate.title, 37)}</div>
                            ${!estate.active ? '<span class="em-badge em-badge-inactive">Inactivo</span>' : ''}
                        </div>
                    `;

                // Precio
                const priceCell = document.createElement('td');
                priceCell.textContent = moneyFormat(estate.price);

                // Colonia, Municipio, Estado
                const coloniaCell = document.createElement('td');
                coloniaCell.textContent = limitString(colonias[estate.id_colonia], 30);

                const municipioCell = document.createElement('td');
                municipioCell.textContent = municipios[estate.id_municipio];

                const estadoCell = document.createElement('td');
                estadoCell.textContent = estados[estate.id_estado];

                // Usuario
                const userCell = document.createElement('td');
                userCell.innerHTML = `<button class="em-button em-button-link">${users[estate.id_user]}</button>`;

                // Fecha
                const dateCell = document.createElement('td');
                dateCell.textContent = convertDate(estate.created_at);

                // Acciones
                const actionsCell = document.createElement('td');
                actionsCell.className = "em-text-right";
                actionsCell.innerHTML = `
                        <div class="em-dropdown">
                            <button class="em-button em-button-ghost" id="em-dropdown-trigger-${estate.id}">
                                <span class="em-sr-only">Abrir menú</span>
                                <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="12" cy="5" r="1"></circle>
                                    <circle cx="12" cy="19" r="1"></circle>
                                </svg>
                            </button>
                            <div class="em-dropdown-content" id="em-dropdown-content-${estate.id}">
                                <div class="em-dropdown-item" data-action="details" data-id="${estate.id}">
                                    <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Detalles
                                </div>
                                <div class="em-dropdown-item" data-action="copy" data-id="${estate.id}">
                                    <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Copiar link
                                </div>
                                <div class="em-dropdown-separator"></div>
                                <div class="em-dropdown-item" data-action="toggle" data-id="${estate.id}">
                                    <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                    </svg>
                                    ${estate.active ? 'Desactivar' : 'Activar'}
                                </div>
                                <div class="em-dropdown-item em-text-destructive" data-action="delete" data-id="${estate.id}">
                                    <svg class="em-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Borrar
                                </div>
                            </div>
                        </div>
                    `;

                // Construir fila
                row.appendChild(titleCell);
                row.appendChild(priceCell);
                row.appendChild(coloniaCell);
                row.appendChild(municipioCell);
                row.appendChild(estadoCell);
                row.appendChild(userCell);
                row.appendChild(dateCell);
                row.appendChild(actionsCell);

                tableBody.appendChild(row);

                // Configurar evento para mostrar menú desplegable
                const trigger = document.getElementById(`em-dropdown-trigger-${estate.id}`);
                const content = document.getElementById(`em-dropdown-content-${estate.id}`);

                if (trigger && content) {
                    trigger.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Cerrar todos los menús antes de abrir este
                        document.querySelectorAll('.em-dropdown-content').forEach(el => {
                            if (el.id !== content.id) el.style.display = 'none';
                        });

                        content.style.display = content.style.display === 'none' || !content.style.display ? 'block' : 'none';
                    });

                    // Configurar acciones del menú
                    content.querySelectorAll('.em-dropdown-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const action = this.getAttribute('data-action');
                            const id = this.getAttribute('data-id');
                            handleAction(action, parseInt(id));
                            content.style.display = 'none';
                        });
                    });
                }
            });

            // Cerrar menús al hacer clic en cualquier lugar
            document.addEventListener('click', function() {
                document.querySelectorAll('.em-dropdown-content').forEach(el => {
                    el.style.display = 'none';
                });
            });
        }

        // Renderizar paginación
        renderPagination(filteredEstates.length, totalPages, startIndex);
    }

    function renderPagination(totalItems, totalPages, startIndex) {
        const paginationContainer = document.getElementById('em-pagination-container');
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const paginationHTML = `
                <div class="em-pagination-info">
                    Mostrando ${startIndex + 1} a ${Math.min(startIndex + state.itemsPerPage, totalItems)} de ${totalItems} resultados
                </div>
                <div class="em-pagination-buttons">
                    <button class="em-button em-button-outline em-button-sm" id="em-prev-page" ${state.currentPage === 1 ? 'disabled' : ''}>
                        Anterior
                    </button>
                    <div class="em-pagination-pages">
                        ${Array.from({ length: totalPages }, (_, i) => i + 1).map(page => ` <
            button class = "em-button em-button-sm ${state.currentPage === page ? 'em-button-primary' : 'em-button-outline'}"
        data - page = "${page}" >
            $ {
                page
            } <
            /button>
        `).join('')}
                    </div>
                    <button class="em-button em-button-outline em-button-sm" id="em-next-page" ${state.currentPage === totalPages ? 'disabled' : ''}>
                        Siguiente
                    </button>
                </div>
            `;

        paginationContainer.innerHTML = paginationHTML;

        // Configurar eventos de paginación
        document.getElementById('em-prev-page').addEventListener('click', handlePrevPage);
        document.getElementById('em-next-page').addEventListener('click', () => handleNextPage(totalPages));

        document.querySelectorAll('.em-pagination-pages button').forEach(button => {
            button.addEventListener('click', function() {
                const page = parseInt(this.getAttribute('data-page'));
                handlePageChange(page);
            });
        });
    }
</script>
@endsection