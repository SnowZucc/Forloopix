<?php
require_once('../../config/config.php');
header('Content-Type: application/json');

// Ce script réinitialise le statut des derniers capteurs 'Départ' et 'Milieu' à 0.

$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    // Trouve le dernier ID pour 'Départ'
    $stmt_dep = $conn->prepare("SELECT id FROM capteur_tracking WHERE tronçon = 'Départ' ORDER BY id DESC LIMIT 1");
    $stmt_dep->execute();
    $result_dep = $stmt_dep->get_result();
    $id_dep = $result_dep->fetch_assoc()['id'] ?? null;
    $stmt_dep->close();

    // Trouve le dernier ID pour 'Milieu'
    $stmt_mil = $conn->prepare("SELECT id FROM capteur_tracking WHERE tronçon = 'Milieu' ORDER BY id DESC LIMIT 1");
    $stmt_mil->execute();
    $result_mil = $stmt_mil->get_result();
    $id_mil = $result_mil->fetch_assoc()['id'] ?? null;
    $stmt_mil->close();

    $updated_rows = 0;

    // Met à jour 'Départ' si trouvé
    if ($id_dep) {
        $update_stmt_dep = $conn->prepare("UPDATE capteur_tracking SET statut = 0 WHERE id = ?");
        $update_stmt_dep->bind_param('i', $id_dep);
        $update_stmt_dep->execute();
        $updated_rows += $update_stmt_dep->affected_rows;
        $update_stmt_dep->close();
    }

    // Met à jour 'Milieu' si trouvé
    if ($id_mil) {
        $update_stmt_mil = $conn->prepare("UPDATE capteur_tracking SET statut = 0 WHERE id = ?");
        $update_stmt_mil->bind_param('i', $id_mil);
        $update_stmt_mil->execute();
        $updated_rows += $update_stmt_mil->affected_rows;
        $update_stmt_mil->close();
    }

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => "Sensor statuses reset. $updated_rows rows affected."]);

} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
} 