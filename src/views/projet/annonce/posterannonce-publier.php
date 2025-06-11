<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start(); // démarrage de la session

// Vérification de la connexion de l'utilisateur
if(!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: /Forloopix/src/views/user/connexion.php");
    exit();
}

// Vérification des données de session nécessaires
$missing_data = false;
$required_fields = ['titre', 'description', 'categorie', 'competences', 'remuneration', 'abonnement'];
foreach($required_fields as $field) {
    if(!isset($_SESSION[$field]) || empty($_SESSION[$field])) {
        $missing_data = true;
        break;
    }
}

// Redirection si des données sont manquantes
if($missing_data) {
    header("Location: posterannonce.php");
    exit();
}

// Vérifier si les valeurs existent dans $_SESSION, sinon définir des valeurs par défaut
$annonce_titre = $_SESSION['titre'] ?? 'A définir';
$annonce_description = $_SESSION['description'] ?? 'A définir';
$annonce_competences_recherchees = $_SESSION['competences'] ?? 'A définir';
$annonce_categorie = $_SESSION['categorie'] ?? 'A définir';
$annonce_collaborateurs_souhaites = $_SESSION['collaborateurs'] ?? 0; // Nombre donc par défaut 0
$annonce_remuneration = $_SESSION['remuneration'] ?? 0; // Nombre donc par défaut 0
$annonce_abonnement = $_SESSION['abonnement'] ?? 'basic'; // Par défaut "basic"
$roles = $_SESSION['roles'] ?? null;

// Traitement du formulaire de publication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }
    
    // Préparation des données pour insertion
    $createur = $_SESSION['user_id'];
    $nom = $_SESSION['titre'] ?? 'Projet sans nom';
    $titre = $_SESSION['titre'];
    $description = $_SESSION['description'];
    $categorie = $_SESSION['categorie'];
    $competences = $_SESSION['competences'];
    $collaborateurs = $_SESSION['collaborateurs'] ?? 1;
    $remuneration = $_SESSION['remuneration'];
    $abonnement = $_SESSION['abonnement'];
    
    // Insertion dans la base de données
    $sql = "INSERT INTO Projets (createur, nom, annonce_titre, annonce_description, principe_du_projet, annonce_competences_recherchees, 
            annonce_categorie, annonce_collaborateurs_souhaites, 
            annonce_date_creation, annonce_remuneration, annonce_etat) 
            VALUES ('$createur', '$nom', '$titre', '$description','$description', '$competences', 
            '$categorie', '$collaborateurs', NOW(), $remuneration, 'ouvert')";
    
    if ($conn->query($sql) === TRUE) {
        // Effacer les données de session utilisées pour l'annonce
        foreach($required_fields as $field) {
            unset($_SESSION[$field]);
        }
        unset($_SESSION['collaborateurs']);
        
        // Redirection vers la page des annonces avec un message de succès
        header("Location: /Forloopix/src/views/projet/espace-projet.php?success=1");
        exit();
    } else {
        $error = "Erreur lors de la publication: " . $conn->error;
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-meryem.css">
        <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
        <title>Publier votre annonce - Start-Hut</title>
        <?php include('../../../templates/head.php'); ?>
    </head>
    <body>
        <?php include('../../../templates/header.php'); ?>
        
        <div class="content">
            <!-- Barre de progression -->
            <div class="progress-container">
                <div class="progress-step"><span>1</span> Aperçu</div>
                <div class="progress-separator">></div>
                <div class="progress-step"><span>2</span> Abonnement</div>
                <div class="progress-separator">></div>
                <div class="progress-step active"><span>3</span> Publier</div>
            </div>
            
            <div class="container-aperçu">
                <h2>📝 Aperçu de votre annonce</h2>
                <h3 class="verfier">Vérifiez toutes les informations avant publication</h3>
                
                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <p><strong>Titre :</strong> <?php echo htmlspecialchars($annonce_titre); ?></p>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($annonce_description); ?></p>
                <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($annonce_categorie); ?></p>
                <p><strong>Compétences :</strong> <?php echo htmlspecialchars($annonce_competences_recherchees); ?></p>
                <p><strong>Nombre de collaborateurs :</strong> <?php echo htmlspecialchars($annonce_collaborateurs_souhaites); ?></p>
                <p><strong>Rémunération :</strong> <?php echo htmlspecialchars($annonce_remuneration); ?></p>
                <p><strong>✅ Abonnement choisi :</strong> <?php echo htmlspecialchars($annonce_abonnement); ?></p>
            </div>
            
            <div class="navigation-buttons">
                <form method="POST">
                    <button type="submit" class="next-btn">Publier</button>
                    <div id="message-success" class="message success" style="display: none;">Annonce publiée !</div>
                </form>
                <a href="posterannonce-abonnement.php" class="back-btn">Retour</a>
            </div>
        </div>
        
        <?php include('../../../templates/footer.php'); ?>
    </body>
</html>