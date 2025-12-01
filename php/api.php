<?php
// Sécurisation des en-têtes HTTP
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Inclut le fichier de connexion à la base de données
require 'db.php';

// Récupère la méthode HTTP utilisée (GET, POST, DELETE, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Si la méthode est GET (récupération de données)
if ($method === 'GET') {
    // Prépare la requête pour lister tous les projets, triés par date de création décroissante
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    // Exécute la requête et renvoie les résultats au format JSON
    // Utilisation de ENT_QUOTES pour éviter les problèmes XSS lors de l'affichage côté client si mal géré
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // On pourrait ajouter un nettoyage ici si nécessaire, mais le client doit aussi échapper
    echo json_encode($projects);

// Si la méthode est POST (envoi de données)
} elseif ($method === 'POST') {
    // Récupère les données JSON envoyées dans le corps de la requête
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Nettoyage des entrées pour éviter le stockage de scripts malveillants (XSS stocké)
    $title = htmlspecialchars(strip_tags($data['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(strip_tags($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars(strip_tags($data['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8');

    // Vérifie si le titre est présent
    if ($title) {
        // Prépare la requête d'insertion d'un nouveau projet
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, status) VALUES (?, ?, ?)");
        // Exécute la requête avec les données fournies
        $stmt->execute([$title, $description, $status]);
        // Renvoie un succès
        echo json_encode(['success' => true]);
    } else {
        // Renvoie une erreur si le titre est manquant
        echo json_encode(['success' => false, 'message' => 'Titre requis']);
    }

// Si la méthode est DELETE (suppression de données)
} elseif ($method === 'DELETE') {
    // Récupère l'ID du projet à supprimer depuis les paramètres d'URL
    $id = $_GET['id'] ?? 0;
    // Vérifie si un ID a été fourni
    if ($id) {
        // Prépare la requête de suppression du projet
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        // Exécute la requête avec l'ID fourni
        $stmt->execute([$id]);
        // Renvoie un succès
        echo json_encode(['success' => true]);
    } else {
        // Renvoie une erreur si l'ID est manquant
        echo json_encode(['success' => false, 'message' => 'ID requis']);
    }
}
?>
