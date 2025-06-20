<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Récupère le dernier statut pour les tronçons "Départ" et "Milieu"
    $query = "(SELECT id, statut, tronçon FROM capteur_tracking WHERE tronçon = 'Départ' ORDER BY id DESC LIMIT 1) UNION ALL (SELECT id, statut, tronçon FROM capteur_tracking WHERE tronçon = 'Milieu' ORDER BY id DESC LIMIT 1)";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $statuses = [];
    while ($row = $result->fetch_assoc()) {
        // On mappe le nom du tronçon de la BDD à un identifiant simple pour le front-end
        if ($row['tronçon'] === 'Départ') {
            $statuses['sensor-1'] = $row['statut'];
        } elseif ($row['tronçon'] === 'Milieu') {
            $statuses['sensor-2'] = $row['statut'];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $statuses]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 