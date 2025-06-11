<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recrutement - Start-Hut</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
</head>
<body>

<?php include('../../templates/header.php'); ?>


   
<div class="content">                       <!-- on mets tout dans cette classe pour que les info soient centré -->
                <!-- Barre de navigation secondaire -->
                <nav class="sub-navbar">
                    <ul>
                    <li><a href="projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'projet.php' ? 'active' : '' ?>">Projet</a></li>
                    <li><a href="espace-projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'espace-projet.php' ? 'active' : '' ?>">Mes annonces</a></li>
                    <li><a href="recrutement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'recrutement.php' ? 'active' : '' ?>">Recrutement</a></li>
                    <li><a href="ressources.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ressources.php' ? 'active' : '' ?>">Ressources</a></li>
                    </ul>
                </nav>
                <nav class="sub-navbar-outils"> <!-- Barre de navigation des outils collaboratifs -->
                    <ul>
                    <li><a href="kanban.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kanban.php' ? 'active' : '' ?>">Kanban</a></li>
                    </ul>
                </nav>

                <div class = "Kanban-colonne">

                </div>  


            </div>



<?php include('../../templates/footer.php'); ?>

</body>
</html>
