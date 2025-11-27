<?php
// Définit l'en-tête de la réponse comme étant du JSON
header('Content-Type: application/json');
// Inclut le fichier de connexion à la base de données
require 'db.php';

// Récupère les données JSON envoyées dans le corps de la requête
$data = json_decode(file_get_contents("php://input"), true);
// Récupère l'action demandée (register ou login), ou une chaîne vide si non définie
$action = $data['action'] ?? '';

// Si l'action est 'register' (inscription)
if ($action === 'register') {
    // Récupère le nom d'utilisateur et le mot de passe
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // Vérifie si les champs sont vides
    if (empty($username) || empty($password)) {
        // Renvoie une erreur si des champs sont manquants
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit; // Arrête l'exécution du script
    }

    // Vérifie si l'utilisateur existe déjà dans la base de données
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        // Si l'utilisateur existe, renvoie une erreur
        echo json_encode(['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris']);
        exit;
    }

    // Hache le mot de passe pour la sécurité
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Prépare la requête d'insertion du nouvel utilisateur
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    // Exécute la requête et vérifie si elle a réussi
    if ($stmt->execute([$username, $hashed_password])) {
        // Renvoie un succès si l'inscription a fonctionné
        echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    } else {
        // Renvoie une erreur si l'insertion a échoué
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
    }

// Si l'action est 'login' (connexion)
} elseif ($action === 'login') {
    // Récupère le nom d'utilisateur et le mot de passe
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // Prépare la requête pour récupérer l'utilisateur par son nom
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    // Récupère le résultat sous forme de tableau associatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si l'utilisateur existe et si le mot de passe correspond au hachage
    if ($user && password_verify($password, $user['password'])) {
        // Renvoie un succès si la connexion est valide
        echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
    } else {
        // Renvoie une erreur si les identifiants sont incorrects
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    }
} else {
    // Si l'action n'est ni 'register' ni 'login', renvoie une erreur
    echo json_encode(['success' => false, 'message' => 'Action invalide']);
}
?>
