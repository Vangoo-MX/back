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

    const totalResultsElement = document.getElementById('total-results')
    if (totalResultsElement) {
        totalResultsElement.textContent = total
    }

    renderPagination(totalPages)
}

function renderPagination(totalPages) {
    const pagination = document.getElementById('pagination')
    if (!pagination) return

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

document.addEventListener('click', function (e) {
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

function propertyActiveSend(id) {
    const url = $("#propertyActivateConfirmBtn" + id).data("url");

    Swal.fire({
        title: '¿Activar propiedad?',
        text: 'La propiedad será visible en el sitio.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "PUT",
                data: {
                    _token: csrfToken
                },
                dataType: 'json',
                success: function (response) {
                    Swal.fire({
                        title: '¡Activada!',
                        text: 'La propiedad ahora está activa.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 1600);
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo activar la propiedad.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function propertyDeactiveSend(id) {
    const url = $("#propertyDeactiveConfirmBtn" + id).data("url");

    Swal.fire({
        title: '¿Desactivar propiedad?',
        text: 'La propiedad dejará de ser visible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "PUT",
                data: {
                    _token: csrfToken
                },
                dataType: 'json',
                success: function (response) {
                    Swal.fire({
                        title: '¡Desactivada!',
                        text: 'La propiedad ha sido desactivada.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 1600);
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo desactivar la propiedad.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

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
                    _token: csrfToken
                },
                dataType: 'json',
                success: function (response) {
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
                error: function (xhr) {
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
