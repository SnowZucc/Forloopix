<?php
// On démarre la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On inclut le fichier de configuration contenant les infos de connexion à la base de données
// require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');

// On récupère l'identifiant de l'utilisateur connecté
$user_id = $_SESSION['user_id'] ?? null;

// Si l'utilisateur n'est pas connecté, on arrête ici
if (!$user_id) return;

// La connexion $conn est supposée être déjà ouverte par la page parente (tableau.php)
// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ---------------------
// Récupération des contacts avec qui j'ai déjà discuté
// ---------------------
$stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.nom, u.prenom
    FROM Utilisateurs u
    JOIN Messages m ON (u.id = m.id_expediteur OR u.id = m.id_destinataire)
    WHERE (m.id_expediteur = ? OR m.id_destinataire = ?) AND u.id != ?
");
// On injecte les valeurs dans la requête
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
// On récupère tous les contacts dans un tableau associatif
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------
// Récupération des messages généraux (sans destinataire)
// ---------------------
$messages_generaux = [];
$stmt = $conn->prepare("
    SELECT m.contenu, m.date, m.id_expediteur, u.prenom, u.nom
    FROM Messages m
    JOIN Utilisateurs u ON m.id_expediteur = u.id
    WHERE m.id_destinataire IS NULL
    ORDER BY m.date DESC
    LIMIT 50
");
$stmt->execute();
$messages_generaux = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------
// Récupération des messages si un contact est sélectionné
// ---------------------
$contact_id = $_GET['contact'] ?? null;
$messages = [];

// Si on a sélectionné le mode "général", on garde les messages déjà récupérés
$mode_general = isset($_GET['mode']) && $_GET['mode'] === 'general';

if ($contact_id && !$mode_general) {
    $stmt = $conn->prepare("
        SELECT contenu, date, id_expediteur
        FROM Messages
        WHERE (id_expediteur = ? AND id_destinataire = ?)
           OR (id_expediteur = ? AND id_destinataire = ?)
        ORDER BY date ASC
    ");
    $stmt->bind_param("iiii", $user_id, $contact_id, $contact_id, $user_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// On ferme la connexion à la base
// $conn->close();
?>
<!-- On lie la feuille de style CSS externe pour la messagerie -->
<link rel="stylesheet" href="assets/css/styles-meryem.css">

<!-- Librairie d'icônes (utilisée pour les bulles de messages) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Bouton 💬 en bas à droite pour ouvrir la messagerie -->
<a id="open-message-modal" class="message-floating-btn">💬</a>
<!-- Fenêtre modale cachée par défaut -->
<div id="messagerie-modal" style="display: none;">
<p style="text-align: center; font-size: 18px; margin: 10px 0;">Start-Chat</p>
  <!-- Bouton pour afficher/masquer le formulaire d'envoi -->
  <div id="new-message-btn">➕ nouveau message</div>

  <!-- Formulaire d'envoi en AJAX -->
  <div id="new-message-form">
    <form id="form-nouveau-message">
      <!-- Champ d'autocomplétion pour rechercher un utilisateur -->
      <input type="text" id="autocomplete-destinataire" placeholder="Nom ou prénom..." required>
      <!-- Champ caché pour stocker l'ID réel du destinataire -->
      <input type="hidden" name="destinataire_id" id="destinataire-id">

      <!-- Zone pour écrire le message -->
      <textarea name="contenu" placeholder="Votre message..." required></textarea>

      <!-- Bouton pour envoyer -->
      <button type="submit">Envoyer</button>
    </form>

    <!-- Message de confirmation après envoi -->
    <div id="confirmation-message" style="display:none; color:green; font-size:13px;">✅ Message envoyé</div>
  </div>
  
  <!-- Onglet de messagerie générale -->
  <div class="contact-card <?= $mode_general ? 'active-contact' : '' ?>" onclick="window.location.href='?mode=general'">
    <i class="fa fa-globe"></i>
    <div class="contact-name">Messagerie générale</div>
  </div>
  
  <!-- Liste des contacts -->
  <?php foreach ($contacts as $contact): ?>
    <?php
      // Si c'est le contact actuellement sélectionné, on applique une classe spéciale
      $isActive = ($contact_id == $contact['id'] && !$mode_general) ? 'active-contact' : '';
    ?>
    <div class="contact-card <?= $isActive ?>" onclick="window.location.href='?contact=<?= $contact['id'] ?>'">
      <i class="fa fa-comments"></i>
      <div class="contact-name"><?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?></div>
    </div>
  <?php endforeach; ?>
  
  <!-- Affichage des messages généraux si le mode général est activé -->
  <?php if ($mode_general): ?>
    <hr>
    <div class="message-thread">
      <?php if (count($messages_generaux) > 0): ?>
        <?php foreach ($messages_generaux as $msg): ?>
          <div class="message-general">
            <strong><?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?></strong> 
            <small class="message-date"><?= htmlspecialchars($msg['date']) ?></small>
            <div class="message-content"><?= nl2br(htmlspecialchars($msg['contenu'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; color: #888;">Aucun message dans la messagerie générale.</p>
      <?php endif; ?>
    </div>

    <!-- Formulaire pour envoyer un message général -->
    <form action="/Forloopix/src/templates/envoyer_message_general.php" method="POST">
      <textarea name="contenu" required style="width:100%; height:60px;" placeholder="Écrivez un message visible par tous..."></textarea><br>
      <button type="submit" style="width:100%;">Envoyer à tous</button>
    </form>
  
  <!-- Sinon, si un contact est sélectionné -->
  <?php elseif ($contact_id): ?>
    <hr>
    <div class="message-thread">
      <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
          <!-- Affichage des messages alignés à gauche ou droite selon l'expéditeur -->
          <div class="message-prive <?= $msg['id_expediteur'] == $user_id ? 'from-me' : 'from-them' ?>">

            <small><?= htmlspecialchars($msg['date']) ?></small><br>
            <?= nl2br(htmlspecialchars($msg['contenu'])) ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; color: #888;">Aucune conversation pour le moment.</p>
      <?php endif; ?>
    </div>

    <!-- Formulaire classique pour envoyer un message à ce contact -->
    <form action="/Forloopix/src/templates/envoyer_message.php" method="POST">
      <input type="hidden" name="destinataire_id" value="<?= htmlspecialchars($contact_id) ?>">
      <textarea name="contenu" required style="width:100%; height:60px;"></textarea><br>
      <button type="submit" style="width:100%;">Envoyer</button>
    </form>
  <?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // On récupère les éléments importants de la page
  const bouton = document.getElementById('open-message-modal');
  const modal = document.getElementById('messagerie-modal');
  const toggleForm = document.getElementById('new-message-btn');
  const formBloc = document.getElementById('new-message-form');
  const form = document.getElementById('form-nouveau-message');
  const confirmation = document.getElementById('confirmation-message');

  // Quand on clique sur 💬 → ouvrir ou fermer la messagerie
  bouton.addEventListener('click', () => {
    modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
  });

  // Afficher ou cacher le formulaire d'envoi
  toggleForm.addEventListener('click', () => {
    formBloc.style.display = formBloc.style.display === 'block' ? 'none' : 'block';
  });

  // Quand on envoie un message via AJAX
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // On prépare les données à envoyer
    const formData = new FormData();
    formData.append("destinataire_id", document.getElementById("destinataire-id").value);
    formData.append("contenu", form.elements["contenu"].value);

    // Envoi vers le fichier PHP
    fetch('/Forloopix/src/templates/envoyer_message.php', {
      method: 'POST',
      body: formData
    }).then(response => {
      if (response.ok) {
        // Message envoyé avec succès
        confirmation.style.display = 'block';
        form.reset();
        setTimeout(() => {
          confirmation.style.display = 'none';
        }, 2000);
      } else {
        alert("❌ Erreur lors de l'envoi.");
      }
    }).catch(error => {
      console.error(error);
      alert("❌ Une erreur s'est produite.");
    });
  });

  // Si un contact est sélectionné ou mode général → afficher automatiquement la messagerie
  const url = new URL(window.location.href);
  const contact = url.searchParams.get("contact");
  const mode = url.searchParams.get("mode");
  if (contact || mode === 'general') {
    modal.style.display = 'block';
  }

  // AUTOCOMPLÉTION (recherche des utilisateurs)
  const input = document.getElementById('autocomplete-destinataire');
  const hiddenId = document.getElementById('destinataire-id');
  let timeout;

  input.addEventListener('input', function () {
    const query = input.value;
    clearTimeout(timeout);

    // Attente de 300ms après frappe pour éviter les requêtes inutiles
    timeout = setTimeout(() => {
      fetch(`/Forloopix/src/templates/recherche_utilisateurs.php?term=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          const datalistId = 'suggestions-destinataires';
          let datalist = document.getElementById(datalistId);

          // Si le datalist n'existe pas encore, on le crée
          if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = datalistId;
            document.body.appendChild(datalist);
            input.setAttribute('list', datalistId);
          }

          // On remplit la liste avec les résultats
          datalist.innerHTML = '';
          data.forEach(user => {
            const option = document.createElement('option');
            option.value = user.label;
            option.dataset.id = user.id;
            datalist.appendChild(option);
          });
        });
    }, 300);
  });

  // Quand l'utilisateur choisit un nom dans la liste, on met son ID dans le champ caché
  input.addEventListener('change', function () {
    const options = document.querySelectorAll(`#suggestions-destinataires option`);
    const match = Array.from(options).find(opt => opt.value === input.value);
    hiddenId.value = match ? match.dataset.id : '';
  });
});
</script>
