// Données simulées
const projects = [
    {
        id: 1,
        title: "Refonte Site Web",
        description: "Modernisation de l'interface utilisateur et amélioration des performances.",
        progress: 75,
        status: "active",
        dueDate: "2023-12-01",
        members: 4
    },
    {
        id: 2,
        title: "Application Mobile",
        description: "Développement de l'application iOS et Android.",
        progress: 30,
        status: "active",
        dueDate: "2024-02-15",
        members: 6
    },
    {
        id: 3,
        title: "Marketing Q4",
        description: "Campagne publicitaire pour la fin d'année.",
        progress: 90,
        status: "pending",
        dueDate: "2023-11-30",
        members: 3
    }
];

const tasks = [
    { id: 1, title: "Design System", status: "todo", tag: "Design" },
    { id: 2, title: "API Authentication", status: "inprogress", tag: "Backend" },
    { id: 3, title: "User Testing", status: "done", tag: "QA" },
    { id: 4, title: "Homepage Layout", status: "todo", tag: "Frontend" }
];

// Éléments du DOM
const projectsGrid = document.getElementById('projectsGrid');
const kanbanBoard = document.getElementById('kanbanBoard');
const mainView = document.getElementById('mainView');
const kanbanView = document.getElementById('kanbanView');

// Affichage des projets
function renderProjects() {
    projectsGrid.innerHTML = projects.map(project => `
        <div class="project-card">
            <div class="project-header">
                <h3>${project.title}</h3>
                <span class="status-badge status-${project.status}">${project.status}</span>
            </div>
            <p style="color: #636e72; font-size: 14px; margin-bottom: 15px;">${project.description}</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${project.progress}%"></div>
            </div>
            <div class="project-footer">
                <span>📅 ${project.dueDate}</span>
                <span>👥 ${project.members} membres</span>
            </div>
        </div>
    `).join('');
}

// Affichage du Kanban
function renderKanban() {
    const columns = {
        todo: document.getElementById('todoCol'),
        inprogress: document.getElementById('inprogressCol'),
        done: document.getElementById('doneCol')
    };

    // Effacer le contenu des colonnes sauf l'en-tête
    Object.values(columns).forEach(col => {
        const header = col.querySelector('.kanban-header');
        col.innerHTML = '';
        col.appendChild(header);
    });

    tasks.forEach(task => {
        const card = document.createElement('div');
        card.className = 'kanban-card';
        card.draggable = true;
        card.innerHTML = `
            <h4>${task.title}</h4>
            <span class="tag">${task.tag}</span>
        `;
        
        if (columns[task.status]) {
            columns[task.status].appendChild(card);
        }
    });
}

// Logique de navigation
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Mettre à jour l'état actif
        document.querySelectorAll('.nav-links a').forEach(l => l.classList.remove('active'));
        e.currentTarget.classList.add('active');

        // Changer de vue
        const view = e.currentTarget.getAttribute('href').substring(1);
        if (view === 'dashboard') {
            mainView.style.display = 'block';
            kanbanView.style.display = 'none';
        } else if (view === 'tasks') {
            mainView.style.display = 'none';
            kanbanView.style.display = 'flex';
            renderKanban();
        }
    });
});

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    renderProjects();
    
    // Mettre à jour les statistiques
    document.getElementById('totalProjects').textContent = projects.length;
    document.getElementById('activeTasks').textContent = tasks.filter(t => t.status !== 'done').length;
});
