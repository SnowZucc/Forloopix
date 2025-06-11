
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
session_start();

$porteur_id = $_SESSION['user_id'] ?? null;
$projet = null;

if ($porteur_id) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Préparation de la requête : on suppose qu’on veut récupérer le projet du porteur connecté
    $stmt = $conn->prepare("
        SELECT annonce_description, annonce_titre, principe_du_projet
        FROM Projets
        WHERE createur = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $porteur_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $projet = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Projet - Start-Hut</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-fatma.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <?php include('../../templates/head.php'); ?>
</head>
<body>

<?php include('../../templates/header.php'); ?>

<!-- Barre de navigation secondaire --> 
<nav class="sub-navbar">
    <ul>
    <li><a href="espace-projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'espace-projet.php' ? 'active' : '' ?>">Mes annonces</a></li>
    <li><a href="recrutement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'recrutement.php' ? 'active' : '' ?>">Recrutement</a></li>
    <li><a href="projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'projet.php' ? 'active' : '' ?>">Projet</a></li>
    <li><a href="ressource/ressources.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ressources.php' ? 'active' : '' ?>">Ressources</a></li>
    </ul>
</nav>
<section class="projet-description">
    <div class="container">
        <?php if ($projet): ?>
            <h1><?= htmlspecialchars($projet['annonce_titre']) ?></h1>
            
            
            <h3>Principe du projet</h3>
            <p class="intro"><?= nl2br(htmlspecialchars($projet['annonce_description'])) ?></p>
        <?php else: ?>
            <p class="intro">Aucun projet trouvé pour votre compte.</p>
        <?php endif; ?>
    </div>
</section>





<?php include('../../templates/footer.php'); ?>

</body>
</html>
