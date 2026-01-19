{{-- Partial compartido para filtros de propiedades --}}
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
