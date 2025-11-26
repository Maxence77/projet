document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('project-form');
    const projectList = document.getElementById('project-list');

    // Charger les projets au démarrage
    loadProjects();

    // Ajouter un projet
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const status = document.getElementById('status').value;

        await fetch('php/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description, status })
        });

        form.reset();
        loadProjects();
    });

    // Fonction pour charger les projets
    async function loadProjects() {
        try {
            const res = await fetch('php/api.php');
            if (!res.ok) {
                throw new Error(`Erreur HTTP: ${res.status}`);
            }
            
            const projects = await res.json();

            if (projects.message && !Array.isArray(projects)) {
                 // Si le backend renvoie un message d'erreur au lieu d'une liste
                 console.error('Erreur backend:', projects.message);
                 alert('Erreur: ' + projects.message);
                 return;
            }

            projectList.innerHTML = '';
            projects.forEach(project => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div>
                        <strong>${project.title}</strong> (${project.status})
                        <p>${project.description}</p>
                    </div>
                    <button class="delete-btn" onclick="deleteProject(${project.id})">X</button>
                `;
                projectList.appendChild(li);
            });
        } catch (error) {
            console.error('Erreur lors du chargement des projets:', error);
            projectList.innerHTML = '<li style="color: red;">Impossible de charger les projets. Vérifiez la console (F12) et assurez-vous que le serveur PHP tourne.</li>';
        }
    }

    // Fonction pour supprimer un projet (globale pour être accessible via onclick)
    window.deleteProject = async (id) => {
        if(confirm('Voulez-vous vraiment supprimer ce projet ?')) {
            await fetch(`php/api.php?id=${id}`, { method: 'DELETE' });
            loadProjects();
        }
    };
});
