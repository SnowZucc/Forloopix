<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
         <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-meryem.css">
         <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
        <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
        <title>Annonce - Start-Hut</title>
        <?php include('../templates/head.php'); ?>
    </head>
    <body>

        <?php        
        // // Affichage des erreurs PHP
        // ini_set('display_errors', 1);
        // error_reporting(E_ALL);       
                                                                                   
        include('../templates/header.php'); 
        
        // Message flash de confirmation ou d'erreur

        if (isset($_GET['msg'])) {
            $class = '';
            $text = '';

            if ($_GET['msg'] === 'success') {
                $class = 'message-success';
                $text = 'Votre candidature a bien été envoyée.';
            } elseif ($_GET['msg'] === 'already_postulated') {
                $class = 'message-warning';
                $text = 'Vous avez déjà postulé à ce projet.';
            } elseif ($_GET['msg'] === 'saved') {
                $class = 'message-success';
                $text = 'Annonce sauvegardée avec succès.';
            } elseif ($_GET['msg'] === 'already') {
                $class = 'message-warning';
                $text = 'Annonce déjà sauvegardée.';
            }

            if ($class && $text) {
                echo "<div class='message-flash $class'>$text</div>";
            }
        }

        // Inclusion du header contenant la navigation
        require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
        
        $bdd = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);              // Création objet PDO pour connexion MySQL
        
        $id_annonce = isset($_GET['id']) ? (int)$_GET['id'] : 0;                                      // Récupère ID URL ou 0 si absent
        
        // Récupération des informations de l'annonce
        $req = $bdd->prepare('SELECT p.*, u.nom, u.prenom, d.lien FROM Projets p 
                     JOIN Utilisateurs u ON p.createur = u.id 
                     LEFT JOIN Documents d ON p.id = d.projet AND d.type = "image"
                     WHERE p.id = ?');                                                                   // Préparation de la requête SQL avec jointure. Infos projet + lien de l'image depuis documeents
        $req->execute([$id_annonce]);                                                                 // Exécute requête avec paramètre ID
        $annonce = $req->fetch(PDO::FETCH_ASSOC);                                                    // Récupère résultat en tableau associatif
        
        if (!$annonce) {                                                                              // Si aucune annonce trouvée
            echo "Annonce non trouvée";                                                              // Affiche message d'erreur
            exit;                                                                                     // Arrête l'exécution du script
        }
        ?>                                                                                      
        
        <div class="content">
        <div class="containerAnnonceFlex">
    <!-- Colonne gauche -->
    <div class="annonceGauche">
        <h1 class="titreAnnonce"><?php echo htmlspecialchars($annonce['annonce_titre']); ?></h1>
        <img src="<?php echo $annonce['lien'] ?? 'https://vection-cms-prod.s3.eu-central-1.amazonaws.com/Adobe_Stock_525614074_8ab9bd18e3.jpeg'; ?>" alt="Image du projet" class="visuelAnnonce">
        <div class="description-bloc">
            <h3>Description</h3>
            <p><?php echo htmlspecialchars($annonce['annonce_description']); ?></p>
        </div>
        <div class="details-bloc">
            <h3>Détails</h3>
            <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($annonce['annonce_categorie']); ?></p>
            <p><strong>Compétences recherchées :</strong> <?php echo htmlspecialchars($annonce['annonce_competences_recherchees']); ?></p>
            <p><strong>Nombre de collaborateurs :</strong> <?php echo htmlspecialchars($annonce['annonce_collaborateurs_souhaites']); ?></p>
        </div>

        <div class="remuneration-bloc">
    <h3>Rémunération</h3>
    <p>
        <?php 
            echo isset($annonce['annonce_remuneration']) && $annonce['annonce_remuneration'] > 0
                ? htmlspecialchars($annonce['annonce_remuneration']) . ' €' 
                : "Non renseignée"; 
        ?>
    </p>
</div>


    </div>

    <!-- Colonne droite -->
    <div class="annonceDroite">
        <div class="carteProfil">
            <img src="/Forloopix/public/assets/img/APRIL.png" alt="Photo de profil" class="profilImage">
            <h2><?= htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']); ?></h2>
            <p><span>📍</span> Pays &nbsp;&nbsp; <span>💬</span> Langues</p>
            <button class="btnContact" onclick="openChatModal(<?= $annonce['createur'] ?>)">Contactez moi</button>
        </div>
    </div>
    <div id="chatModal" style="display:none; position:fixed; top:10%; left:30%; width:40%; height:50%; background:white; border:1px solid #ccc; box-shadow: 0 0 10px #000; padding:10px; z-index:1000;border-radius:10px;text-align:left;">
    <div id="chatHeader">
    <button onclick="closeChatModal()" style="background:red; margin-bottom:10px;">Fermer</button>
    <h3>Chat</h3>
    </div>
    <div id="chatMessages" style="height:60%; overflow-y:auto; border:1px solid #eee; margin-bottom:10px; padding:5px;">
        Chargement...
    </div>
    <form id="chatForm">
        <input type="hidden" name="utilisateur_id" id="chatUserId">
        <textarea name="message" id="chatMessageInput" rows="3" style="width:80%; border-radius:10px;margin-top:20px" placeholder=" Votre message..." required></textarea>
        <button type="submit" style="color:black; background:#2ecc71">Envoyer</button>
    </form>
    </div>
    <script>
    let chatRefreshInterval = null;

    function openChatModal(utilisateurId) {
        document.getElementById('chatModal').style.display = 'block';
        document.getElementById('chatUserId').value = utilisateurId;
        loadChatMessages(utilisateurId);

        // Active le rafraîchissement automatique toutes les 5 secondes
        chatRefreshInterval = setInterval(() => {
            loadChatMessages(utilisateurId);
        }, 5000);
    }

    function closeChatModal() {
        document.getElementById('chatModal').style.display = 'none';

        // Stoppe le rafraîchissement automatique
        if (chatRefreshInterval) {
            clearInterval(chatRefreshInterval);
            chatRefreshInterval = null;
        }
    }

    function loadChatMessages(utilisateurId) {
    fetch('projet/chat_api.php?utilisateur_id=' + utilisateurId)
        .then(response => response.json())
        .then(data => {
            const chatBox = document.getElementById('chatMessages');
            chatBox.innerHTML = '';

            const currentUserId = <?= json_encode($_SESSION['user_id']) ?>;

            if (data.length === 0) {
                chatBox.innerHTML = '<p>Aucun message pour l’instant.</p>';
            } else {
                data.forEach(msg => {
                    const isMine = (parseInt(msg.id_expediteur) === parseInt(currentUserId));

                    const msgElem = document.createElement('div');
                    msgElem.style.marginBottom = '10px';
                    msgElem.style.padding = '8px 12px';
                    msgElem.style.borderRadius = '12px';
                    msgElem.style.maxWidth = '70%';
                    msgElem.style.wordWrap = 'break-word';

                    if (isMine) {
                        // Style pour l’expéditeur (toi)
                        msgElem.style.backgroundColor = '#2ecc71';
                        msgElem.style.color = 'white';
                        msgElem.style.alignSelf = 'flex-end';
                        msgElem.innerHTML = `
                            <strong style="color:#d4f4df;">${msg.prenom} ${msg.nom}</strong>
                            <small style="color:#d4f4df;">${msg.date}</small>
                            <div>${msg.contenu}</div>
                        `;
                    } else {
                        // Style pour l’autre
                        msgElem.style.backgroundColor = '#ecf0f1';
                        msgElem.style.color = '#2c3e50';
                        msgElem.style.alignSelf = 'flex-start';
                        msgElem.innerHTML = `
                            <strong>${msg.prenom} ${msg.nom}</strong>
                            <small>${msg.date}</small>
                            <div>${msg.contenu}</div>
                        `;
                    }

                    chatBox.appendChild(msgElem);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}


    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const utilisateurId = document.getElementById('chatUserId').value;
        const message = document.getElementById('chatMessageInput').value;

        fetch('projet/chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `utilisateur_id=${encodeURIComponent(utilisateurId)}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById('chatMessageInput').value = '';
                loadChatMessages(utilisateurId);
            }
        });
    });
    // DRAGGABLE FUNCTIONALITY
dragElement(document.getElementById("chatModal"));

function dragElement(elmnt) {
    const header = document.getElementById("chatHeader");
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    if (header) {
        // if present, the header is where you move the DIV from
        header.onmousedown = dragMouseDown;
    } else {
        // otherwise, move the DIV from anywhere inside the DIV
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        // stop moving when mouse button is released
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

    </script>

</div>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $user_type = $_SESSION['user_type'] ?? null;
            ?>


                <?php if ($user_type === 'porteur'): ?>
               
                <?php endif; ?>
                <?php
                $from_hutbox = isset($_GET['from']) && $_GET['from'] === 'hutbox';
                ?>

                <?php if ($user_type === 'collaborateur'): ?>
                <div class="actionsAnnonce">
                    <form method="POST" action="postuler_annonce.php">
                        <input type="hidden" name="id_projet" value="<?= $annonce['id'] ?>">
                        <?php if ($from_hutbox): ?>
                            <input type="hidden" name="from" value="hutbox">
                        <?php endif; ?>
                        <button type="submit" class="btnAction">Postuler</button>
                    </form>
                    <?php if ($from_hutbox): ?>

                        <!-- Bouton Supprimer des favoris -->
                        <form method="POST" action="/Forloopix/src/views/supprimer_sauvegarde.php" style="display: inline;">
                            <input type="hidden" name="id_projet" value="<?= $annonce['id'] ?>">
                            <button  type="submit" class="btnSupprimer">Supprimer</button>
                        </form>

                    <?php endif; ?>


                    <?php if (!$from_hutbox): ?>
                    <form method="POST" action="sauvegarder_annonces.php" style="display: inline;">
                        <input type="hidden" name="id_projet" value="<?= $annonce['id'] ?>">
                        <button type="submit" class="btnAction btnSecondaire">Sauvegarder</button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php
                $from_page = $_GET['from'] ?? 'annonces';  // valeur par défaut : annonces

                // Par défaut, retour = annonces.php
                $return_link = '/Forloopix/src/views/annonces.php';

                // Si on vient d'index, on retourne vers index.php
                if ($from_page === 'index') {
                    $return_link = '/Forloopix/public/index.php';
                }

                // Si collaborateur et depuis hutbox → espace collaborateur
                if ($user_type === 'collaborateur' && $from_page === 'hutbox') {
                    $return_link = '/Forloopix/src/views/projet/espace-collaborateur.php?view=Hutbox';
                }
                ?>
                <div style="margin: 20px;">
                    <a href="<?= $return_link ?>" class="btnAction btnSecondaire">← Retour</a>
                </div>

        


                <!-- Boutons en bas -->
             



        </div>
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
            <div class="message-flash message-success">Votre candidature a bien été envoyée.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'already_postulated'): ?>
            <div class="message-flash message-warning">Vous avez déjà postulé à ce projet.</div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && in_array($_GET['msg'], ['saved', 'already'])): ?>
            <div class="message-flash <?= $_GET['msg'] === 'saved' ? 'message-success' : 'message-warning' ?>">
                <?= $_GET['msg'] === 'saved' ? 'Annonce sauvegardée avec succès.' : 'Annonce déjà sauvegardée.' ?>
            </div>
        <?php endif; ?>


        <script>
        // Cible tous les messages affichés
        ['msg-envoye', 'msg-postule', 'msg-sauvegarde'].forEach(function(id) {
            const msgElement = document.getElementById(id);
            if (msgElement) {
                setTimeout(() => {
                    msgElement.style.transition = "opacity 0.5s";
                    msgElement.style.opacity = 0;
                    setTimeout(() => msgElement.remove(), 500); // suppression après fondu
                }, 4000); // disparition après 4 secondes
            }
        });
        </script>
        
        <script>
        // Sauvegarde la position de scroll avant de soumettre le formulaire
        const formsToTrack = [
            "form[action='sauvegarder_annonces.php']",
            "form[action='postuler_annonce.php']"
        ];

        formsToTrack.forEach(selector => {
            const form = document.querySelector(selector);
            if (form) {
                form.addEventListener("submit", function () {
                    sessionStorage.setItem("scrollTop", window.scrollY);
                });
            }
        });

        // Après rechargement, restaure la position
        window.addEventListener("load", function () {
            const scrollTop = sessionStorage.getItem("scrollTop");
            if (scrollTop !== null) {
            window.scrollTo({ top: parseInt(scrollTop), behavior: "instant" });
            sessionStorage.removeItem("scrollTop");
            }
        });
        </script>





        <?php include('../templates/footer.php'); ?> 

        <script>
            const msgEnvoye = document.getElementById("msg-envoye") || document.getElementById("msg-postule");
            if (msgEnvoye) {
                setTimeout(() => {
                    msgEnvoye.style.transition = "opacity 0.5s";
                    msgEnvoye.style.opacity = 0;
                    setTimeout(() => msgEnvoye.remove(), 500);
                }, 3000);
            }
        </script>

  
    </body>
</html>