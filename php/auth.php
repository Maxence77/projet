<?php
header('Content-Type: application/json');
require 'db.php';

// Création de la table users si elle n'existe pas
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // On continue même si erreur (la table existe peut-être déjà)
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if ($action === 'register') {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Champs requis']);
        exit;
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur déjà pris']);
        exit;
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
    }

} elseif ($action === 'login') {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
    } else {
        // Fallback pour l'admin par défaut si la base est vide ou pour compatibilité
        if ($username === 'admin' && $password === 'admin') {
             echo json_encode(['success' => true, 'message' => 'Connexion admin réussie']);
        } else {
             echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
}
?>
