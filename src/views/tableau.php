<?php
error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="L'outil le plus simple pour transformer vos idées en startups.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/styles-meryem.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Start-Hut</title>
    <?php include('../src/templates/head.php'); ?>
</head>

<body>
<?php include('../src/templates/header.php'); ?>    

<div class="content">

    <!-- Hero Section -->
    <section class="landing">
        <div class="landing-text">
            <h1>START-HUT</h1>
            <p>L'outil le plus simple <br> pour <span class="highlight">transformer</span> vos idées en startups.</p>
            <div class="boutons-collaborateur">
            <?php
            if (session_status() === PHP_SESSION_NONE) session_start(); 
            if (!isset($_SESSION['user_id'])) : ?>
                <a href="../src/views/annonces.php" class="btn-collab">Trouver un projet</a>
                <a href="../src/views/user/connexion.php" class="btn-projet">Me connecter pour publier</a>
            <?php else : ?>
                <a href="../src/views/projet/annonce/posterannonce.php" class="btn-collab">Publier un projet</a>
                <a href="../src/views/annonces.php" class="btn-projet">Trouver un projet</a>
            <?php endif; ?>
            </div>
        </div>
        <div class="landing-image">
            <img src="https://img.freepik.com/free-vector/business-team-discussing-ideas-startup_74855-4380.jpg?w=1380&t=st=1714579429~exp=1714580029~hmac=64a3984348efb08d9be17b07ed4e31ba3495af40fc0e6f2de1e62cf247741b5c" alt="Illustration de startups">
        </div>
    </section>

    <!-- Section Collaborateur -->
    <section class="section-collaborateur">
        <div class="conteneur-collaborateur">
            <div class="image-collaborateur">
                <img src="/Forloopix/public/assets/img/pourquoinouschoisir.png" alt="Illustration collaboration">
            </div>
            <div class="texte-collaborateur">
                <h2>Rejoignez l’aventure Start-Hut</h2>
                <p class="soustitre">Vous avez des compétences à partager ?</p>
                <p class="description">
                    Mettez votre talent au service de projets innovants.<br>
                    👩‍💻 Développeurs, designers, marketeurs, ingénieurs...<br>
                    Participez à la création de startups ambitieuses en rejoignant des porteurs de projets motivés.
                </p>
                <ul class="liste-arguments">
                    <li>🌱 Contribuez à des projets concrets dès aujourd’hui</li>
                    <li>🌍 Développez votre réseau et vos compétences</li>
                    <li>💼 Gagnez en visibilité auprès de recruteurs et investisseurs</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Récupération projets populaires -->
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
    $annonces_populaires = [];
    $image_defaut = "/Forloopix/public/assets/img/populaire1.png";

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8");

        $sql = "SELECT p.id, p.annonce_titre, p.annonce_description, d.lien 
                FROM Projets p
                LEFT JOIN Documents d ON p.id = d.projet AND d.type = 'image'
                WHERE p.annonce_etat = 'ouvert'
                ORDER BY RAND()
                LIMIT 5";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $annonces_populaires[] = $row;
            }
        }
    } catch (Exception $e) {
        echo "<p style='color:red; text-align:center;'>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>";
    }
    ?>

    <!-- Section populaire -->
    <section class="popular-section">
        <div class="container">
            <div class="textepop">
                <h2>Projet<br><span class="highlight">populaires</span></h2>
            </div>

            <div class="cards-wrapper">
                <?php if (empty($annonces_populaires)) : ?>
                    <p style="text-align:center; color:gray;">Aucun projet populaire pour le moment.</p>
                <?php else : ?>
                    <?php foreach ($annonces_populaires as $annonce): ?>
                        <div class="project-card">
                            <div class="icon-container">
                                <img src="<?= htmlspecialchars($annonce['lien'] ?: $image_defaut) ?>" alt="Icône projet" width="45" height="45">
                            </div>
                            <h3><?= htmlspecialchars($annonce['annonce_titre']) ?></h3>
                            <p><?= htmlspecialchars($annonce['annonce_description']) ?></p>
                            <a href="/Forloopix/src/views/annonce.php?id=<?= $annonce['id'] ?>&from=index" class="btn-details">Voir détails</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Pourquoi nous choisir -->
    <section class="pourquoi-section">
        <div class="conteneur-pourquoinouschoisir">
            <div class="imagepourquoinouschoisir">
                <img src="https://img.freepik.com/free-vector/business-team-brainstorm-idea-lightbulb-from-jigsaw-working-team-collaboration-enterprise-cooperation-colleagues-mutual-assistance-concept-pinkish-coral-bluevector-isolated-illustration_335657-1651.jpg?w=1380&t=st=1714579570~exp=1714580170~hmac=f57de609bdb0c839b777737ee9be8437e66a57705325e23927d6a38e111dbfcf" alt="Collaboration d'équipe">
            </div>
            <div class="textepourquoinouschoisir">
                <h2>Lancez vous, c'est facile.</h2>
                <div class="liste-choix">
                    <figure>
                        <img src="https://img.icons8.com/ios/50/27ae60/conference-call.png" alt="Talents">
                        <figcaption>
                            <h3>Trouvez les bons talents</h3>
                            <p>Accédez à une communauté de profils motivés et compétents pour construire votre équipe idéale.</p>
                        </figcaption>
                    </figure>
                    <figure>
                        <img src="https://img.icons8.com/ios/50/27ae60/idea.png" alt="Vision">
                        <figcaption>
                            <h3>Définissez votre vision</h3>
                            <p>Donnez à votre projet une direction précise pour attirer les meilleurs profils.</p>
                        </figcaption>
                    </figure>
                    <figure>
                        <img src="https://img.icons8.com/ios/50/27ae60/handshake.png" alt="Collaboration">
                        <figcaption>
                            <h3>Bâtissez des collaborations solides</h3>
                            <p>Travaillez avec des personnes de confiance pour faire avancer votre projet dans les meilleures conditions.</p>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

</div>

<?php include('../src/templates/footer.php'); ?>    
</body>
</html>
