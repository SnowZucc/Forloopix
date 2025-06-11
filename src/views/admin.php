<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /Forloopix/public/index.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection_error = null;
if ($conn->connect_error) {
    $connection_error = "Connexion à la base de données échouée : " . $conn->connect_error;
    // Afficher l'erreur de manière proéminente si la connexion échoue tôt
    // Cela pourrait empêcher le reste de la page de se charger, donc nous la plaçons ici.
    // Si vous voyez un écran blanc, vérifiez les logs PHP pour cette erreur.
    error_log($connection_error); // Loggue l'erreur pour le serveur
    // Ne pas utiliser die() ici pour permettre l'affichage potentiel d'erreurs dans le HTML
}

// Traitement des modifications d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $type = $_POST['type'];
        
        $password_update_sql = "";
        $bind_types = "ssssi"; // s for nom, prenom, email, type, i for user_id
        $bind_params = [&$nom, &$prenom, &$email, &$type];

        if (!empty($_POST['password'])) {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_update_sql = ", mot_de_passe = ?";
            $bind_types .= "s"; // s for hashed_password
            $bind_params[] = &$hashed_password;
        }
        $bind_params[] = &$user_id;
        
        $sql = "UPDATE Utilisateurs SET nom = ?, prenom = ?, email = ?, type = ?" . $password_update_sql . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Dynamically bind parameters
        call_user_func_array([$stmt, 'bind_param'], array_merge([$bind_types], $bind_params));
        
        if ($stmt->execute()) {
            $success_message = "Utilisateur mis à jour avec succès.";
        } else {
            $error_message = "Erreur lors de la mise à jour : " . $stmt->error;
        }
        $stmt->close();
    }
}

// Récupération des utilisateurs
$sql = "SELECT id, nom, prenom, email, type FROM Utilisateurs ORDER BY id DESC";
$result = $conn->query($sql);
$query_error = null; 
$all_users = []; // Pour stocker les utilisateurs si fetch_all fonctionne

if (!$connection_error && $result === false) { 
    $query_error = "Erreur lors de la récupération des utilisateurs : " . $conn->error;
    error_log($query_error); 
} elseif (!$connection_error && $result) {
    // Tentative avec fetch_all
    // echo "<div style='background-color: mediumpurple; color: white; padding: 10px; border: 1px solid purple;'>DEBUG: Tentative avec fetch_all(MYSQLI_ASSOC). Nombre de lignes attendues: {$result->num_rows}<pre>";
    $all_users = $result->fetch_all(MYSQLI_ASSOC);
    // var_dump($all_users);
    // echo "</pre>Erreur MySQLi après fetch_all: " . mysqli_error($conn) . "</div>";
    
    if (empty($all_users) && $result->num_rows > 0) {
        // Si fetch_all ne retourne rien mais num_rows > 0, c'est toujours très étrange
        $query_error = "fetch_all n'a retourné aucun utilisateur bien que num_rows soit > 0.";
         error_log($query_error);
    }
    $result->close(); // Fermer le résultat ici car nous avons (ou pas) toutes les données
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Administration - Gestion des utilisateurs</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-quentin.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
    <?php include('../templates/head.php'); ?>
    <style>
        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .admin-title {
            text-align: center;
            margin-bottom: 30px;
            color: #1e7a1e;
            font-size: 24px;
            font-weight: bold;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .users-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        
        .users-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .users-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .btn-edit, .btn-save, .btn-cancel {
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .btn-edit {
            background-color: #f8f9fa;
            border: 1px solid #6c757d;
            color: #212529;
        }
        
        .btn-save {
            background-color: #1e7a1e;
            border: 1px solid #1e7a1e;
            color: white;
        }
        
        .btn-cancel {
            background-color: #dc3545;
            border: 1px solid #dc3545;
            color: white;
        }
        
        .form-edit input, .form-edit select {
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        
        .user-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .type-admin {
            background-color: #dc3545;
            color: white;
        }
        
        .type-porteur {
            background-color: #ffc107;
            color: #212529;
        }
        
        .type-collaborateur {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>

<?php include('../templates/header.php'); // Décommenté ?>

<div class="content">
    <div class="admin-container">
        <h1 class="admin-title">Gestion des Utilisateurs</h1>
        
        <?php if (isset($connection_error)): ?>
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <strong>Erreur Critique:</strong> <?php echo htmlspecialchars($connection_error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($query_error)): ?>
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($query_error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Nettoyage des anciens messages de débogage
                ?>
                <?php if (!$connection_error && !empty($all_users)): ?>
                    <?php foreach($all_users as $row): ?>
                        <tr id="user-row-<?php echo $row['id']; ?>">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nom']); ?></td>
                            <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="user-type type-<?php echo strtolower($row['type']); ?>">
                                    <?php echo ucfirst($row['type']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="editUser(<?php echo $row['id']; ?>)">Modifier</button>
                            </td>
                        </tr>
                        <tr id="edit-row-<?php echo $row['id']; ?>" style="display: none;">
                            <td colspan="6">
                                <form class="form-edit" method="POST" action="admin.php"> <?php // Action vers admin.php ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 10px;">
                                        <div>
                                            <label for="nom-<?php echo $row['id']; ?>">Nom:</label>
                                            <input type="text" id="nom-<?php echo $row['id']; ?>" name="nom" value="<?php echo htmlspecialchars($row['nom']); ?>">
                                        </div>
                                        <div>
                                            <label for="prenom-<?php echo $row['id']; ?>">Prénom:</label>
                                            <input type="text" id="prenom-<?php echo $row['id']; ?>" name="prenom" value="<?php echo htmlspecialchars($row['prenom']); ?>">
                                        </div>
                                        <div>
                                            <label for="email-<?php echo $row['id']; ?>">Email:</label>
                                            <input type="email" id="email-<?php echo $row['id']; ?>" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                        </div>
                                        <div>
                                            <label for="password-<?php echo $row['id']; ?>">Nouveau mot de passe:</label>
                                            <input type="password" id="password-<?php echo $row['id']; ?>" name="password" placeholder="Laisser vide si inchangé">
                                        </div>
                                        <div>
                                            <label for="type-<?php echo $row['id']; ?>">Type:</label>
                                            <select id="type-<?php echo $row['id']; ?>" name="type">
                                                <option value="porteur" <?php echo ($row['type'] === 'porteur') ? 'selected' : ''; ?>>Porteur</option>
                                                <option value="collaborateur" <?php echo ($row['type'] === 'collaborateur') ? 'selected' : ''; ?>>Collaborateur</option>
                                                <option value="admin" <?php echo ($row['type'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <button type="button" class="btn-cancel" onclick="cancelEdit(<?php echo $row['id']; ?>)">Annuler</button>
                                        <button type="submit" class="btn-save">Enregistrer</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (isset($query_error)):
                     // S'assurer d'afficher query_error s'il a été défini plus haut
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Impossible de charger les utilisateurs : <?php echo htmlspecialchars($query_error); ?></td>
                    </tr>
                <?php elseif ($connection_error): ?>
                     <tr>
                        <td colspan="6" style="text-align: center;">Impossible de se connecter à la base de données. Vérifiez le message d'erreur ci-dessus.</td>
                    </tr>
                <?php else: // Cas où $all_users est vide et pas d'autre erreur spécifique ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun utilisateur trouvé dans la base de données.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
if ($conn) { // Fermer la connexion principale si elle est ouverte
    $conn->close();
}
include('../templates/footer.php'); // Décommenté
?>

<script>
    function editUser(userId) {
        // Masquer toutes les autres lignes d'édition ouvertes
        document.querySelectorAll('tr[id^="edit-row-"]').forEach(function(editRow) {
            if (editRow.id !== 'edit-row-' + userId) {
                editRow.style.display = 'none';
                // Réafficher la ligne d'utilisateur correspondante
                let correspondingUserId = editRow.id.replace('edit-row-', '');
                document.getElementById('user-row-' + correspondingUserId).style.display = 'table-row';
            }
        });

        document.getElementById('user-row-' + userId).style.display = 'none';
        document.getElementById('edit-row-' + userId).style.display = 'table-row';
    }
    
    function cancelEdit(userId) {
        document.getElementById('user-row-' + userId).style.display = 'table-row';
        document.getElementById('edit-row-' + userId).style.display = 'none';
    }
</script>

</body>
</html>     