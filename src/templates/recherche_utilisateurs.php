<?php
// On inclut le fichier de configuration avec les constantes de connexion à la base de données
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// On récupère le terme de recherche envoyé en GET (ex: "jean"), ou une chaîne vide si rien n'est envoyé
$term = $_GET['term'] ?? '';

// On établit une connexion à la base de données avec les infos de config
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Si la connexion échoue, on arrête le script
if ($conn->connect_error) exit;

// On sécurise le terme pour éviter les injections SQL et on prépare la recherche avec des %
$term = "%" . $conn->real_escape_string($term) . "%";

// On prépare la requête : on cherche les utilisateurs dont le prénom + nom contient le terme recherché
$stmt = $conn->prepare("
  SELECT id, prenom, nom 
  FROM Utilisateurs 
  WHERE CONCAT(prenom, ' ', nom) LIKE ? 
  LIMIT 10
");

// On lie la variable $term à la requête (remplace le `?`)
$stmt->bind_param("s", $term);

// On exécute la requête SQL
$stmt->execute();

// On récupère les résultats sous forme de tableau associatif
$result = $stmt->get_result();

// On initialise un tableau pour stocker les suggestions
$suggestions = [];

// Pour chaque ligne trouvée (chaque utilisateur correspondant), on ajoute son id et son nom complet
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['id'],
        'label' => $row['prenom'] . ' ' . $row['nom']
    ];
}

// On indique que la réponse est de type JSON
header('Content-Type: application/json');

// On renvoie les suggestions au format JSON (utilisé par le champ d'autocomplétion côté JS)
echo json_encode($suggestions);
