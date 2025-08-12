@extends('layouts.adminLayout')

@section('breadcrumb','Apartamentos destacados')

@section('title','Highlights')

@section('titleContent','Apartamentos destacados')

@section('content')

<form method="post" action="{{ route('apartments.highlights.store') }}">
    @csrf
    <div class="filters-container">
        <h5 class="filters-title">Añadir apartamentos destacados</h5>
        <div class="filters-row">
            <div class="form-group">
                <label for="municipio">Municipio</label>
                <select id="municipiosh-select" data-table="#hlTable" name="id_municipio">
                    <option selected value="0" data-municipio-id="0">Todas los municipios destacados</option>
                    @foreach($municipios as $municipio)
                    <option value="{{$municipio->id}}" data-municipio-id="{{$municipio->id}}">{{$municipio->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="properties">Apartamentos destacados</label>
                <div class="input-with-button">
                    <select id="properties-select" name="id_property">
                        <option selected value="0">Seleccione un apartamento...</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="table-container">
    <table class="custom-table w-full text-sm text-left">
        <thead class="custom-header">
            <tr>
                <th>Titulo</th>
                <th>Estado</th>
                <th>Municipio</th>
                <th>Orden</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estates as $estate)
            <tr class="municipio-{{$estate->id_municipio}}">
                <td>{{$estate->apartment->title ?? 'Sin título'}}</td>
                <td>{{ $estate->estado->nombre ?? 'Sin estado' }}</td>
                <td>{{ $estate->municipio->nombre ?? 'Sin municipio' }}</td>
                <td>{{$estate->num_order}}</td>
                <td>
                    <div class="table-actions">
                        <form action="{{ route('apartments.highlights.destroy', $estate->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="table-action-btn orange">
                                <i class="fas fa-trash"></i></button>
                        </form>

                        <a href="https://www.vangoo.mx/detailsDepa/apartments/{{$estate->id}}"
                            target="_blank"
                            class="table-action-btn blue"
                            title="Ver en Vangoo">
                            <i class="fas fa-link"></i>
                        </a>

                        <form id="orden-form{{$estate->id_property}}"
                            action="{{ route('apartments.highlights.update', $estate->id_property) }}"
                            method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="id" value="{{ $estate->id_property }}">
                            <select class="table-action-select"
                                name="num_order"
                                onchange="ordenSelect({{$estate->id_property}})">
                                <option hidden>Orden</option>
                                @for($i = 1; $i <= count($estates); $i++)
                                    <option value="{{ $i }}" {{ $estate->num_order == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                    </option>
                                    @endfor
                            </select>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br><br>

<script>
    const assignUrl = "{{ route('apartments.highlights.store') }}";

    $(document).ready(function() {
        $('document').on('change', '#municipiosh-select', function() {
            var table = $($(this).data('table')).DataTable();
            var municipioId = $(this).val();
            if (municipioId == 0 || municipioId == "") {
                table.column(3).search("").draw();
            } else {
                table.column(3).search(municipioId).draw();
            }
        });

    });

    function ordenSelect(id) {
        console.log(id);
        $('#orden-form' + id).submit();
    }

    document.getElementById('municipiosh-select').addEventListener('change', function() {
        const municipioId = this.value;
        const url = '/apartments/municipio/' + municipioId;

        fetch(url)
            .then(response => response.json())
            .then(properties => {
                const select = document.getElementById('properties-select');
                select.innerHTML = '';

                if (properties.length === 0) {
                    select.innerHTML = '<option value="">No hay propiedades disponibles</option>';
                    const existingBtn = document.getElementById('btn-asignar');
                    if (existingBtn) existingBtn.remove();
                    return;
                }

                properties.forEach(property => {
                    const opt = document.createElement('option');
                    opt.value = property.id;
                    opt.textContent = `${property.id} - ${property.title}`;
                    select.appendChild(opt);
                });

                const existingBtn = document.getElementById('btn-asignar');
                if (existingBtn) existingBtn.remove();

                const btn = document.createElement('button');
                btn.id = 'btn-asignar';
                btn.type = 'button';
                btn.className = 'btn-add';
                btn.textContent = 'Añadir';
                btn.onclick = assignProperty;
                select.insertAdjacentElement('afterend', btn);
            })
            .catch(error => console.error('Error en fetch:', error));
    });

    function assignProperty() {
        const selectedId = document.getElementById('properties-select').value;
        const municipioId = document.getElementById('municipiosh-select').value;

        if (!municipioId || municipioId === "0") {
            Swal.fire({
                title: 'Atención',
                text: 'Debes seleccionar un municipio.',
                icon: 'warning'
            });
            return;
        }

        if (!selectedId || selectedId === "0") {
            Swal.fire({
                title: 'Atención',
                text: 'Debes seleccionar una propiedad.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: '¿Asignar propiedad?',
            text: 'Esta propiedad será marcada como destacada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: assignUrl,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_municipio: municipioId,
                        id_property: selectedId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            title: '¡Asignada!',
                            text: 'La propiedad ha sido asignada correctamente.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1600);
                    },
                    error: function(xhr) {
                        let errorMsg = 'No se pudo asignar la propiedad.';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.id_municipio) {
                                errorMsg += "\n" + xhr.responseJSON.errors.id_municipio.join('\n');
                            }
                        }

                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
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

    .table-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .table-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        border-radius: 4px;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.2s ease;
        height: 32px;
    }

    .table-action-btn.orange {
        background-color: #ffe6e6;
    }

    .table-action-btn.orange:hover {
        color: #d32f2f;
    }

    .table-action-btn.blue {
        background-color: #3498db;
    }

    .table-action-btn.blue:hover {
        background-color: #2980b9;
    }

    .table-action-select {
        height: 32px;
        font-size: 13px;
        border-radius: 4px;
        padding: 0 6px;
    }

    .filters-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }

    .input-with-button {
        display: flex;
        gap: 0.5rem;
    }

    .btn-add {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-add:hover {
        background-color: #2980b9;
    }
</style>

@endsection()
