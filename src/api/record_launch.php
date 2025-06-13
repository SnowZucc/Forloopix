<?php
// api/record_launch.php

header('Content-Type: application/json');
require_once('../../config/config.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['attraction_id']) || !isset($data['passenger_count'])) {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
    exit;
}

$attraction_id = $data['attraction_id'];
$passenger_count = (int)$data['passenger_count'];

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");

    $stmt = $conn->prepare("INSERT INTO Launches (attraction_id, passenger_count) VALUES (?, ?)");
    if ($stmt === false) {
        throw new Exception("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("si", $attraction_id, $passenger_count);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'launch_id' => $stmt->insert_id]);
    } else {
        throw new Exception("Erreur d'exécution: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // En cas d'erreur, renvoyer une réponse d'erreur
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} 