<!-- Style pour retirer le soulignage et l'effet bleu des liens -->
 <style>
    footer a {
        text-decoration: none;
        color: inherit;
    }
</style>

<footer>
<?php include('messagerie.php'); ?>
       <div class="footer-container">


        <div class="footer-section">
            <h3>Navigation Principale</h3>


            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $user_type = $_SESSION['user_type'] ?? null;
            ?>

            <?php if ($user_type === 'admin'): ?>
            <p><a href="/Forloopix/public/index.php">Accueil</a></p>
            <p><a href="/Forloopix/src/views/annonces.php">Annonces</a></p>
            <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>
            <?php endif; ?>

            <?php if ($user_type === 'manager'): ?>
            <p><a href="/Forloopix/public/index.php">Accueil</a></p>
            <p><a href="/Forloopix/src/views/annonces.php">Annonces</a></p>
            <p><a href="/Forloopix/src/views/projet/annonce/posterannonce.php">Poster une annonce</a></p>
            <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>
            
            <?php endif; ?>

            <?php if ($user_type === 'agent'): ?>
            <p><a href="/Forloopix/public/index.php">Accueil</a></p>
            <p><a href="/Forloopix/src/views/annonces.php">Annonces</a></p>
            <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>

            <?php endif; ?>


            <?php if (!isset($_SESSION['user_id'])): ?>
            <p><a href="/Forloopix/public/index.php">Accueil</a></p>
            <p><a href="/Forloopix/src/views/annonces.php">Annonces</a></p>
            <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>
            <?php endif; ?>
         
        </div>




        <div class="footer-section">
            <h3>Espace Client</h3>

            <?php if ($user_type === 'admin'): ?>
                <p><a href="/Forloopix/src/views/admin.php">Panneau admin</a></p>
                <p><a href="/Forloopix/src/views/user/profil.php">Mon profil</a></p>
                <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>  
            <?php endif; ?>

            <?php if ($user_type === 'manager'): ?>
                <p><a href="/Forloopix/src/views/projet/espace-projet.php" >Espace projet</a></p>
                <p><a href="/Forloopix/src/views/user/profil.php" >Mon profil</a></p>
                <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>  
            <?php endif; ?>

            <?php if ($user_type === 'agent'): ?>
                
                <p><a href="/Forloopix/src/views/user/profil.php" >Mon profil</a></p>
                <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>  

            <?php endif; ?>


            <?php if (!isset($_SESSION['user_id'])): ?>
            <p><a href="/Forloopix/src/views/user/connexion.php">Connexion</a></p>
            <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>
            <?php endif; ?>
         
          
        </div>
                             
        <div class="footer-section">
           
            <?php if ($user_type === 'admin'): ?>
            <h3>Administration</h3>
            <p><a href="/Forloopix/src/views/admin.php">Panneau admin</a></p>
            <p><a href="/Forloopix/src/views/faq.php">FAQ</a></p>
            <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>
            <?php endif; ?>

            <?php if ($user_type === 'manager'): ?>
            <h3>Mon projet</h3>
            <p><a href="/Forloopix/src/views/projet/espace-projet.php">Espace projet</a></p>
            <p><a href="/Forloopix/src/views/projet/espace-projet.php">Mes annonces</a></p>
            <p><a href="/Forloopix/src/views/projet/recrutement.php">Recrutement</a></p>
            <p><a href="/Forloopix/src/views/projet/ressource/ressources.php">Ressources</a></p>
            <?php endif; ?>

            <?php if ($user_type === 'agent'): ?>
            <h3>Mon projet</h3>
            <p><a href="/Forloopix/src/views/projet/espace-agent.php" >Espace agent</a></p>
            <p><a href="/Forloopix/src/views/projet/historique.php">Mes candidatures</a></p>
            <p><a href="/Forloopix/src/views/projet/dashbord.php">Dashbord</a></p>
        
            <?php endif; ?>
         
            <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Ajoute un contenu par défaut pour maintenir l'équilibre -->
        <h3>Découvrir</h3>
        <p><a href="/Forloopix/src/views/faq.php">FAQ</a></p>
        <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>
        <p><a href="/Forloopix/public/index.php">Explorer Start-Hut</a></p>
        <?php endif; ?>


  
        </div>
            
        
        <div class="footer-section">
            <h3>Légales</h3>
            <p><a href="/Forloopix/src/views/legal/cgu.php">CGU</a></p>
            <p><a href="/Forloopix/src/views/legal/mentions.php">Mentions légales</a></p>
            <p><a href="/Forloopix/src/views/legal/cookies.php">Politiques des cookies</a></p>
        </div>
    </div>
    <div class="footer-bottom">
   
    
    © 2025 StartHut - Tous droits réservés.
</div>

</footer>
