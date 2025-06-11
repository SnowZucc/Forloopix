<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// Vérifie que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collaborateur') {
    header("Location: ../user/connexion.php");
    exit;
}

// Récupération des données
$id_utilisateur = $_SESSION['user_id'];
$id_projet = isset($_POST['id_projet']) ? (int)$_POST['id_projet'] : 0;
$from = $_POST['from'] ?? null;

if ($id_projet <= 0) {
    echo "Projet non spécifié.";
    exit;
}

// Connexion à la BDD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifie si l'utilisateur a déjà postulé
$check = $conn->prepare("SELECT * FROM Candidatures WHERE utilisateur_id = ? AND projet_id = ?");
$check->bind_param("ii", $id_utilisateur, $id_projet);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $redirect = "Location: annonce.php?id=$id_projet&msg=already_postulated";
    if ($from === 'hutbox') {
        $redirect .= "&from=hutbox";
    }
    header($redirect);
    exit;
}

// Insère la candidature
$stmt = $conn->prepare("INSERT INTO Candidatures (utilisateur_id, projet_id, statut, date_postulation) VALUES (?, ?, 'en attente', NOW())");
$stmt->bind_param("ii", $id_utilisateur, $id_projet);
$stmt->execute();

// Redirection finale
$redirect = "Location: annonce.php?id=$id_projet&msg=success";
if ($from === 'hutbox') {
    $redirect .= "&from=hutbox";
}

header($redirect);
exit;
?>
