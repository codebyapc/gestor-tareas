// JavaScript para Dashboard de TaskFlow
// Maneja AJAX para CRUD sin recarga

// Variables de configuración desde PHP
const API_URL = window.API_URL || '/gestor-tareas/api';

console.log('dashboard.js cargado correctamente');
console.log('API_URL:', API_URL);

// Toast notification function
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    loadProjects();
    loadStatistics();

    // Search functionality
    const searchInput = document.getElementById('task-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const tasks = document.querySelectorAll('.task');
            tasks.forEach(task => {
                const title = task.querySelector('h4').textContent.toLowerCase();
                const desc = task.querySelector('p').textContent.toLowerCase();
                if (title.includes(query) || desc.includes(query)) {
                    task.style.display = 'block';
                } else {
                    task.style.display = 'none';
                }
            });
        });
    }

    // Modal para nuevo proyecto
    const modal = document.getElementById('project-modal');
    const btn = document.getElementById('new-project-btn');
    const span = document.querySelector('.close');

    btn.onclick = function() {
        modal.style.display = 'block';
    }

    span.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Formulario de proyecto manejado por onclick en el botón

    // Modal para nueva tarea
    const taskModal = document.getElementById('task-modal');
    const taskSpan = taskModal.querySelector('.close');

    taskSpan.onclick = function() {
        taskModal.style.display = 'none';
        // Limpiar formulario al cerrar
        clearTaskForm();
    }

    window.onclick = function(event) {
        if (event.target == taskModal) {
            taskModal.style.display = 'none';
            // Limpiar formulario al cerrar
            clearTaskForm();
        }
    }

    // Formulario de tarea - solo submit, no click en botón
    document.getElementById('task-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Formulario enviado, previniendo default');

        // Verificar si ya se está procesando
        if (this.dataset.processing === 'true') {
            console.log('Ya se está procesando, ignorando envío duplicado');
            return;
        }
        this.dataset.processing = 'true';

        try {
            const button = document.querySelector('#task-form button');
            const mode = button.dataset.mode;
            const taskId = button.dataset.taskId;

            if (mode === 'update' && taskId) {
                await updateTask(taskId);
            } else {
                const proyectoId = document.querySelector('#new-task-btn').dataset.proyectoId;
                if (proyectoId) {
                    await createTask(proyectoId);
                } else {
                    showToast('Selecciona un proyecto primero', 'error');
                }
            }
        } finally {
            // Limpiar el flag de procesamiento
            this.dataset.processing = 'false';
        }
    });
});

// Cargar proyectos
function loadProjects() {
    fetch(`${API_URL}/proyectos`)
        .then(async response => {
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                if (!response.ok) throw new Error(data.error || 'Error desconocido');
                if (Array.isArray(data)) {
                    const list = document.getElementById('projects-list');
                    list.innerHTML = '';
                    data.forEach(project => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <span onclick="loadTasks(${project.id})">${project.nombre}</span>
                            <button onclick="editProject(${project.id}, '${project.nombre}', '${project.descripcion}')" class="btn-icon" title="Editar">✏️</button>
                            <button onclick="deleteProject(${project.id})" class="btn-icon" title="Eliminar">🗑️</button>
                        `;
                        list.appendChild(li);
                    });
                } else {
                    alert('Error: ' + (data.error || 'Respuesta inválida'));
                }
            } catch (e) {
                console.error('Respuesta no JSON:', text);
                alert('Error del servidor: ' + text);
            }
        })
        .catch(err => {
            console.error('Error al cargar proyectos:', err);
            alert('Error al cargar proyectos: ' + err.message);
        });
}

// Crear proyecto
function createProject() {
    const name = document.getElementById('project-name').value;
    const desc = document.getElementById('project-desc').value;

    fetch(`${API_URL}/proyectos`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombre=${encodeURIComponent(name)}&descripcion=${encodeURIComponent(desc)}`
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (!response.ok) throw new Error(data.error || 'Error desconocido');
            if (data.success) {
                document.getElementById('project-modal').style.display = 'none';
                loadProjects();
            } else {
                alert(data.error);
            }
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            alert('Error del servidor: ' + text);
        }
    })
    .catch(err => {
        console.error('Error al crear proyecto:', err);
        alert('Error al crear proyecto: ' + err.message);
    });
}

// Editar proyecto
function editProject(id, nombre, descripcion) {
    document.getElementById('project-name').value = nombre;
    document.getElementById('project-desc').value = descripcion;
    document.getElementById('project-modal').style.display = 'block';
    // Cambiar el botón para actualizar en lugar de crear
    const button = document.querySelector('#project-form button');
    button.textContent = '🔄 Actualizar';
    button.className = 'btn btn-secondary';
    button.onclick = function() {
        updateProject(id);
    };
}

// Actualizar proyecto
function updateProject(id) {
    const name = document.getElementById('project-name').value;
    const desc = document.getElementById('project-desc').value;

    fetch(`${API_URL}/proyectos/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&nombre=${encodeURIComponent(name)}&descripcion=${encodeURIComponent(desc)}`
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (!response.ok) throw new Error(data.error || 'Error desconocido');
            if (data.success) {
                document.getElementById('project-modal').style.display = 'none';
                loadProjects();
                loadStatistics(); // Actualizar estadísticas
                // Restaurar el comportamiento original del formulario
                const projectButton = document.querySelector('#project-form button');
                projectButton.textContent = '✅ Crear';
                projectButton.className = 'btn btn-primary';
                projectButton.onclick = function() {
                    createProject();
                };
                // Limpiar los campos del formulario después de actualizar
                document.getElementById('project-name').value = '';
                document.getElementById('project-desc').value = '';
            } else {
                alert(data.error);
            }
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            alert('Error del servidor: ' + text);
        }
    })
    .catch(err => {
        console.error('Error al actualizar proyecto:', err);
        alert('Error al actualizar proyecto: ' + err.message);
    });
}

// Eliminar proyecto
function deleteProject(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este proyecto?')) {
        fetch(`${API_URL}/proyectos/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(async response => {
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                if (!response.ok) throw new Error(data.error || 'Error desconocido');
                if (data.success) {
                    loadProjects();
                    loadStatistics(); // Actualizar estadísticas
                } else {
                    alert(data.error);
                }
            } catch (e) {
                console.error('Respuesta no JSON:', text);
                alert('Error del servidor: ' + text);
            }
        })
        .catch(err => {
            console.error('Error al eliminar proyecto:', err);
            alert('Error al eliminar proyecto: ' + err.message);
        });
    }
}

// Cargar tareas para un proyecto
function loadTasks(proyectoId) {
    console.log('loadTasks llamado con proyectoId:', proyectoId);
    fetch(`${API_URL}/tareas?proyecto_id=${proyectoId}`)
        .then(async response => {
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                if (!response.ok) throw new Error(data.error || 'Error desconocido');
                if (Array.isArray(data)) {
                    const container = document.getElementById('tasks-container');
                    container.innerHTML = '<button id="new-task-btn" class="btn btn-primary">➕ Nueva Tarea</button>';
                    console.log('Botón "Nueva Tarea" creado para proyectoId:', proyectoId);
                    data.forEach(task => {
                        const div = document.createElement('div');
                        div.className = 'task';
                        const dueDate = task.fecha_vencimiento ? new Date(task.fecha_vencimiento).toLocaleDateString('es-ES') : 'Sin fecha';
                        div.innerHTML = `
                            <div class="task-content">
                                <h4 class="task-title">${task.titulo}</h4>
                                <p class="task-description">${task.descripcion}</p>
                                <div class="task-meta">
                                    <span class="task-due-date">📅 ${dueDate}</span>
                                </div>
                            </div>
                            <div class="task-actions">
                                <select onchange="updateTaskStatus(${task.id}, this.value)" class="task-select">
                                    <option value="pendiente" ${task.estado === 'pendiente' ? 'selected' : ''}>⏳ Pendiente</option>
                                    <option value="en progreso" ${task.estado === 'en progreso' ? 'selected' : ''}>🔄 En Progreso</option>
                                    <option value="completada" ${task.estado === 'completada' ? 'selected' : ''}>✅ Completada</option>
                                </select>
                                <button onclick="editTask(${task.id}, '${task.titulo}', '${task.descripcion}', '${task.estado}', '${task.fecha_vencimiento || ''}')" class="btn-icon" title="Editar">✏️</button>
                                <button onclick="deleteTask(${task.id})" class="btn-icon" title="Eliminar">🗑️</button>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                    // Agregar event listener al botón dinámico
                    const taskBtn = container.querySelector('#new-task-btn');
                    if (taskBtn) {
                        taskBtn.dataset.proyectoId = proyectoId;
                        taskBtn.onclick = function() {
                            console.log('Botón "Nueva Tarea" clickeado para proyectoId:', proyectoId);
                            clearTaskForm(); // Limpiar formulario antes de abrir
                            document.getElementById('task-modal').style.display = 'block';
                        };
                        console.log('Event listener agregado al botón para proyectoId:', proyectoId);
                    }
                } else {
                    alert('Error: ' + (data.error || 'Respuesta inválida'));
                }
            } catch (e) {
                console.error('Respuesta no JSON:', text);
                alert('Error del servidor: ' + text);
            }
        })
        .catch(err => {
            console.error('Error al cargar tareas:', err);
            alert('Error al cargar tareas: ' + err.message);
        });
}

// Actualizar estado de tarea
function updateTaskStatus(id, estado) {
    fetch(`${API_URL}/tareas/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&estado=${encodeURIComponent(estado)}`
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (!response.ok) throw new Error(data.error || 'Error desconocido');
            if (data.success) {
                // Recargar tareas
                const activeProject = document.querySelector('#projects-list li span[onclick*="loadTasks"]');
                if (activeProject) {
                    const match = activeProject.getAttribute('onclick').match(/loadTasks\((\d+)\)/);
                    if (match) loadTasks(match[1]);
                }
                loadStatistics(); // Actualizar estadísticas
            } else {
                alert(data.error);
            }
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            alert('Error del servidor: ' + text);
        }
    })
    .catch(err => {
        console.error('Error al actualizar tarea:', err);
        alert('Error al actualizar tarea: ' + err.message);
    });
}

// Eliminar tarea
function deleteTask(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
        fetch(`${API_URL}/tareas/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(async response => {
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                if (!response.ok) throw new Error(data.error || 'Error desconocido');
                if (data.success) {
                    // Recargar tareas
                    const activeProject = document.querySelector('#projects-list li span[onclick*="loadTasks"]');
                    if (activeProject) {
                        const match = activeProject.getAttribute('onclick').match(/loadTasks\((\d+)\)/);
                        if (match) loadTasks(match[1]);
                    }
                    loadStatistics(); // Actualizar estadísticas
                } else {
                    alert(data.error);
                }
            } catch (e) {
                console.error('Respuesta no JSON:', text);
                alert('Error del servidor: ' + text);
            }
        })
        .catch(err => {
            console.error('Error al eliminar tarea:', err);
            alert('Error al eliminar tarea: ' + err.message);
        });
    }
}

// Cargar estadísticas
function loadStatistics() {
    fetch(`${API_URL}/estadisticas/usuario`)
        .then(response => response.json())
        .then(data => {
            drawChart(data);
        });
}

// Dibujar gráfico en canvas
function drawChart(data) {
    const canvas = document.getElementById('stats-chart');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (data.length === 0) {
        ctx.font = '16px Inter, sans-serif';
        ctx.fillStyle = '#64748b';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos', canvas.width / 2, canvas.height / 2);
        return;
    }

    const total = data.reduce((sum, stat) => sum + parseInt(stat.count), 0);
    let startAngle = 0;

    const colors = {
        'pendiente': '#ffc107',
        'en progreso': '#007bff',
        'completada': '#28a745'
    };

    data.forEach(stat => {
        const sliceAngle = (stat.count / total) * 2 * Math.PI;
        ctx.beginPath();
        ctx.moveTo(150, 100);
        ctx.arc(150, 100, 60, startAngle, startAngle + sliceAngle);
        ctx.closePath();
        ctx.fillStyle = colors[stat.estado] || '#ccc';
        ctx.fill();
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.stroke();
        startAngle += sliceAngle;
    });

    // Leyenda
    const legendContainer = document.querySelector('.statistics-center .legend') || document.createElement('div');
    legendContainer.className = 'legend';
    legendContainer.innerHTML = '';
    data.forEach(stat => {
        const item = document.createElement('div');
        item.style.display = 'flex';
        item.style.alignItems = 'center';
        item.style.marginBottom = '0.5rem';
        item.innerHTML = `
            <div style="width: 12px; height: 12px; background: ${colors[stat.estado]}; border-radius: 50%; margin-right: 0.5rem;"></div>
            <span style="color: var(--text-primary); font-size: 0.875rem;">${stat.estado}: ${stat.count}</span>
        `;
        legendContainer.appendChild(item);
    });

    const statsDiv = document.querySelector('.statistics-center');
    if (statsDiv && !statsDiv.querySelector('.legend')) {
        statsDiv.appendChild(legendContainer);
    }
}
// Función para alternar tema oscuro/claro
function toggleTheme() {
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    
    if (body.getAttribute('data-theme') === 'dark') {
        body.removeAttribute('data-theme');
        themeToggle.textContent = '🌙';
        localStorage.setItem('theme', 'light');
    } else {
        body.setAttribute('data-theme', 'dark');
        themeToggle.textContent = '☀️';
        localStorage.setItem('theme', 'dark');
    }
}

// Editar tarea
function editTask(id, titulo, descripcion, estado, fechaVencimiento) {
    document.getElementById('task-title').value = titulo;
    document.getElementById('task-desc').value = descripcion;
    document.getElementById('task-state').value = estado;
    document.getElementById('task-due-date').value = fechaVencimiento || '';
    document.getElementById('task-modal').style.display = 'block';
    // Cambiar el botón para actualizar en lugar de crear
    const button = document.querySelector('#task-form button');
    button.textContent = '🔄 Actualizar';
    button.className = 'btn btn-secondary';
    button.dataset.mode = 'update';
    button.dataset.taskId = id;
}

// Actualizar tarea
async function updateTask(id) {
    const title = document.getElementById('task-title').value;
    const desc = document.getElementById('task-desc').value;
    const state = document.getElementById('task-state').value;
    const dueDate = document.getElementById('task-due-date').value;

    if (!title.trim()) {
        showToast('El título es obligatorio', 'error');
        return;
    }

    const body = `id=${id}&titulo=${encodeURIComponent(title)}&descripcion=${encodeURIComponent(desc)}&estado=${encodeURIComponent(state)}&fecha_vencimiento=${encodeURIComponent(dueDate)}`;
    console.log('Body del update:', body);

    try {
        const response = await fetch(`${API_URL}/tareas/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        });

        const text = await response.text();
        console.log('Respuesta de update tarea:', text);

        const data = JSON.parse(text);
        if (!response.ok) throw new Error(data.error || 'Error desconocido');

        if (data.success) {
            document.getElementById('task-modal').style.display = 'none';
            loadTasks(getCurrentProjectId());
            loadStatistics(); // Actualizar estadísticas
            clearTaskForm();
            showToast('Tarea actualizada exitosamente', 'success');
        } else {
            showToast(data.error, 'error');
        }
    } catch (e) {
        console.error('Error al actualizar tarea:', e);
        showToast('Error del servidor: ' + e.message, 'error');
    }
}

// Obtener ID del proyecto actual
function getCurrentProjectId() {
    const activeProject = document.querySelector('#projects-list li span[onclick*="loadTasks"]');
    if (activeProject) {
        const match = activeProject.getAttribute('onclick').match(/loadTasks\((\d+)\)/);
        return match ? match[1] : null;
    }
    return null;
}

// Crear tarea
async function createTask(proyectoId) {
    console.log('Creando tarea para proyecto_id:', proyectoId);
    const title = document.getElementById('task-title').value;
    const desc = document.getElementById('task-desc').value;
    const state = document.getElementById('task-state').value;
    const dueDate = document.getElementById('task-due-date').value;

    console.log('Datos del formulario: title=', title, 'desc=', desc, 'state=', state, 'dueDate=', dueDate);

    if (!title.trim()) {
        showToast('El título es obligatorio', 'error');
        return;
    }

    const body = `titulo=${encodeURIComponent(title)}&descripcion=${encodeURIComponent(desc)}&estado=${encodeURIComponent(state)}&proyecto_id=${proyectoId}&fecha_vencimiento=${encodeURIComponent(dueDate)}`;
    console.log('Body del fetch:', body);

    fetch(`${API_URL}/tareas`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(async response => {
        const text = await response.text();
        console.log('Respuesta de crear tarea:', text);
        try {
            const data = JSON.parse(text);
            if (!response.ok) throw new Error(data.error || 'Error desconocido');
            if (data.success) {
                document.getElementById('task-modal').style.display = 'none';
                loadTasks(proyectoId);
                loadStatistics(); // Actualizar estadísticas
                clearTaskForm(); // Limpiar formulario después de crear tarea
                showToast('Tarea creada exitosamente', 'success');
            } else {
                showToast(data.error, 'error');
            }
        } catch (e) {
            console.error('Respuesta no JSON:', text);
            showToast('Error del servidor: ' + text, 'error');
        }
    })
    .catch(err => {
        console.error('Error al crear tarea:', err);
        showToast('Error al crear tarea: ' + err.message, 'error');
    });
}

// Función para limpiar el formulario de tareas
function clearTaskForm() {
    console.log('Limpiando formulario de tareas');
    document.getElementById('task-title').value = '';
    document.getElementById('task-desc').value = '';
    document.getElementById('task-state').value = 'pendiente';
    document.getElementById('task-due-date').value = '';
    // Restaurar el comportamiento original del formulario
    const taskButton = document.querySelector('#task-form button');
    taskButton.textContent = '✅ Crear Tarea';
    taskButton.className = 'btn btn-primary';
    // Limpiar atributos de modo
    delete taskButton.dataset.mode;
    delete taskButton.dataset.taskId;
}

// Cargar tema guardado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    const themeToggle = document.getElementById('theme-toggle');

    if (savedTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        themeToggle.textContent = '☀️';
    }

    // Agregar evento al botón de toggle
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
});