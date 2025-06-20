<?php
error_reporting(E_ERROR);
ini_set('display_errors', 0);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Gestion des capteurs">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <title>Gestion des Capteurs</title>
    <?php include('../templates/head.php'); ?>
</head>
<body>
<?php include('../templates/header.php'); ?>

<div class="settings-container">
    <div class="settings-header">
        <h1>Gestion des Capteurs</h1>
    </div>

    <div class="settings-panel">
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Temps avant arrêt après capteur final</h3>
                <p class="setting-description">Définit le délai en secondes entre l'activation du capteur de milieu de parcours et l'arrêt complet de l'attraction.</p>
            </div>
            <div class="setting-control">
                <input type="number" class="setting-input" id="ride-end-delay" value="15">
                <span style="margin-left: 5px;">secondes</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const delayInput = document.getElementById('ride-end-delay');

    // Charge la valeur depuis le localStorage si elle existe
    const savedDelay = localStorage.getItem('rideEndDelay');
    if (savedDelay) {
        delayInput.value = savedDelay;
    }

    // Sauvegarde la nouvelle valeur dans le localStorage à chaque changement
    delayInput.addEventListener('change', () => {
        localStorage.setItem('rideEndDelay', delayInput.value);
        console.log(`Délai sauvegardé : ${delayInput.value} secondes`);
    });
});
</script>

<?php include('../templates/footer.php'); ?>
</body>
</html> 