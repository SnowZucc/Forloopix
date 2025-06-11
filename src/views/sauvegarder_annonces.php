<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// Vérifie que l'utilisateur est connecté et est collaborateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collaborateur') {
    header("Location: ../user/connexion.php");
    exit;
}

// Récupère les infos
$id_utilisateur = $_SESSION['user_id'];
$id_projet = $_POST['id_projet'] ?? null;

if (!$id_projet) {
    header("Location: annonce.php?msg=invalid");
    exit;
}

// Connexion à la BDD (MySQLi ici car le reste du site semble l’utiliser)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion à la base : " . $conn->connect_error);
}

// Vérifie si l'annonce est déjà sauvegardée
$stmt = $conn->prepare("SELECT * FROM Annonces_Sauvegardees WHERE id_utilisateur = ? AND id_projet = ?");
$stmt->bind_param("ii", $id_utilisateur, $id_projet);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO Annonces_Sauvegardees (id_utilisateur, id_projet) VALUES (?, ?)");
    $insert->bind_param("ii", $id_utilisateur, $id_projet);
    $insert->execute();
    $insert->close();
    $msg = "saved";
} else {
    $msg = "already";
}

$stmt->close();
$conn->close();

header("Location: annonce.php?id=$id_projet&msg=$msg");
exit;
