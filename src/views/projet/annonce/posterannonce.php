<?php
session_start(); // Starts or resumes a session
// Checks if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirects to the login page if the user is not logged in
    header('Location: /Forloopix/src/views/user/connexion.php');
    exit(); // Stops script execution after redirection
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
        <title>Poster une annonce - Start-Hut</title>
        <?php include('../../../templates/head.php'); ?>
    </head>
    <body>
        <?php include('../../../templates/header.php'); ?>
        
        <div class="content">
            <!-- Sous barre (étapes pour poster une annonce) -->
            <div class="progress-container">
                <div class="progress-step active"><span>1</span> Aperçu</div>
                <div class="progress-separator">></div>
                <div class="progress-step"><span>2</span> Abonnement</div>
                <div class="progress-separator">></div>
                <div class="progress-step"><span>3</span> Publier</div>
            </div>
             
            <!-- Barre de progression  -->
            <div class="containerposter">
                <form action="posterannonce-abonnement.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="titre">Titre <span class="highlight2">*</span></label>
                        <small>Le titre de votre projet est le meilleur endroit pour inclure les mots-clés que les collaborateurs utiliseront pour rechercher un projet comme le vôtre</small>
                        <input type="text" id="titre" name="titre" maxlength="80" required value="<?php echo htmlspecialchars($_SESSION['titre'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Description<span class="highlight2">*</span></label>
                        <small>Décrivez votre projet</small>
                        <input type="text" id="description" name="description" maxlength="250" required value="<?php echo htmlspecialchars($_SESSION['description'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="categorie">Catégorie <span class="highlight2">*</span></label>
                        <small>Choisissez la catégorie qui convient le mieux à votre projet</small>
                        <select id="categorie" name="categorie" required>
                            <option value="">Choisissez une catégorie</option>
                            <option value="technologies" <?php echo (isset($_SESSION['categorie']) && $_SESSION['categorie'] == 'technologies') ? 'selected' : ''; ?>>Technologie</option>
                            <option value="education" <?php echo (isset($_SESSION['categorie']) && $_SESSION['categorie'] == 'education') ? 'selected' : ''; ?>>Education</option>
                            <option value="business" <?php echo (isset($_SESSION['categorie']) && $_SESSION['categorie'] == 'business') ? 'selected' : ''; ?>>Business</option>
                            <option value="autre" <?php echo (isset($_SESSION['categorie']) && $_SESSION['categorie'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="competences">Compétences recherchées <span class="highlight2">*</span> </label>
                        <small>Les compétences permettent aux collaborateurs captivés par votre projet de savoir s'ils peuvent en faire partie ou non</small>
                        <select id="competences" name="competences" required>
                            <option value="">Choisissez une ou plusieurs compétences</option>
                            <option value="developpeur" <?php echo (isset($_SESSION['competences']) && $_SESSION['competences'] == 'developpeur') ? 'selected' : ''; ?>>Développement</option>
                            <option value="designer" <?php echo (isset($_SESSION['competences']) && $_SESSION['competences'] == 'designer') ? 'selected' : ''; ?>>Designer</option>
                            <option value="marketing" <?php echo (isset($_SESSION['competences']) && $_SESSION['competences'] == 'marketing') ? 'selected' : ''; ?>>Marketing</option>
                            <option value="communication" <?php echo (isset($_SESSION['competences']) && $_SESSION['competences'] == 'communication') ? 'selected' : ''; ?>>Communication</option>
                            <option value="autre" <?php echo (isset($_SESSION['competences']) && $_SESSION['competences'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="collaborateurs">Nombre de collaborateurs souhaités</label>
                        <small>Le nombre de collaborateurs permettra aux potentiels collaborateurs de savoir s'il y a encore des places disponibles</small>
                        <input type="number" id="collaborateurs" name="collaborateurs" min="1" value="<?php echo htmlspecialchars($_SESSION['collaborateurs'] ?? '1'); ?>">
                    </div>
                    <div class="form-group2">
                        <label for="remuneration">Rémunération <span class="highlight2">*</span></label>
                        <input type="number" id="remuneration" name="remuneration" required value="<?php echo htmlspecialchars($_SESSION['remuneration'] ?? ''); ?>">
                    </div>
                
                    <button type="submit" class="submit-btn">Sauvegarder & Continuer</button>
                </form>
            </div>
        </div>

        <?php include('../../../templates/footer.php'); ?>
    </body>
</html>
