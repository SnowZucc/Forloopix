<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collaborateur') {
    header("Location: ../user/connexion.php");
    exit;
}

$id_utilisateur = $_SESSION['user_id'];
$id_projet = $_POST['id_projet'] ?? null;

if (!$id_projet) {
    echo "ID manquant.";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Supprime la ligne dans Annonces_Sauvegardees
$stmt = $conn->prepare("DELETE FROM Annonces_Sauvegardees WHERE id_utilisateur = ? AND id_projet = ?");
$stmt->bind_param("ii", $id_utilisateur, $id_projet);
$stmt->execute();

header("Location: /Forloopix/src/views/projet/espace-collaborateur.php?view=Hutbox&msg=deleted");

exit;
?>
