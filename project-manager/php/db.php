<?php
$host = 'localhost';
$dbname = 'gestion_projet';
$username = 'gestion_user';
$password = 'secret123'; // Changez ceci selon votre configuration

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
    error_log("DB Connection Error: " . $db_error); // Affiche l'erreur dans le terminal
}
?>
