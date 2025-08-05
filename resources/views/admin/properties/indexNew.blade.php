@extends('layouts.adminLayout')

@section('breadcrumb','Propiedades')

@section('title','Propiedades')

@section('titleContent','Propiedades publicadas')

@section('content')

<!-- Content Row -->
<style>
    /* Reset básico y estilos globales */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    body {
        background-color: #f5f5f5;
        color: #333;
        padding: 20px;
        line-height: 1.5;
    }

    /* Estilos para contenedores y tarjetas */
    .space-y-6>*+* {
        margin-top: 24px;
    }

    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px 0;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-content {
        padding: 20px 24px 24px;
    }

    /* Estilos para formulario y filtros */
    .grid {
        display: grid;
        gap: 16px;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, 1fr);
    }

    @media (min-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .grid-cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .space-y-2>*+* {
        margin-top: 8px;
    }

    label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #444;
    }

    .relative {
        position: relative;
    }

    .input {
        width: 100%;
        padding: 8px 12px 8px 32px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .input-icon {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 16px;
        height: 16px;
    }

    .select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        background-color: white;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 16px;
    }

    .select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    /* Estilos para la tabla */
    .table-container {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 14px;
        color: #4b5563;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .py-8 {
        padding-top: 32px;
        padding-bottom: 32px;
    }

    .text-muted-foreground {
        color: #6b7280;
    }

    /* Badge para estado inactivo */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 4px;
        background-color: #e5e7eb;
        color: #4b5563;
    }

    /* Botones */
    .button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
        border: 1px solid transparent;
    }

    .button-variant-link {
        background: none;
        border: none;
        color: #3b82f6;
        padding: 0;
        height: auto;
        font-weight: normal;
        text-decoration: underline;
    }

    .button-variant-ghost {
        background: none;
        border: none;
        color: #4b5563;
    }

    .button-variant-ghost:hover {
        background-color: #f3f4f6;
    }

    .button-variant-outline {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .button-variant-outline:hover {
        background-color: #f9fafb;
    }

    .button-variant-default {
        background-color: #3b82f6;
        color: white;
    }

    .button-variant-default:hover {
        background-color: #2563eb;
    }

    .button-size-sm {
        padding: 4px 8px;
        font-size: 13px;
    }

    .button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Menú desplegable */
    .dropdown-menu {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        position: absolute;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 6px;
        z-index: 100;
        right: 0;
        margin-top: 4px;
        overflow: hidden;
    }

    .dropdown-item {
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }

    .dropdown-separator {
        height: 1px;
        background-color: #e5e7eb;
        margin: 4px 0;
    }

    .text-destructive {
        color: #ef4444;
    }

    /* Paginación */
    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-pages {
        display: flex;
        gap: 4px;
    }

    .icon {
        width: 16px;
        height: 16px;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .gap-2 {
        gap: 8px;
    }

    .sr-only {
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
</style>
<div class="space-y-6">
    <!-- Tarjeta de Filtros -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filtros y Búsqueda
            </h2>
        </div>
        <div class="card-content">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div class="space-y-2">
                    <label for="search">Buscar por título</label>
                    <div class="relative">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="search" class="input" placeholder="Buscar propiedades..." />
                    </div>
                </div>

                <!-- Filtro por Colonia -->
                <div class="space-y-2">
                    <label>Colonia</label>
                    <select class="select colonia-filter">
                        <option value="all">Todas las colonias</option>
                        <!-- Opciones se llenarán con JavaScript -->
                    </select>
                </div>

                <!-- Filtro por Municipio -->
                <div class="space-y-2">
                    <label>Municipio</label>
                    <select class="select municipio-filter">
                        <option value="all">Todos los municipios</option>
                        <!-- Opciones se llenarán con JavaScript -->
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div class="space-y-2">
                    <label>Estado</label>
                    <select class="select estado-filter">
                        <option value="all">Todos los estados</option>
                        <!-- Opciones se llenarán con JavaScript -->
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Propiedades -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Propiedades (<span id="results-count">0</span> resultados)</h2>
        </div>
        <div class="card-content">
            <div class="table-container">
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
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="estates-table-body">
                        <!-- Datos se llenarán con JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div id="pagination-container" class="pagination">
                <!-- Contenido se llenará con JavaScript -->
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
        fillSelect('.colonia-filter', colonias);
        fillSelect('.municipio-filter', municipios);
        fillSelect('.estado-filter', estados);

        // Configurar eventos
        document.getElementById('search').addEventListener('input', handleSearch);
        document.querySelector('.colonia-filter').addEventListener('change', handleFilterChange);
        document.querySelector('.municipio-filter').addEventListener('change', handleFilterChange);
        document.querySelector('.estado-filter').addEventListener('change', handleFilterChange);

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
        state.currentPage = 1; // Resetear a la primera página
        renderView();
    }

    function handleFilterChange() {
        state.selectedColonia = document.querySelector('.colonia-filter').value;
        state.selectedMunicipio = document.querySelector('.municipio-filter').value;
        state.selectedEstado = document.querySelector('.estado-filter').value;
        state.currentPage = 1; // Resetear a la primera página
        renderView();
    }

    function handleAction(action, estateId) {
        switch (action) {
            case "details":
                alert(`Ver detalles de propiedad ${estateId}`);
                break;
            case "copy":
                navigator.clipboard.writeText(`${window.location.origin}/property/${estateId}`);
                alert(`Link copiado para propiedad ${estateId}`);
                break;
            case "toggle":
                alert(`Activar/Desactivar propiedad ${estateId}`);
                break;
            case "delete":
                if (confirm(`¿Estás seguro de que quieres eliminar la propiedad ${estateId}?`)) {
                    alert(`Propiedad ${estateId} eliminada`);
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
        document.getElementById('results-count').textContent = filteredEstates.length;

        // Paginación
        const totalPages = Math.ceil(filteredEstates.length / state.itemsPerPage);
        const startIndex = (state.currentPage - 1) * state.itemsPerPage;
        const paginatedEstates = filteredEstates.slice(startIndex, startIndex + state.itemsPerPage);

        // Renderizar tabla
        const tableBody = document.getElementById('estates-table-body');
        tableBody.innerHTML = '';

        if (paginatedEstates.length === 0) {
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-8 text-muted-foreground">
                            No se encontraron propiedades con los filtros aplicados
                        </td>
                    </tr>
                `;
        } else {
            paginatedEstates.forEach(estate => {
                const row = document.createElement('tr');

                // Título con posible badge de inactivo
                const titleCell = document.createElement('td');
                titleCell.className = 'font-medium';
                titleCell.innerHTML = `
                        <div class="flex items-center gap-2">
                            ${limitString(estate.title, 37)}
                            ${!estate.active ? '<span class="badge">Inactivo</span>' : ''}
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
                userCell.innerHTML = `<button class="button button-variant-link">${users[estate.id_user]}</button>`;

                // Fecha
                const dateCell = document.createElement('td');
                dateCell.textContent = convertDate(estate.created_at);

                // Acciones
                const actionsCell = document.createElement('td');
                actionsCell.className = 'text-right';
                actionsCell.innerHTML = `
                        <div class="dropdown-menu">
                            <button class="button button-variant-ghost" id="dropdown-trigger-${estate.id}">
                                <span class="sr-only">Abrir menú</span>
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="12" cy="5" r="1"></circle>
                                    <circle cx="12" cy="19" r="1"></circle>
                                </svg>
                            </button>
                            <div class="dropdown-content" id="dropdown-content-${estate.id}" style="display: none;">
                                <div class="dropdown-item" data-action="details" data-id="${estate.id}">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Detalles
                                </div>
                                <div class="dropdown-item" data-action="copy" data-id="${estate.id}">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Copiar link
                                </div>
                                <div class="dropdown-separator"></div>
                                <div class="dropdown-item" data-action="toggle" data-id="${estate.id}">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                    </svg>
                                    ${estate.active ? 'Desactivar' : 'Activar'}
                                </div>
                                <div class="dropdown-item text-destructive" data-action="delete" data-id="${estate.id}">
                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                const trigger = document.getElementById(`dropdown-trigger-${estate.id}`);
                const content = document.getElementById(`dropdown-content-${estate.id}`);

                if (trigger && content) {
                    trigger.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Cerrar todos los menús antes de abrir este
                        document.querySelectorAll('.dropdown-content').forEach(el => {
                            if (el.id !== content.id) el.style.display = 'none';
                        });

                        content.style.display = content.style.display === 'none' || !content.style.display ? 'block' : 'none';
                    });

                    // Configurar acciones del menú
                    content.querySelectorAll('.dropdown-item').forEach(item => {
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
                document.querySelectorAll('.dropdown-content').forEach(el => {
                    el.style.display = 'none';
                });
            });
        }

        // Renderizar paginación
        renderPagination(filteredEstates.length, totalPages, startIndex);
    }

    function renderPagination(totalItems, totalPages, startIndex) {
        const paginationContainer = document.getElementById('pagination-container');
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const paginationHTML = `
                <div>
                    <div class="text-sm text-muted-foreground">
                        Mostrando ${startIndex + 1} a ${Math.min(startIndex + state.itemsPerPage, totalItems)} de ${totalItems} resultados
                    </div>
                </div>
                <div class="pagination-buttons">
                    <button class="button button-variant-outline button-size-sm" id="prev-page" ${state.currentPage === 1 ? 'disabled' : ''}>
                        Anterior
                    </button>
                    <div class="pagination-pages">
                        ${Array.from({ length: totalPages }, (_, i) => i + 1).map(page => ` <
            button class = "button ${state.currentPage === page ? 'button-variant-default' : 'button-variant-outline'} button-size-sm"
        data - page = "${page}" >
            $ {
                page
            } <
            /button>
        `).join('')}
                    </div>
                    <button class="button button-variant-outline button-size-sm" id="next-page" ${state.currentPage === totalPages ? 'disabled' : ''}>
                        Siguiente
                    </button>
                </div>
            `;

        paginationContainer.innerHTML = paginationHTML;

        // Configurar eventos de paginación
        document.getElementById('prev-page').addEventListener('click', handlePrevPage);
        document.getElementById('next-page').addEventListener('click', () => handleNextPage(totalPages));

        document.querySelectorAll('.pagination-pages button').forEach(button => {
            button.addEventListener('click', function() {
                const page = parseInt(this.getAttribute('data-page'));
                handlePageChange(page);
            });
        });
    }
</script>


@endsection()
