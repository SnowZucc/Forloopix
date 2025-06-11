<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>FAQ Start-Hut</title>
  <meta name="description" content="FAQ – Tout ce qu'il faut savoir sur Start-Hut">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-khaleb.css">
  <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
  
  <?php include('../templates/head.php'); ?>
</head>
<body>
  <?php include('../templates/header.php'); ?> 
  <div class="content"> 
  <div class="contentFAQ">
    <div class="search-container">
    <input type="text" id="searchInput" placeholder="Rechercher une question..." onkeyup="afficherSuggestions()">
    <div id="suggestions" class="suggestion-box"></div>
    </div>
    <h1>Foire aux questions (FAQ)</h1>
    <p>Bienvenue dans notre section FAQ ! Vous trouverez ici les réponses aux questions les plus fréquentes concernant Start-Hut.</p>
  <!-- Section Inscription et Gestion du Compte -->
  <div class="faq-section">
    <h2>Inscription et Gestion du Compte</h2>
    <div class="faq-item">
    <div class="faq-bloc">
      <div class="faq-question">Comment créer un compte sur Start-Hut ?</div>
      <div class="faq-toggle"></div>
    </div>
      <div class="faq-answer">
        <p>Expliquez le processus d’inscription et les informations requises.</p>
      </div>
    </div>

    <div class="faq-item">
    <div class="faq-bloc">
      <div class="faq-question">Comment puis-je modifier ou supprimer mon compte ?</div>
      <div class="faq-toggle"></div>
    </div>
      <div class="faq-answer">
        <p>Fournissez des instructions sur la gestion des paramètres personnels.</p>
      </div>
    </div>

    <div class="faq-item">
    <div class="faq-bloc">
      <div class="faq-question">Que faire en cas d'oubli de mot de passe ?</div>
      <div class="faq-toggle"></div>
    </div>
      <div class="faq-answer">
        <p>Décrivez la procédure de réinitialisation du mot de passe.</p>
      </div>
    </div>
  </div>

  <!-- Section Sécurité et Confidentialité -->
  <div class="faq-section">
    <h2>Sécurité et Confidentialité</h2>
    <div class="faq-item">
    <div class="faq-bloc">
      <div class="faq-question">Comment protégez-vous mes données personnelles ?</div>
      <div class="faq-toggle"></div>
    </div>
      <div class="faq-answer">
        <p>Nous utilisons des protocoles de sécurité avancés pour garantir la protection de vos informations.</p>
      </div>
    </div>

    <div class="faq-item">
    <div class="faq-bloc">
      <div class="faq-question">Quels sont mes droits concernant mes données sur Start-Hut ?</div>
      <div class="faq-toggle"></div>
    </div>
      <div class="faq-answer">
        <p>Vous pouvez demander l'accès, la modification ou la suppression de vos données à tout moment.</p>
      </div>
    </div>
  </div>

  <!-- Section Paiements et Facturation -->
  <div class="faq-section">
      <h2>Paiements et Facturation</h2>
      <div class="faq-item">
      <div class="faq-bloc">
        <div class="faq-question">Quels modes de paiement acceptez-vous ?</div>
        <div class="faq-toggle"></div>
      </div>
        <div class="faq-answer">
          <p>Nous acceptons les paiements par carte bancaire, PayPal et virement bancaire.</p>
        </div>
      </div>

      <div class="faq-item">
      <div class="faq-bloc">
        <div class="faq-question">Comment annuler mon abonnement ?</div>
        <div class="faq-toggle"></div>
      </div>
        <div class="faq-answer">
          <p>Vous pouvez annuler votre abonnement via votre espace personnel ou en contactant notre support.</p>
        </div>
      </div>
  </div>

  <!-- Section Gestion des Services -->
  <div class="faq-section">
      <h2>Gestion des Services</h2>
      <div class="faq-item">
      <div class="faq-bloc">
        <div class="faq-question">Comment créer un projet ?</div>
        <div class="faq-toggle"></div>
      </div>
      <div class="faq-answer">
        <p>Vous pouvez créer un projet en accédant à votre tableau de bord et en suivant les étapes indiquées.</p>
      </div>
      </div>

      <div class="faq-item">
      <div class="faq-bloc">
        <div class="faq-question">Comment rejoindre un projet ?</div>
        <div class="faq-toggle"></div>
      </div>
        <div class="faq-answer">
          <p>Recherchez un projet dans la section dédiée et postulez en fonction de vos compétences.</p>
        </div>
      </div>

      <div class="faq-item">
      <div class="faq-bloc">
        <div class="faq-question">Existe-t-il une communauté ou un forum pour échanger ?</div>
        <div class="faq-toggle"></div>
      </div>
        <div class="faq-answer">
          <p>Oui, vous pouvez rejoindre notre forum de discussion pour échanger avec d'autres utilisateurs.</p>
        </div>
      </div>
  </div>
   
  <!-- Section Contactez-nous stylisée dans une box -->

    <div class="contact-section">
      <div class="contact-container">
        <div class="contact-text">
          <h2>Besoin d'aide ?</h2>
          <p>Nous sommes là pour vous aider.</p>
          
          <a class="contact-button" href="/Forloopix/src/views/contact.php">Contactez-nous </a>
        
        
        </div>
        <div class="contact-image">
          <img src="/Forloopix/public/assets/img/image-aide.png" alt="image d'aide" >
        </div>
      </div>
    </div>
    </div>
  </div> 

  </div> <!-- Fin de la div content -->
  <script>
    // Sélectionner toutes les flèches
    const toggles = document.querySelectorAll('.faq-item');

    toggles.forEach(toggle => {
      toggle.addEventListener('click', () => {
        const item = toggle.closest('.faq-item');
        item.classList.toggle('active');
      });
    });

  </script> 
  <script>
  function afficherSuggestions() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const items = document.querySelectorAll(".faq-item");
  const suggestionsContainer = document.getElementById("suggestions");

  suggestionsContainer.innerHTML = "";

  if (input.trim() === "") return;

  const suggestionsDéjàVues = new Set();

  items.forEach((item) => {
    const question = item.querySelector(".faq-question").textContent.toLowerCase();
    const answer = item.querySelector(".faq-answer").textContent.toLowerCase();
    const texteComplet = question + " " + answer;

    if ((question.includes(input) || answer.includes(input)) && !suggestionsDéjàVues.has(texteComplet)) {
      const clone = item.cloneNode(true);
      clone.classList.add("suggestion-item");

      // Masquer la réponse par défaut
      const clonedAnswer = clone.querySelector(".faq-answer");
      if (clonedAnswer) clonedAnswer.style.display = "none";

      // 👉 Ajouter le toggle .active dynamiquement
      clone.addEventListener("click", () => {
        clone.classList.toggle("active");

        // Afficher / masquer la réponse
        if (clonedAnswer) {
          const isVisible = clonedAnswer.style.display === "block";
          clonedAnswer.style.display = isVisible ? "none" : "block";
        }
      });

      suggestionsContainer.appendChild(clone);
      suggestionsDéjàVues.add(texteComplet);
    }
  });

  if (suggestionsContainer.innerHTML === "") {
    suggestionsContainer.innerHTML = "<div class='suggestion-item'>Aucun résultat trouvé.</div>";
  }
}
  </script>
  <script>
    // Fermer la suggestion-box si on clique à l'extérieur
    document.addEventListener('click', function(event) {
      const searchInput = document.getElementById("searchInput");
      const suggestions = document.getElementById("suggestions");

      // Si le clic est en dehors de la boîte et du champ
      if (!searchInput.contains(event.target) && !suggestions.contains(event.target)) {
        suggestions.innerHTML = "";
      }
    });
  </script>



  <?php include('../templates/footer.php'); ?>

</body>
</html>