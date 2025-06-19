<?php
error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="La solution de pilotage et d'analyse pour vos attractions.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/styles-meryem.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Start-Hut - Contrôle d'Attractions</title>
    <?php include('../src/templates/head.php'); ?>
</head>

<body>
<?php include('../src/templates/header.php'); ?>    

<div class="content">

    <!-- Hero Section -->
    <section class="landing">
        <div class="landing-text">
            <h1>La gestion de vos attractions, <span class="highlight">réinventée.</span></h1>
            <p>Pilotez, analysez et optimisez vos manèges avec une précision inégalée.<br>Données en temps réel, sécurité renforcée.</p>
            <div class="boutons-collaborateur">
                <a href="../src/views/accueil.php" class="btn-collab">Accéder au panel</a>
                <a href="#features" class="btn-projet">Découvrir</a>
            </div>
        </div>
        <div class="landing-image">
            <img src="assets/img/landing/Landing2.png" alt="Panneau de contrôle d'attraction">
        </div>
    </section>

    <!-- Section des fonctionnalités -->
    <section id="features" class="pourquoi-section">
        <div class="conteneur-pourquoinouschoisir">
            <div class="imagepourquoinouschoisir">
                <img src="assets/img/landing/Landing1.png" alt="Personnes dans un manège">
            </div>
            <div class="textepourquoinouschoisir">
                <h2>Une suite complète pour une gestion parfaite.</h2>
                <div class="liste-choix">
                    <figure>
                        <i class="fa-solid fa-gauge-high fa-3x" style="color: #27ae60;"></i>
                        <figcaption>
                            <h3>Tableau de Bord Live</h3>
                            <p>Gardez le contrôle total avec une vue d'ensemble en temps réel : démarrages, capteurs de position et 'Cri-o-mètre'.</p>
                        </figcaption>
                    </figure>
                    <figure>
                        <i class="fa-solid fa-chart-line fa-3x" style="color: #27ae60;"></i>
                        <figcaption>
                            <h3>Statistiques Détaillées</h3>
                            <p>Analysez les performances sur le long terme. Suivez le nombre de visiteurs, les lancements et les pics sonores par attraction.</p>
                        </figcaption>
                    </figure>
                    <figure>
                        <i class="fa-solid fa-sliders fa-3x" style="color: #27ae60;"></i>
                        <figcaption>
                            <h3>Gestion des Capteurs</h3>
                            <p>Ajustez la sensibilité de vos capteurs et personnalisez les seuils d'alerte pour une sécurité et une maintenance optimales.</p>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section "Conçu pour les opérateurs" -->
    <section class="section-collaborateur">
        <div class="conteneur-collaborateur">
            <div class="image-collaborateur">
                <img src="assets/img/landing/Landing3.png" alt="Opérateur de manège">
            </div>
            <div class="texte-collaborateur">
                <h2>Conçu pour les Opérateurs</h2>
                <p class="soustitre">Parce que chaque seconde compte.</p>
                <p class="description">
                    Notre interface a été pensée pour être intuitive, rapide et fiable, même en conditions réelles. Moins de temps de formation, plus de temps pour assurer la sécurité et le plaisir de vos visiteurs.
                </p>
                <ul class="liste-arguments">
                    <li>Prise en main immédiate et sans effort</li>
                    <li>Des données claires pour des décisions rapides</li>
                    <li>Interface accessible sur tablette et mobile</li>
                </ul>
            </div>
        </div>
    </section>

</div>

<?php include('../src/templates/footer.php'); ?>    
</body>
</html>
