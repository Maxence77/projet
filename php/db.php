<?php
// Chargement des variables d'environnement (Simulé pour cet exemple simple sans Composer/Dotenv)
// Dans un environnement de prod, ces variables sont définies dans la config du serveur ou via un fichier .env non versionné

// Définition de l'hôte de la base de données
$host = getenv('DB_HOST') ?: 'localhost';
// Nom de la base de données à utiliser
$dbname = getenv('DB_NAME') ?: 'gestion_projet';
// Nom d'utilisateur pour se connecter à la base de données
$username = getenv('DB_USER') ?: 'gestion_user';
// Mot de passe pour l'utilisateur de la base de données
$password = 'secret123';

try {
    // Tentative de connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration de PDO pour qu'il lance des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Désactiver l'émulation des requêtes préparées pour plus de sécurité (évite certaines injections SQL)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // En cas d'erreur, ne JAMAIS afficher les détails de l'erreur à l'utilisateur final en production
    error_log("Erreur de connexion BDD : " . $e->getMessage());
    die("Erreur de connexion au service de données.");
}
?>
