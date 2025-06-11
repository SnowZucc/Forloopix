<?php
// On démarre la session pour accéder à l'utilisateur connecté
session_start();

// On inclut le fichier de configuration pour se connecter à la base
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// On récupère l'ID de l'expéditeur (l'utilisateur actuellement connecté)
$expediteur = $_SESSION['user_id'] ?? null;

// On récupère le contenu du message envoyé via POST
$contenu = $_POST['contenu'] ?? null;

// On vérifie qu'on a un expéditeur et un message à envoyer
if ($expediteur && $contenu) {
    // Connexion à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Pour un message général, le destinataire est NULL
    $stmt = $conn->prepare("
        INSERT INTO Messages (id_expediteur, id_destinataire, contenu, date) 
        VALUES (?, NULL, ?, NOW())
    ");
    $stmt->bind_param("is", $expediteur, $contenu);
    $stmt->execute();
    $stmt->close();
    
    // On ferme la connexion à la base
    $conn->close();
}

// À la fin, on redirige l'utilisateur vers la page précédente avec le mode général activé
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?> 