<?php
// On démarre la session pour accéder à l’utilisateur connecté
session_start();

// On inclut le fichier de configuration pour se connecter à la base
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// On récupère l’ID de l’expéditeur (l’utilisateur actuellement connecté)
$expediteur = $_SESSION['user_id'] ?? null;

// On récupère le contenu du message envoyé via POST
$contenu = $_POST['contenu'] ?? null;

// Deux manières possibles de recevoir le destinataire :
// - soit via son ID (champ caché dans le formulaire)
// - soit via son nom/prénom (si l’autocomplétion ne renvoie pas l’ID)
$destinataire_id = $_POST['destinataire_id'] ?? null;
$destinataire_nom = $_POST['destinataire_nom'] ?? null;

// On vérifie qu'on a un expéditeur et un message à envoyer
if ($expediteur && $contenu) {

    // Connexion à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Si l'ID du destinataire n'est pas défini mais qu'on a son nom/prénom
    if (!$destinataire_id && $destinataire_nom) {
        // On cherche son ID dans la base en comparant nom + prénom
        $stmt = $conn->prepare("
            SELECT id 
            FROM Utilisateurs 
            WHERE CONCAT(prenom, ' ', nom) LIKE CONCAT('%', ?, '%') 
            LIMIT 1
        ");
        $stmt->bind_param("s", $destinataire_nom);
        $stmt->execute();

        // On stocke l'ID trouvé dans la variable $destinataire_id
        $stmt->bind_result($destinataire_id);
        $stmt->fetch();
        $stmt->close();
    }

    // Si on a bien trouvé un destinataire (par ID direct ou via nom/prénom)
    if ($destinataire_id) {
        // On insère le message dans la table Messages
        $stmt = $conn->prepare("
            INSERT INTO Messages (id_expediteur, id_destinataire, contenu, date) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("iis", $expediteur, $destinataire_id, $contenu);
        $stmt->execute();
        $stmt->close();
    }

    // On ferme la connexion à la base
    $conn->close();
}

// À la fin, on redirige l’utilisateur vers la page précédente (rechargement auto de la messagerie)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
