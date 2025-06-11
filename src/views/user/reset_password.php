<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
require $_SERVER['DOCUMENT_ROOT'] . '/Forloopix/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$message = "";
$error = false;

// Fonction pour générer un mot de passe aléatoire
function generateTemporaryPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Vérifier si l'email existe dans la base de données
    $sql = "SELECT id FROM Utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        // Générer un mot de passe temporaire
        $temp_password = generateTemporaryPassword();
        $hashed_temp_password = password_hash($temp_password, PASSWORD_BCRYPT); // Hasher le mot de passe temporaire
        
        // Mettre à jour le mot de passe dans la base de données
        $update_sql = "UPDATE Utilisateurs SET mot_de_passe = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_temp_password, $email); // Utiliser le mot de passe hashé
        $update_result = $update_stmt->execute();
        
        if ($update_result) {
            // Configuration de PHPMailer pour utiliser OVH SMTP
            $mail = new PHPMailer(true);
            
            try {
                // Configuration du serveur
                $mail->CharSet = 'UTF-8'; // Définir l'encodage en UTF-8
                $mail->Encoding = 'base64'; // Utiliser l'encodage base64 pour le contenu
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port = SMTP_PORT;
                
                // Destinataires
                $mail->setFrom(SMTP_USERNAME, 'Start-Hut');
                $mail->addAddress($email);
                
                // Contenu
                $mail->isHTML(true);
                $mail->Subject = 'Start-Hut - Réinitialisation de votre mot de passe';
                $mail->Body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Réinitialisation de mot de passe</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .password { font-weight: bold; background-color: #f1f1f1; padding: 10px; margin: 10px 0; }
                        .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #6c757d; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="content">
                            <p>Bonjour,</br>
                            Vous avez demandé la réinitialisation de votre mot de passe. Voici votre nouveau mot de passe temporaire :</p>
                            <div class="password">' . $temp_password . '</div>
                            <p>Par mesure de sécurité, nous vous recommandons de changer ce mot de passe dès votre prochaine connexion.</p>
                            Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer cet e-mail ou contacter notre support si cela continue.</br>
                            <p>Cordialement,<br>L\'équipe Start-Hut</p>
                        </div>
                    </div>
                </body>
                </html>';
                
                $mail->AltBody = "Bonjour,\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nVotre nouveau mot de passe temporaire est : " . $temp_password . "\n\nNous vous recommandons de changer ce mot de passe dès votre prochaine connexion.\n\nCordialement,\nL'équipe Start-Hut";
                
                $mail->send();
            } catch (Exception $e) {
                // Ne pas révéler les erreurs détaillées à l'utilisateur pour des raisons de sécurité
                error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
        }
        
        $update_stmt->close();
    }
    
    // Toujours afficher le même message, qu'il y ait eu un succès ou non (sécurité)
    $message = "Si l'adresse email existe dans notre système, un mot de passe temporaire vous sera envoyé.";
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Start-Hut - Réinitialisation de mot de passe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css?v=4">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css?v=2">
    <?php include('../../templates/head.php'); ?>
</head>
<body>
    <?php include('../../templates/header.php'); ?>

    <div class="content">
        <h1 class="connexion-title">RÉINITIALISATION DE MOT DE PASSE</h1>

        <?php if (!empty($message)): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="reset_password.php" method="POST" class="connexion">
                <div class="form-group">
                    <label for="email">Adresse mail <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="input-field" placeholder="Votre adresse mail" required>
                </div>

                <div class="button-container">
                    <button type="submit" class="btnConnexion">Envoyer</button>
                </div>
                <p class="register-link"><a href="connexion.php">Retour à la connexion</a></p>
            </form>
        </div>
    </div>

    <?php include('../../templates/footer.php'); ?>
</body>
</html> 