<?php
// Définition de l'hôte de la base de données (généralement localhost)
$host = 'localhost';
// Nom de la base de données à utiliser
$dbname = 'gestion_projet';
// Nom d'utilisateur pour se connecter à la base de données
$username = 'gestion_user';
// Mot de passe pour l'utilisateur de la base de données
$password = 'secret123';

try {
    // Tentative de connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration de PDO pour qu'il lance des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, on arrête le script et on affiche le message d'erreur
    die("Erreur de connexion : " . $e->getMessage());
}
?>
