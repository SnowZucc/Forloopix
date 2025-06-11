<!-- Inclusion automatique de la page Mes candidatures par défaut -->
 <?php
$page = $_GET['view'] ?? 'historique';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Collaborateur</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <?php include('../../templates/head.php'); ?>
</head>
<body>

<?php include('../../templates/header.php'); ?>
<!-- Barre de navigation secondaire --> 
<nav class="sub-navbar">
    <ul>
        <li><a href="/Forloopix/src/views/projet/espace-collaborateur.php?view=historique" class="<?= $page == 'historique' ? 'active' : '' ?>">Mes candidatures</a></li>
        <li><a href="/Forloopix/src/views/projet/espace-collaborateur.php?view=Hutbox" class="<?= $page == 'Hutbox' ? 'active' : '' ?>">Hutbox</a></li>

    </ul>
</nav>

<?php 
if ($page === 'historique') {
    include('historique.php');
} elseif ($page === 'dashbord') {
    include('dashbord.php');
} elseif ($page === 'Hutbox') {
    include('Hutbox.php');
} else {
    echo "<p style='color:red;'>Erreur : vue inconnue</p>";
}
?>

<?php include('../../templates/footer.php'); ?>

</body>
</html>
