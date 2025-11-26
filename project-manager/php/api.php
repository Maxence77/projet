<?php
header('Content-Type: application/json');
require 'db.php';

if (isset($db_error)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion DB: ' . $db_error]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['title'])) {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, status) VALUES (?, ?, ?)");
            $stmt->execute([$data['title'], $data['description'] ?? '', $data['status'] ?? 'pending']);
            echo json_encode(['success' => true, 'message' => 'Projet ajouté']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(['success' => true, 'message' => 'Projet supprimé']);
        }
        break;

    default:
        echo json_encode(['message' => 'Méthode non supportée']);
        break;
}
?>
