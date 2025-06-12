<?php
error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran

require_once('../attractions_data.php');

// Détermine l'attraction à afficher. 'goudurix' par défaut.
$attraction_id = 'goudurix'; // ID par défaut
if (isset($_GET['attraction']) && array_key_exists($_GET['attraction'], $attractions)) {
    $attraction_id = $_GET['attraction'];
}
$current_attraction = $attractions[$attraction_id];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Tableau de bord pour la gestion des manèges.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@700&display=swap" rel="stylesheet">
    <title>Tableau de Bord - <?= htmlspecialchars($current_attraction['name']) ?></title>
    <?php include('../templates/head.php'); ?>
</head>

<body>
<?php include('../templates/header.php'); ?>    

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Panneau de Contrôle - <?= htmlspecialchars($current_attraction['name']) ?></h1>
    </div>

    <div class="control-panel">
        <div class="panel-main">
            <div class="segment-display-container">
                <p class="panel-title">COMPTEUR PASSAGES</p>
                <div class="segment-display">03</div>
            </div>
            <div class="seats-container">
                <p class="panel-title">PLACES OCCUPÉES</p>
                <div class="seats-grid">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                        <div class="seat <?= $i < 5 ? 'occupied' : '' ?>"></div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="ride-mockup">
                <div class="track-title">POSITION DU WAGON</div>
                <img src="<?= htmlspecialchars($current_attraction['image']) ?>" alt="Maquette du manège" class="ride-image">
                <div class="sensor sensor-1 active"></div>
                <div class="sensor sensor-2"></div>
                <div class="sensor sensor-3"></div>
            </div>
        </div>
        <div class="panel-bottom">
            <div class="sonometer">
                <p class="panel-title">CRI-O-MÈTRE</p>
                <div class="sonometer-bar">
                    <div class="sonometer-level" style="width: 75%;"></div>
                </div>
                <div class="sonometer-value">-12 dB</div>
            </div>

            <div class="controls">
                <button class="btn-control start">START</button>
                <button class="btn-control stop">STOP</button>
            </div>
        </div>
    </div>
</div>

<?php include('../templates/footer.php'); ?>    
</body>
</html>
