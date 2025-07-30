async function cargarRestaurantes() {
    const res = await fetch('/api/restaurantes');
    const data = await res.json();
    const tbody = document.getElementById('lista-restaurantes');
    tbody.innerHTML = '';

    data.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
                    <td>${r.id}</td>
                    <td><span>${r.nombre}</span><input type="text" value="${r.nombre}" style="display:none;"></td>
                    <td><span>${r.direccion}</span><input type="text" value="${r.direccion}" style="display:none;"></td>
                    <td><span>${r.telefono}</span><input type="text" value="${r.telefono}" style="display:none;"></td>
                    <td class="acciones">
                        <button class="edit" onclick="editarFila(this)">Editar</button>
                        <button class="save" style="display:none;" onclick="guardarEdicion(this, ${r.id})">Guardar</button>
                        <button class="cancel" style="display:none;" onclick="cancelarEdicion(this)">Cancelar</button>
                        <button class="delete" onclick="eliminarRestaurante(${r.id})">Eliminar</button>
                    </td>
                `;
        tbody.appendChild(tr);
    });
}

async function buscarRestaurantePorId() {
    const id = document.getElementById('buscar-id').value.trim();
    const resultadoDiv = document.getElementById('resultado-busqueda');
    resultadoDiv.innerHTML = '';

    if (!id) {
        resultadoDiv.innerHTML = '<p style="color: red;">Por favor, introduce un ID válido.</p>';
        return;
    }

    try {
        const response = await fetch(`/api/restaurantes/${id}`);
        if (!response.ok) {
            if (response.status === 404) {
                resultadoDiv.innerHTML = `<p style="color: red;">Restaurante con ID ${id} no encontrado.</p>`;
            } else {
                resultadoDiv.innerHTML = `<p style="color: red;">Error al buscar el restaurante. Código: ${response.status}</p>`;
            }
            return;
        }

        const restaurante = await response.json();

        resultadoDiv.innerHTML = `
            <p><strong>ID:</strong> ${restaurante.id}</p>
            <p><strong>Nombre:</strong> ${restaurante.nombre}</p>
            <p><strong>Dirección:</strong> ${restaurante.direccion}</p>
            <p><strong>Teléfono:</strong> ${restaurante.telefono}</p>
        `;

    } catch (error) {
        resultadoDiv.innerHTML = `<p style="color: red;">Error en la conexión: ${error.message}</p>`;
    }
}

async function crearRestaurante() {
    const nombre = document.getElementById('nombre').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const telefono = document.getElementById('telefono').value.trim();

    if (!nombre || !direccion || !telefono) {
        alert("Todos los campos son obligatorios.");
        return;
    }

    const res = await fetch('/api/restaurantes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, direccion, telefono })
    });

    if (res.ok) {
        alert('Restaurante creado');
        document.getElementById('nombre').value = '';
        document.getElementById('direccion').value = '';
        document.getElementById('telefono').value = '';
        cargarRestaurantes();
    } else {
        alert('Error al crear');
    }
}

async function eliminarRestaurante(id) {
    if (!confirm("¿Estás seguro de eliminar este restaurante?")) return;

    const res = await fetch('/api/restaurantes/' + id, {
        method: 'DELETE'
    });

    if (res.ok) {
        alert('Eliminado correctamente');
        cargarRestaurantes();
    } else {
        alert('Error al eliminar');
    }
}

function editarFila(btn) {
    const row = btn.closest('tr');
    row.querySelectorAll('span').forEach(span => span.style.display = 'none');
    row.querySelectorAll('input').forEach(input => input.style.display = 'inline');
    row.querySelector('.edit').style.display = 'none';
    row.querySelector('.save').style.display = 'inline';
    row.querySelector('.cancel').style.display = 'inline';
}

function cancelarEdicion(btn) {
    const row = btn.closest('tr');
    const spans = row.querySelectorAll('span');
    const inputs = row.querySelectorAll('input');

    inputs.forEach((input, i) => {
        input.value = spans[i].textContent;
        input.style.display = 'none';
    });

    spans.forEach(span => span.style.display = 'inline');
    row.querySelector('.save').style.display = 'none';
    row.querySelector('.cancel').style.display = 'none';
    row.querySelector('.edit').style.display = 'inline';
}

async function guardarEdicion(btn, id) {
    const row = btn.closest('tr');
    const inputs = row.querySelectorAll('input');
    const nombre = inputs[0].value.trim();
    const direccion = inputs[1].value.trim();
    const telefono = inputs[2].value.trim();

    if (!nombre || !direccion || !telefono) {
        alert("Todos los campos son obligatorios.");
        return;
    }

    const res = await fetch('/api/restaurantes/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, direccion, telefono })
    });

    if (res.ok) {
        alert('Restaurante actualizado');
        cargarRestaurantes();
    } else {
        alert('Error al actualizar');
    }
}

cargarRestaurantes();