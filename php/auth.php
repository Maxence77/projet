<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if ($action === 'register') {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris']);
        exit;
    }

    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hashed_password])) {
        echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    } else {
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
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Action invalide']);
}
?>
