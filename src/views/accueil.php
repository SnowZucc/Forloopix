<?php require_once('../attractions_data.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sélection du manège">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <title>Choisir une attraction</title>
    <?php include('../templates/head.php'); ?>
</head>
<body>
<?php include('../templates/header.php'); ?>    

<div class="accueil-container">
    <div class="accueil-header">
        <h1>Choisir une attraction</h1>
    </div>

    <div class="attraction-grid">
        <?php foreach ($attractions as $id => $attraction): ?>
            <a href="tableau.php?attraction=<?= htmlspecialchars($id) ?>" class="attraction-card">
                <img src="<?= htmlspecialchars($attraction['image']) ?>" alt="<?= htmlspecialchars($attraction['name']) ?>" class="attraction-image">
                <div class="attraction-name-overlay">
                    <span class="attraction-name"><?= htmlspecialchars($attraction['name']) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include('../templates/footer.php'); ?>    
</body>
</html> 