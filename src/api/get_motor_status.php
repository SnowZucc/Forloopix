<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

// Ce script récupère la vitesse actuelle du moteur avec motorId = 1

$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    $result = $conn->query("SELECT motorSpeed FROM motors WHERE motorId = 1 LIMIT 1");

    if (!$result) {
        throw new Exception("La requête a échoué : " . $conn->error);
    }

    $row = $result->fetch_assoc();
    
    if ($row) {
        echo json_encode(['status' => 'success', 'speed' => floatval($row['motorSpeed'])]);
    } else {
        // Aucun moteur trouvé avec l'ID 1
        echo json_encode(['status' => 'error', 'message' => 'Moteur avec ID 1 non trouvé.', 'speed' => 0]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 