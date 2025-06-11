<?php
session_start(); // démarre la session

// Vérification de la connexion de l'utilisateur
if(!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: /Forloopix/src/views/user/connexion.php");
    exit();
}

// La logique de suppression a été déplacée vers espace-projet.php
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Mes annonces</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
      <?php include('../../../templates/head.php'); ?>
      <script>
      // Script pour la confirmation de suppression
      function confirmDelete(id) {
          if(confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) {
              window.location.href = '/Forloopix/src/views/projet/espace-projet.php?delete=' + id;
          }
      }
      </script>
  </head>
  <body>
    <!-- <?php include('../../../templates/header.php'); ?> -->   <!-- Retiré car inclus dans espace projets -->

    <div class="content">
        <h2 class="page-title">Mes annonces</h2>

        <?php 
        // Affichage des messages de l'espace projet (succès/erreur de suppression)
        if(isset($_SESSION['message_espace_projet'])):
        ?>
            <div class="<?= $_SESSION['message_espace_projet_type'] == 'success' ? 'success-message' : 'error-message' ?>">
                <?php 
                echo $_SESSION['message_espace_projet']; 
                unset($_SESSION['message_espace_projet']); // Supprimer le message après l'affichage
                unset($_SESSION['message_espace_projet_type']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): // Conserver pour la confirmation de publication d'annonce ?>
            <div class="success-message">Votre annonce a été publiée avec succès !</div>
        <?php endif; ?>

        <?php
        require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Utiliser des requêtes préparées pour éviter les injections SQL
        $stmt = $conn->prepare("SELECT * FROM Projets WHERE createur = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Pour chaque annonce dans la db, on boucle et on affiche les informations de l'annonce
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {  // fetch_assoc() récupère les données sous forme de tableau associatif
                echo '<div class="annonce-container">';
                echo '<div class="annonce-image">';
                
                // Récupérer l'image associée au projet si elle existe
                $img_stmt = $conn->prepare("SELECT lien FROM Documents WHERE projet = ? AND type = 'image' LIMIT 1");
                $img_stmt->bind_param("i", $row["id"]);
                $img_stmt->execute();
                $img_result = $img_stmt->get_result();
                
                if($img_result->num_rows > 0) {
                    $img_row = $img_result->fetch_assoc();
                    echo '<img src="' . htmlspecialchars($img_row["lien"]) . '">';
                } else {
                    // Image par défaut
                    echo '<img src="https://vection-cms-prod.s3.eu-central-1.amazonaws.com/Adobe_Stock_525614074_8ab9bd18e3.jpeg">';
                }
                
                echo '</div>';
                
                echo '<div class="annonce-info">';
                echo '<h3>' . htmlspecialchars($row["annonce_titre"]) . '</h3>';
                echo '<p>' . htmlspecialchars($row["annonce_description"]) . '</p>';
                echo '<p>Catégorie: ' . htmlspecialchars($row["annonce_categorie"]) . '</p>';
                echo '<p>Compétences recherchées: ' . htmlspecialchars($row["annonce_competences_recherchees"]) . '</p>';
                echo '<p>Collaborateurs souhaités: ' . htmlspecialchars($row["annonce_collaborateurs_souhaites"]) . '</p>';
                echo '</div>';

                echo '<div class="etat-annonce">';
                echo '<p>État: ' . htmlspecialchars($row["annonce_etat"]) . '</p>';
                echo '<p>Date de publication: ' . date('d/m/Y', strtotime($row["annonce_date_creation"])) . '</p>';
                echo '</div>';

                echo '<div class="button-container">';
                echo '<button class="delete-button" onclick="confirmDelete(' . $row["id"] . ')">Supprimer</button>';
                echo '<a href="/Forloopix/src/views/projet/annonce/modifier_annonces.php?edit=' . $row["id"] . '" class="edit-button">Modifier</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="no-annonces">';
            echo '<p>Vous n\'avez pas encore publié d\'annonces.</p>';
            echo '<a href="annonce/posterannonce.php" class="create-button">Créer une annonce</a>';
            echo '</div>';
        }
        
        $conn->close();
        ?>

    </div>

  </body>
</html>
