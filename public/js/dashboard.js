// JavaScript para Dashboard de TaskFlow
// Maneja AJAX para CRUD sin recarga

document.addEventListener('DOMContentLoaded', function() {
    loadProjects();
    loadStatistics();

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

    // Formulario de proyecto
    document.getElementById('project-form').addEventListener('submit', function(e) {
        e.preventDefault();
        createProject();
    });
});

// Cargar proyectos
function loadProjects() {
    fetch('/api/proyectos')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('projects-list');
            list.innerHTML = '';
            data.forEach(project => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span onclick="loadTasks(${project.id})">${project.nombre}</span>
                    <button onclick="editProject(${project.id}, '${project.nombre}', '${project.descripcion}')">Editar</button>
                    <button onclick="deleteProject(${project.id})">Eliminar</button>
                `;
                list.appendChild(li);
            });
        });
}

// Crear proyecto
function createProject() {
    const name = document.getElementById('project-name').value;
    const desc = document.getElementById('project-desc').value;

    fetch('/api/proyectos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombre=${encodeURIComponent(name)}&descripcion=${encodeURIComponent(desc)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('project-modal').style.display = 'none';
            loadProjects();
        } else {
            alert(data.error);
        }
    });
}

// Cargar tareas para un proyecto
function loadTasks(proyectoId) {
    fetch(`/api/tareas?proyecto_id=${proyectoId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('tasks-container');
            container.innerHTML = '<h3>Tareas</h3>';
            data.forEach(task => {
                const div = document.createElement('div');
                div.className = 'task';
                div.innerHTML = `
                    <h4>${task.titulo}</h4>
                    <p>${task.descripcion}</p>
                    <select onchange="updateTaskStatus(${task.id}, this.value)">
                        <option value="pendiente" ${task.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="en progreso" ${task.estado === 'en progreso' ? 'selected' : ''}>En Progreso</option>
                        <option value="completada" ${task.estado === 'completada' ? 'selected' : ''}>Completada</option>
                    </select>
                    <button onclick="deleteTask(${task.id})">Eliminar</button>
                `;
                container.appendChild(div);
            });
        });
}

// Actualizar estado de tarea
function updateTaskStatus(id, estado) {
    fetch('/api/tareas/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&estado=${encodeURIComponent(estado)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar tareas
            const activeProject = document.querySelector('#projects-list li span[onclick*="loadTasks"]');
            if (activeProject) {
                const match = activeProject.getAttribute('onclick').match(/loadTasks\((\d+)\)/);
                if (match) loadTasks(match[1]);
            }
        }
    });
}

// Cargar estadísticas
function loadStatistics() {
    fetch('/api/estadisticas')
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
        ctx.moveTo(200, 100);
        ctx.arc(200, 100, 80, startAngle, startAngle + sliceAngle);
        ctx.closePath();
        ctx.fillStyle = colors[stat.estado] || '#ccc';
        ctx.fill();
        ctx.stroke();
        startAngle += sliceAngle;
    });

    // Leyenda
    const legend = document.createElement('div');
    legend.innerHTML = '<h4>Leyenda</h4>';
    data.forEach(stat => {
        legend.innerHTML += `<p style="color: ${colors[stat.estado]};">${stat.estado}: ${stat.count}</p>`;
    });
    document.getElementById('statistics').appendChild(legend);
}