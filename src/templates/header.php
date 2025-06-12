<?php
error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran
?>
<div class="navbar-container">
    <nav class="navbar">
        <div class="logo">
            <a href="/Forloopix/public/index.php"><img src="/Forloopix/public/assets/img/logo.png" alt="Logo"></a>
        </div>


<!-- Bouton hamburger -->
<button class="hamburger" onclick="toggleMenu()">
  ☰
</button>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_type = $_SESSION['user_type'] ?? null;
?>

<ul class="nav-links mobile-menu">
  <?php if ($user_type === 'manager'): ?>
    <li><a href="/Forloopix/src/views/annonces.php">Accueil</a></li>
    <li><a href="/Forloopix/src/views/projet/annonce/posterannonce.php">Tableau</a></li>
    <li><a href="/Forloopix/src/views/abonnements.php">Statistiques</a></li>
    <li><a href="/Forloopix/src/views/faq.php">Capteurs</a></li>
  <?php elseif ($user_type === 'agent'): ?>
    <li><a href="/Forloopix/src/views/annonces.php">Accueil</a></li>
    <li><a href="/Forloopix/src/views/projet/annonce/posterannonce.php">Tableau</a></li>
    <li><a href="/Forloopix/src/views/abonnements.php">Statistiques</a></li>
  <?php else: ?>
    <li><a href="/Forloopix/src/views/abonnements.php">Statistiques</a></li>
  <?php endif; ?>
</ul>

<div class="auth-buttons mobile-menu">
  <?php if (!isset($_SESSION['user_id'])): ?>
    <a href="/Forloopix/src/views/user/connexion.php" class="signup">Se connecter</a>
  <?php else: ?>
    <a href="/Forloopix/src/views/user/profil.php" class="signup">Mon profil</a>
    <a href="/Forloopix/src/views/admin.php" class="signup">Panel Admin</a>

    <?php
    /* Commentaire de bloc pour le débogage
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT d.lien FROM Documents d WHERE d.proprietaire = ? AND d.type = 'image'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_image = $result->fetch_assoc();
    $conn->close();
    */
    $user_image = []; // Valeur par défaut pour éviter les erreurs sur la ligne suivante si le bloc est commenté
    ?>

    <a href="/Forloopix/src/views/user/profil.php" class="profile-image-link">
      <img src="<?php echo $user_image['lien'] ?? '/Forloopix/public/assets/img/APRIL.png'; ?>" class="header-profile-pic" alt="Photo de profil">
    </a>
  <?php endif; ?>
</div>


    <div class="navbar-border"></div> <!-- Bordure largeur de tout l'écran-->
</div>

<script>
  function toggleMenu() {
    document.querySelector('.nav-links').classList.toggle('show');
    document.querySelector('.auth-buttons').classList.toggle('show');
  }
</script>


