<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-meryem.css">
        <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
        <title>Abonnements - Start-Hut</title>
        <?php include('../templates/head.php'); ?>
    </head>
    <body>
        <?php include('../templates/header.php'); ?>             <!-- Rajoute le header par la magie de PHP  -->
        <div class="content">                       <!-- on mets tout dans cette classe pour que les info soient centré -->
        
        <div class="containerabonnementconsulter">
        
            <!-- Si vous avez dautre idées pour le textes et les prix nhesitez pas -->
        
        <h2>Découvrez nos formules d’abonnement</h2>
        <h1>Consultez nos offres et choisissez celle qui correspond le mieux à vos besoins. Chaque formule vous donne accès à des fonctionnalités spécifiques pour développer vos projets efficacement.</h1>
       
        <div class="plans">
             <!-- Bloc représentant l'offre BASIC -->
             <div class="plan basic">
                <h2>BASIC</h2>
                
                <p class="price">Gratuit</p>
                
                <ul> <!-- avantage de loffre-->
                
                    <li>Publier des projets</li>
                    <li>Visibilité standard</li>
                    <li>Pas de mise en avant</li>
                    <li> Statistiques de projet non disponibles</li>
                </ul>
  
                <p class=""></p>
               
            </div> 
            <!-- Bloc représentant l'offre STANDARD -->
            <div class="plan standard">
                <h2>STANDARD</h2>
                <p class="price">9.99€/mois</p>
                <ul> <!-- avantage de loffre-->
                    <li>Tous les avantages du forfait Basic</li>
                    <li>Mise en avant des publications</li>
                    <li>Suivi de projet (barre d’avancement, outils collaboratifs de base)</li>
                    <li>Mise en avant de vos projets</li>
                </ul>
                <p class=""></p>
                
                
            </div>
           <!-- Bloc représentant l'offre PREMIUM -->
            <div class="plan premium">
            <h2>PREMIUM</h2>
                <p class="price">19.99€/mois</p>
                <ul> <!-- avantage de loffre-->
                    <li>Tous les avantages du forfait Standard</li>
                    <li>Permet aux publications d'apparaître en tête du site</li>
                    <li>Accès à des ressources pédagogiques exclusives</li>
                    <li>Documents d’étude de marché préremplis et personnalisables</li>
                </ul>
                
                <p class="prie"></p>
            
        </div>
        </div>
    </div>
    </div>
    
        <?php include('../templates/footer.php'); ?>    
    </body>
</html>