<?php
error_reporting(E_ERROR);
ini_set('display_errors', 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recrutement - Start-Hut</title>
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-guillaume.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <?php include('../../templates/head.php'); ?>
</head>
<?php include('../../templates/header.php'); ?>
<body>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
session_start();

$porteur_id = $_SESSION['user_id'] ?? null;
$equipe = [];
$candidatures = [];

if ($porteur_id) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Récupérer les membres de l'équipe
    $stmt_equipe = $conn->prepare("
        SELECT u.id AS id_utilisateur, p.id AS id_projet, p.annonce_titre, u.nom, u.prenom, u.email,
               d.lien AS photo_profil
        FROM ParticipantsProjets pp
        JOIN Utilisateurs u ON pp.id_participant = u.id
        JOIN Projets p ON pp.id_projet = p.id
        LEFT JOIN Documents d ON u.id = d.proprietaire AND d.type = 'image' 
        WHERE p.createur = ?
    ");
    $stmt_equipe->bind_param("i", $porteur_id);
    $stmt_equipe->execute();
    $result_equipe = $stmt_equipe->get_result();
    $equipe = $result_equipe->fetch_all(MYSQLI_ASSOC);
    $stmt_equipe->close();

    // Récupérer les candidatures
    $stmt = $conn->prepare("
        SELECT 
            u.nom, u.prenom, u.email,
            p.annonce_titre,
            c.utilisateur_id, c.projet_id,
            c.statut, c.date_postulation,
            d.lien AS photo_profil
        FROM Candidatures c
        JOIN Utilisateurs u ON c.utilisateur_id = u.id
        JOIN Projets p ON c.projet_id = p.id
        LEFT JOIN Documents d ON u.id = d.proprietaire AND d.type = 'image'
        WHERE p.createur = ?
        ORDER BY c.date_postulation DESC
    ");
    $stmt->bind_param("i", $porteur_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidatures = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $conn->close();
}

// Regrouper les membres de l'équipe par projet
$equipe_par_projet = [];
foreach ($equipe as $membre) {
    $projet_id = $membre['id_projet'];

    if (!isset($equipe_par_projet[$projet_id])) {
        $equipe_par_projet[$projet_id] = [
            'titre' => $membre['annonce_titre'],
            'membres' => []
        ];
    }

    $equipe_par_projet[$projet_id]['membres'][] = $membre;
}

// Regrouper uniquement les candidatures non acceptées/refusées
$candidatures_par_projet = [];
foreach ($candidatures as $candidat) {
    if ($candidat['statut'] === 'refuse' || $candidat['statut'] === 'accepte') {
        continue;
    }

    $projet_id = $candidat['projet_id'];

    if (!isset($candidatures_par_projet[$projet_id])) {
        $candidatures_par_projet[$projet_id] = [
            'titre' => $candidat['annonce_titre'],
            'candidats' => []
        ];
    }

    $candidatures_par_projet[$projet_id]['candidats'][] = $candidat;
}
?>

<nav class="sub-navbar">
    <ul>
        <li><a href="espace-projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'espace-projet.php' ? 'active' : '' ?>">Mes annonces</a></li>
        <li><a href="recrutement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'recrutement.php' ? 'active' : '' ?>">Recrutement</a></li>
        <li><a href="projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'projet.php' ? 'active' : '' ?>">Projet</a></li>
        <li><a href="ressource/ressources.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ressources.php' ? 'active' : '' ?>">Ressources</a></li>
    </ul>
</nav>

<main class="recrutement-container">
    <!-- Section Mon Équipe -->
    <section class="mon-equipe">
        <h2>Mon équipe</h2>
        <?php if (empty($equipe_par_projet)) : ?>
            <p>Aucun membre pour le moment.</p>
        <?php else : ?>
            <?php foreach ($equipe_par_projet as $projet) : ?>
                <div class="projet-bloc">
                    <h3 style="margin-top: 20px;"><?= htmlspecialchars($projet['titre']) ?></h3>
                    <div class="equipe-liste">
                        <?php foreach ($projet['membres'] as $membre) : ?>
                            <div class="equipe-card">
                                <div class="equipe-avatar">
                                    <img src="<?= $membre['photo_profil'] ?? '/Forloopix/public/assets/img/APRIL.png'; ?>" alt="Avatar">
                                </div>
                                <div class="equipe-info">
                                    <h4><?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?></h4>
                                    <p><?= htmlspecialchars($membre['email']) ?></p>
                                </div>
                                <div class="recrutement-actions">
                                    <button class="btn-chat" onclick="openChatModal(<?= $membre['id_utilisateur'] ?>)">Chat</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Section Recrutement en cours -->
    <section class="recrutement-en-cours">
        <h2>Recrutement en cours</h2>

        <?php if (empty($candidatures_par_projet)) : ?>
            <p>Aucun collaborateur n’a encore postulé à vos annonces.</p>
        <?php else : ?>
            <?php foreach ($candidatures_par_projet as $projet) : ?>
                <div class="projet-bloc">
                    <h3 style="margin-top: 20px;"><?= htmlspecialchars($projet['titre']) ?></h3>
                    <div class="recrutement-liste">
                        <?php foreach ($projet['candidats'] as $candidat) : ?>
                            <div class="recrutement-card">
                                <div class="recrutement-avatar">
                                    <img src="<?= $candidat['photo_profil'] ?? '/Forloopix/public/assets/img/APRIL.png' ?>" alt="Avatar">
                                </div>
                                <div class="recrutement-info">
                                    <h4><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></h4>
                                    <p><strong>Email :</strong> <?= htmlspecialchars($candidat['email']) ?></p>
                                    <p><strong>Statut :</strong> <?= htmlspecialchars($candidat['statut']) ?></p>
                                    <p><strong>Date :</strong> <?= htmlspecialchars($candidat['date_postulation']) ?></p>
                                </div>
                                <div class="recrutement-actions">
                                    <form action="traiter_candidature.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="utilisateur_id" value="<?= htmlspecialchars($candidat['utilisateur_id']) ?>">
                                        <input type="hidden" name="projet_id" value="<?= htmlspecialchars($candidat['projet_id']) ?>">
                                        <input type="hidden" name="action" value="accepter">
                                        <button type="submit" class="btn-accepter">Accepter</button>
                                    </form>
                                    <form action="traiter_candidature.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="utilisateur_id" value="<?= htmlspecialchars($candidat['utilisateur_id']) ?>">
                                        <input type="hidden" name="projet_id" value="<?= htmlspecialchars($candidat['projet_id']) ?>">
                                        <input type="hidden" name="action" value="refuser">
                                        <button type="submit" class="btn-refuser">Refuser</button>
                                    </form>
                                    <button class="btn-chat" onclick="openChatModal(<?= $membre['id_utilisateur'] ?>)">Chat</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div id="chatModal" style="display:none; position:fixed; top:10%; left:30%; width:40%; height:50%; background:white; border:1px solid #ccc; box-shadow: 0 0 10px #000; padding:10px; z-index:1000;border-radius:10px;">
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
    fetch('chat_api.php?utilisateur_id=' + utilisateurId)
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

        fetch('chat_api.php', {
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


    </section>
</main>

<?php include('../../templates/footer.php'); ?>

</body>
</html>
