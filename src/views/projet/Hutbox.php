<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// Vérifie que l'utilisateur est connecté et est collaborateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collaborateur') {
    header("Location: ../user/connexion.php");
    exit;
}

// Récupère les infos
$id_utilisateur = $_SESSION['user_id'];

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête SQL pour les annonces sauvegardées par cet utilisateur
$sql = "SELECT p.*, d.lien 
        FROM Annonces_Sauvegardees s
        JOIN Projets p ON s.id_projet = p.id
        LEFT JOIN Documents d ON p.id = d.projet AND d.type = 'image'
        WHERE s.id_utilisateur = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hutbox – Start-Hut</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-khaleb.css">
</head>
<body>
    <div class="content">   
        <h2>📦 Mes annonces sauvegardées</h2>
        <div class="grid">
            <?php
            while($row = $result->fetch_assoc()) {
                echo "<a href='/Forloopix/src/views/annonce.php?id=" . $row['id'] . "&from=hutbox' class='nav-links'>";
                echo "<figure>";
                echo "<img src='" . ($row["lien"] ?? 'https://vection-cms-prod.s3.eu-central-1.amazonaws.com/Adobe_Stock_525614074_8ab9bd18e3.jpeg') . "'>";
                echo "<figcaption>";
                echo "<h3>" . htmlspecialchars($row["annonce_titre"]) . "</h3>";
                echo "<p>" . htmlspecialchars($row["annonce_description"]) . "</p>";
                echo "</figcaption>";
                echo "</figure>";
                echo "</a>";

            }
            ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
