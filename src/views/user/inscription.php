<?php
// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

// Inclure le fichier de configuration
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// Variables de messages
$successMessage = "";
$errorMessage = "";

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['lastname'] ?? null;
    $prenom = $_POST['firstname'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $statut = $_POST['statut'] ?? null;

    if ($nom && $prenom && $email && $password && $statut) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }

        // Vérifier si l'e-mail est déjà utilisé
        $stmt = $conn->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errorMessage = "Cet email est déjà enregistré.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $description_profil = "Nouvel utilisateur";
            $langues_parlees = "Français";

            $stmt = $conn->prepare("INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, description_profil, langues_parlees, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $nom, $prenom, $email, $hashed_password, $description_profil, $langues_parlees, $statut);

            if ($stmt->execute()) {
                $successMessage = "Inscription réussie !";
            } else {
                $errorMessage = "Erreur lors de l'inscription.";
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        $errorMessage = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inscription - Start-Hut</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css?v=4">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css?v=2">
    <?php include('../../templates/head.php'); ?>
</head>

<body>
<?php include('../../templates/header.php'); ?>
<div class="content">
    <h1 class="inscription-title">INSCRIPTION</h1>

    <?php if ($successMessage): ?>
        <div class="confirmation success"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="confirmation error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form action="inscription.php" method="POST" class="inscription">
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="lastname">Nom <span class="required">*</span></label>
                    <input type="text" id="lastname" name="lastname" class="input-field" placeholder="Votre nom" required>
                </div>

                <div class="form-group half-width">
                    <label for="firstname">Prénom <span class="required">*</span></label>
                    <input type="text" id="firstname" name="firstname" class="input-field" placeholder="Votre prénom" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="input-field" placeholder="Veuillez saisir votre adresse mail" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="input-field" placeholder="Veuillez saisir votre mot de passe" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer votre mot de passe <span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" placeholder="Veuillez confirmer votre mot de passe" required>
            </div>

            <div class="form-group">
                <label>Statut <span class="required">*</span></label>
                <div class="statut-container">
                    <div class="statut-option">
                        <input type="radio" id="manager" name="statut" value="manager" required>
                        <label for="manager">Manager</label>
                    </div>
                    <div class="statut-option">
                        <input type="radio" id="agent" name="statut" value="agent">
                        <label for="agent">Agent</label>
                    </div>
                </div>
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="cgu" required>
                <label for="cgu">J'ai lu et j'accepte les <a href="../legal/cgu.php">Conditions Générales d'Utilisation</a></label>
            </div>

            <div class="button-container">
                <button type="submit" class="btnInscription">S'inscrire</button>
            </div>
        </form>
    </div>

    <p class="already-registered">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
</div>

<?php include('../../templates/footer.php'); ?>
</body>
</html>
