<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

// Ce script récupère la dernière valeur du sonomètre depuis la table capteur_son

$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Récupère la dernière entrée de la table capteur_son
    $result = $conn->query("SELECT valeur FROM capteur_son ORDER BY id DESC LIMIT 1");

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    
    if ($row) {
        echo json_encode(['status' => 'success', 'value' => floatval($row['valeur'])]);
    } else {
        // Aucune donnée trouvée, retourne 0 par défaut
        echo json_encode(['status' => 'success', 'value' => 0]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 