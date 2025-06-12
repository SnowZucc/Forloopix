<?php
// Définir les infos de connexion
$servername = "mysql-appg1d.alwaysdata.net";
$username = "appg1d_groupee";
$password = "Dev\$G11E";
$dbname = "appg1d_projetcommun";

// Créer la connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier si la connexion est réussie
if (mysqli_connect_errno()) {
  die("Erreur de connexion : " . mysqli_connect_error());
}
echo "Connexion réussie à la base de données !";

// Fermer la connexion
mysqli_close($conn);
?>