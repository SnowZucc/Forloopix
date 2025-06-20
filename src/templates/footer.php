<!-- Style pour retirer le soulignage et l'effet bleu des liens -->
 <style>
    footer a {
        text-decoration: none;
        color: inherit;
    }
</style>

<footer>
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    include('messagerie.php'); 
}

$user_type = $_SESSION['user_type'] ?? null;
?>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Navigation</h3>
            <p><a href="/Forloopix/public/index.php">Accueil</a></p>
            <p><a href="/Forloopix/src/views/abonnements.php">Tarification</a></p>
            <p><a href="/Forloopix/src/views/contact.php">Contact</a></p>
        </div>

        <div class="footer-section">
            <h3>Mon Espace</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p><a href="/Forloopix/src/views/user/profil.php">Mon Profil</a></p>
                <?php if ($user_type === 'admin'): ?>
                    <p><a href="/Forloopix/src/views/statistiques.php">Statistiques</a></p>
                    <p><a href="/Forloopix/src/views/accueil.php">Tableau</a></p>
                    <p><a href="/Forloopix/src/views/capteurs.php">Capteurs</a></p>
                    <p><a href="/Forloopix/src/views/admin.php">Panneau Admin</a></p>
                <?php elseif ($user_type === 'agent'): ?>
                    <p><a href="/Forloopix/src/views/accueil.php">Tableau</a></p>
                <?php else: ?>
                    <p><a href="/Forloopix/src/views/statistiques.php">Statistiques</a></p>
                <?php endif; ?>
                <p><a href="/Forloopix/src/views/user/logout.php">Déconnexion</a></p>
            <?php else: ?>
                <p><a href="/Forloopix/src/views/user/connexion.php">Connexion</a></p>
                <p><a href="/Forloopix/src/views/user/inscription.php">Inscription</a></p>
            <?php endif; ?>
        </div>
                             
        <div class="footer-section">
            <h3>Support</h3>
            <p><a href="/Forloopix/src/views/faq.php">FAQ</a></p>
            <p><a href="/Forloopix/src/views/contact.php">Nous contacter</a></p>
        </div>
            
        <div class="footer-section">
            <h3>Légal</h3>
            <p><a href="/Forloopix/src/views/legal/cgu.php">CGU</a></p>
            <p><a href="/Forloopix/src/views/legal/mentions.php">Mentions légales</a></p>
        </div>
    </div>
    <div class="footer-bottom">
        © <?php echo date("Y"); ?> StartHut - Tous droits réservés.
    </div>
</footer>
