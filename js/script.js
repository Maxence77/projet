// Attend que le document HTML soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {
    // Récupère le formulaire d'ajout de projet par son ID
    const form = document.getElementById('project-form');
    // Récupère la liste où les projets seront affichés
    const projectList = document.getElementById('project-list');

    // Appelle la fonction pour charger et afficher les projets existants au démarrage
    loadProjects();

    // Ajoute un écouteur d'événement pour la soumission du formulaire
    form.addEventListener('submit', async (e) => {
        // Empêche le rechargement de la page lors de la soumission du formulaire
        e.preventDefault();
        
        // Récupère les valeurs des champs du formulaire
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const status = document.getElementById('status').value;

        // Envoie une requête POST à l'API pour créer un nouveau projet
        await fetch('php/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description, status })
        });

        // Réinitialise le formulaire après l'ajout
        form.reset();
        // Recharge la liste des projets pour afficher le nouveau projet
        loadProjects();
    });

    // Fonction asynchrone pour charger les projets depuis l'API
    async function loadProjects() {
        try {
            // Envoie une requête GET à l'API pour récupérer les projets
            const res = await fetch('php/api.php');
            // Vérifie si la réponse HTTP est correcte (code 200-299)
            if (!res.ok) {
                throw new Error(`Erreur HTTP: ${res.status}`);
            }
            
            // Convertit la réponse en JSON
            const projects = await res.json();

            // Vérifie si la réponse contient un message d'erreur au lieu d'un tableau
            if (projects.message && !Array.isArray(projects)) {
                 // Affiche l'erreur dans la console et via une alerte
                 console.error('Erreur backend:', projects.message);
                 alert('Erreur: ' + projects.message);
                 return;
            }

            // Vide la liste actuelle des projets
            projectList.innerHTML = '';
            // Parcourt chaque projet reçu et l'ajoute à la liste
            projects.forEach(project => {
                // Crée un nouvel élément de liste (li)
                const li = document.createElement('li');
                // Définit le contenu HTML de l'élément (titre, statut, description, bouton supprimer)
                li.innerHTML = `
                    <div>
                        <strong>${project.title}</strong> (${project.status})
                        <p>${project.description}</p>
                    </div>
                    <button class="delete-btn" onclick="deleteProject(${project.id})">X</button>
                `;
                // Ajoute l'élément à la liste dans le DOM
                projectList.appendChild(li);
            });
        } catch (error) {
            // En cas d'erreur (réseau, parsing, etc.), affiche un message d'erreur
            console.error('Erreur lors du chargement des projets:', error);
            projectList.innerHTML = '<li style="color: red;">Impossible de charger les projets. Vérifiez la console (F12) et assurez-vous que le serveur PHP tourne.</li>';
        }
    }

    // Fonction globale pour supprimer un projet (accessible via l'attribut onclick du HTML)
    window.deleteProject = async (id) => {
        // Demande confirmation à l'utilisateur avant de supprimer
        if(confirm('Voulez-vous vraiment supprimer ce projet ?')) {
            // Envoie une requête DELETE à l'API avec l'ID du projet
            await fetch(`php/api.php?id=${id}`, { method: 'DELETE' });
            // Recharge la liste des projets pour refléter la suppression
            loadProjects();
        }
    };
});

// Fonction pour se déconnecter
function logout() {
    // Supprime l'indicateur de connexion du stockage local
    localStorage.removeItem('isLoggedIn');
    // Redirige l'utilisateur vers la page de connexion
    window.location.href = 'index.html';
}
