<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $query = "
        SELECT t1.id, t1.statut, t1.date, t1.tronçon
        FROM capteur_tracking t1
        INNER JOIN (
            SELECT tronçon, MAX(id) as max_id
            FROM capteur_tracking
            GROUP BY tronçon
        ) t2 ON t1.tronçon = t2.tronçon AND t1.id = t2.max_id
        ORDER BY t1.tronçon ASC
    ";
    
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 