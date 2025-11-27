<?php
header('Content-Type: application/json');
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List projects
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} elseif ($method === 'POST') {
    // Add project
    $data = json_decode(file_get_contents("php://input"), true);
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $status = $data['status'] ?? 'pending';

    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, status) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $status]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Titre requis']);
    }

} elseif ($method === 'DELETE') {
    // Delete project
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID requis']);
    }
}
?>
