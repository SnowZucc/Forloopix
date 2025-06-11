<?php
session_start(); // Démarrage de la session

// Traitement de la suppression d'annonce
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if(!isset($_SESSION['user_id'])) {
        // Utilisateur non connecté ou session expirée
        $_SESSION['message_espace_projet'] = "Vous devez être connecté pour effectuer cette action.";
        $_SESSION['message_espace_projet_type'] = "error";
        header("Location: espace-projet.php"); // Rediriger pour afficher le message
        exit();
    }

    require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        $_SESSION['message_espace_projet'] = "Erreur de connexion à la base de données.";
        $_SESSION['message_espace_projet_type'] = "error";
    } else {
        $delete_id = $_GET['delete'];
        $user_id = $_SESSION['user_id'];
        
        $conn->begin_transaction(); // Démarrer la transaction

        try {
            // Vérifier que l'annonce appartient bien à l'utilisateur
            $stmt_check = $conn->prepare("SELECT id FROM Projets WHERE id = ? AND createur = ?");
            $stmt_check->bind_param("ii", $delete_id, $user_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if($result_check->num_rows > 0) {
                // D'abord, supprimer les documents associés au projet
                $stmt_delete_docs = $conn->prepare("DELETE FROM Documents WHERE projet = ?");
                $stmt_delete_docs->bind_param("i", $delete_id);
                if (!$stmt_delete_docs->execute()) {
                    throw new Exception("Erreur lors de la suppression des documents associés.");
                }
                $stmt_delete_docs->close();

                // Ensuite, supprimer l'annonce (projet)
                $stmt_delete_projet = $conn->prepare("DELETE FROM Projets WHERE id = ?");
                $stmt_delete_projet->bind_param("i", $delete_id);
                if ($stmt_delete_projet->execute()) {
                    $conn->commit(); // Valider la transaction
                    $_SESSION['message_espace_projet'] = "L'annonce et les documents associés ont été supprimés avec succès.";
                    $_SESSION['message_espace_projet_type'] = "success";
                } else {
                    throw new Exception("Erreur lors de la suppression de l'annonce.");
                }
                $stmt_delete_projet->close();
            } else {
                $_SESSION['message_espace_projet'] = "Annonce non trouvée ou vous n'avez pas la permission de la supprimer.";
                $_SESSION['message_espace_projet_type'] = "error";
                // Pas besoin de rollback ici car aucune modification n'a été tentée si l'annonce n'est pas trouvée
            }
            $stmt_check->close();
        } catch (Exception $e) {
            $conn->rollback(); // Annuler la transaction en cas d'erreur
            $_SESSION['message_espace_projet'] = $e->getMessage();
            $_SESSION['message_espace_projet_type'] = "error";
        }
        $conn->close();
    }
    // Rediriger pour nettoyer l'URL et éviter la resoumission
    header("Location: espace-projet.php");
    exit();
}

error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Projet - Start-Hut</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
    <?php include('../../templates/head.php'); ?>
</head>
<body>

<?php include('../../templates/header.php'); ?>

<!-- Barre de navigation secondaire --> 
<nav class="sub-navbar">
    <ul>
    <li><a href="espace-projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'espace-projet.php' ? 'active' : '' ?>">Mes annonces</a></li>
    <li><a href="recrutement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'recrutement.php' ? 'active' : '' ?>">Recrutement</a></li>
    <li><a href="projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'projet.php' ? 'active' : '' ?>">Projet</a></li>
    <li><a href="ressource/ressources.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ressources.php' ? 'active' : '' ?>">Ressources</a></li>
    </ul>
</nav>

<!-- Inclusion automatique de la page Mes annonces par défaut -->
<?php include('annonce/monannonce.php'); ?>

<?php include('../../templates/footer.php'); ?>

</body>
</html>
