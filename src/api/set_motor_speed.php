<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

// Ce script définit la vitesse pour un moteur spécifique (motorId = 1)

$conn = null;
try {
    $data = json_decode(file_get_contents('php://input'), true);
    $speed = $data['speed'] ?? null;

    if (!isset($speed) || !is_numeric($speed)) {
        throw new Exception("La valeur de la vitesse est manquante ou invalide.");
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE motors SET motorSpeed = ? WHERE motorId = 1");
    if (!$stmt) {
        throw new Exception("La préparation de la requête a échoué : " . $conn->error);
    }

    $stmt->bind_param('d', $speed); // 'd' pour double/float
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => "Vitesse du moteur mise à jour à $speed."]);
    } else {
        echo json_encode(['status' => 'success', 'message' => "Commande de mise à jour de la vitesse du moteur envoyée. Aucune ligne affectée (la vitesse était peut-être déjà la même ou motorId non trouvé)."]);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 