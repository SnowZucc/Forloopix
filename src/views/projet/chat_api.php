<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

$user_id = $_SESSION['user_id'] ?? null;
$utilisateur_id = $_GET['utilisateur_id'] ?? $_POST['utilisateur_id'] ?? null;

if (!$user_id || !$utilisateur_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres manquants.']);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message !== '') {
        $stmt = $conn->prepare("
            INSERT INTO Messages (id_expediteur, id_destinataire, contenu, date)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("iis", $user_id, $utilisateur_id, $message);
        $stmt->execute();
        $stmt->close();
    }
    echo json_encode(['success' => true]);
    exit;
}

// Récupérer les messages
$stmt = $conn->prepare("
    SELECT m.*, u.prenom, u.nom
    FROM Messages m
    JOIN Utilisateurs u ON m.id_expediteur = u.id
    WHERE (m.id_expediteur = ? AND m.id_destinataire = ?)
       OR (m.id_expediteur = ? AND m.id_destinataire = ?)
    ORDER BY m.date ASC
");
$stmt->bind_param("iiii", $user_id, $utilisateur_id, $utilisateur_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

header('Content-Type: application/json');
echo json_encode($messages);
