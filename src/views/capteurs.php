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
                <h3 class="setting-title">Échelle du Sonomètre</h3>
                <p class="setting-description">Activer pour utiliser une échelle logarithmique pour le Cri-o-mètre.</p>
            </div>
            <div class="setting-control">
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Seuil d'alerte sonore</h3>
                <p class="setting-description">Définir le niveau de dB à partir duquel une alerte est visible.</p>
            </div>
            <div class="setting-control">
                <input type="number" class="setting-input" value="-10">
                <span style="margin-left: 5px;">dB</span>
            </div>
        </div>
        
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Sensibilité des capteurs de position</h3>
                <p class="setting-description">Régler la sensibilité de détection du wagon.</p>
            </div>
            <div class="setting-control">
                 <select class="setting-select">
                    <option>Basse</option>
                    <option selected>Moyenne</option>
                    <option>Haute</option>
                </select>
            </div>
        </div>
    </div>
</div>

<?php include('../templates/footer.php'); ?>
</body>
</html> 