<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'porteur') {
    header("Location: ../user/connexion.php");
    exit;
}

$utilisateur_id = $_POST['utilisateur_id'] ?? null;
$projet_id = $_POST['projet_id'] ?? null;
$action = $_POST['action'] ?? '';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($action === 'accepter') {
    // 1. Mettre à jour le statut
    $update = $conn->prepare("UPDATE Candidatures SET statut = 'accepte' WHERE utilisateur_id = ? AND projet_id = ?");
    $update->bind_param("ii", $utilisateur_id, $projet_id);
    $update->execute();

    // 2. Ajouter dans ParticipantsProjets
    $insert = $conn->prepare("INSERT IGNORE INTO ParticipantsProjets (id_projet, id_participant, role) VALUES (?, ?, 'membre')");
    $insert->bind_param("ii", $projet_id, $utilisateur_id);
    $insert->execute();

} elseif ($action === 'refuser') {
    // Refuser la candidature
    $update = $conn->prepare("UPDATE Candidatures SET statut = 'refuse' WHERE utilisateur_id = ? AND projet_id = ?");
    $update->bind_param("ii", $utilisateur_id, $projet_id);
    $update->execute();
}

$conn->close();
header("Location: recrutement.php");
exit;
