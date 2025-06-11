<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-meryem.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Contact - Start-Hut</title>
    <?php include('../templates/head.php'); ?>
  </head>
  <body>
    <?php include('../templates/header.php'); ?>

    <div class="content">
    
      <section class="contact-page">
        <div class="map-contact">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.67578667655!2d2.323153590220823!3d48.84532259740809!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e671ce3fa0c01f%3A0x9bd1ac22c478d56e!2sIsep!5e0!3m2!1sfr!2sfr!4v1746372554003!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="100%" height="350" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy"></iframe>
        </div>


      

       
      
        <div class="contact-container">
          <!-- Formulaire -->
          <div class="form-section">
            <h2>Nous contacter</h2>
            <form action="contact.php" method="POST" class="soumettre">
              <input type="text" name="prenom" placeholder="Prénom" required>
              <input type="text" name="nom" placeholder="Nom" required>
              <input type="email" name="email" placeholder="Email" required>
              <input type="text" name="sujet" placeholder="Objet" required>
              <textarea name="message" placeholder="Message" rows="6" required></textarea>
              <button type="submit">Soumettre</button>
            </form>
          </div>

          <!-- Coordonnées START-HUT -->
          <div class="info-section">
            <h3>Vous avez des questions ?</h3>

            <p><strong>Général :</strong><br>
              <i class="fas fa-phone"></i> 
              <a href="tel:+33123456789">+33 1 23 45 67 89</a><br>

              <i class="fas fa-envelope"></i> 
              <a href="mailto:contact@start-hut.fr">contact@start-hut.fr</a>
            </p>

            <p><strong>Adresse :</strong><br>
              <p>
                START-HUT<br>
                28 Rue Notre Dame des Champs <br>
                75006 Paris, France
        </p>
            </p>
          </div>

        </div>
      </section>
     
    </div>

    <?php include('../templates/footer.php'); ?>
    <?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];

    $sql = "INSERT INTO Contacts (nom, prenom, email, sujet, message) 
            VALUES ('$nom', '$prenom', '$email', '$sujet', '$message')";


if ($conn->query($sql) === TRUE) {
    // Ne rien afficher
} else {
    // Ne rien afficher non plus
}

}

$conn->close();
?>

 

  </body>
</html>
