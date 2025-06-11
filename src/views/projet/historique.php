<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "<p style='color:red;'>Erreur : utilisateur non connecté.</p>";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les candidatures de l'utilisateur
$sql = "SELECT p.nom AS nom_projet, p.annonce_titre, p.annonce_description, p.annonce_date_creation, p.annonce_etat, c.date_postulation
        FROM Candidatures c
        JOIN Projets p ON c.projet_id = p.id
        WHERE c.utilisateur_id = ?
        ORDER BY c.date_postulation DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="mes-candidatures-container">
    <h2>Mes Candidatures</h2>

    <?php if ($result->num_rows > 0): ?>
        <ul class="candidature-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="candidature-item">
                    <h3><?= htmlspecialchars($row['annonce_titre']) ?> (<?= htmlspecialchars($row['nom_projet']) ?>)</h3>
                    <p><?= htmlspecialchars($row['annonce_description']) ?></p>
                    <p><strong>Date de candidature :</strong> <?= date("d/m/Y", strtotime($row['date_postulation'])) ?></p>
                    <p><strong>État de l'annonce :</strong> <?= htmlspecialchars($row['annonce_etat']) ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Vous n’avez pas encore postulé à un projet.</p>
    <?php endif; ?>

</div>

<?php
$stmt->close();
$conn->close();
?>
