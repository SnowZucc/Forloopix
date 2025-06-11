<?php
// ressources.php
include 'data.php'; // Assurez-vous d'avoir un fichier data.php qui contient les ressources
$type = $_GET['type'] ?? 'guides'; // Détermine le type de contenu à afficher (par défaut 'guides')
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Ressources utiles pour vos projets startup">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-fatma.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">

    <title>Start-Hut - Ressources</title>
    <?php include('../../../templates/head.php'); ?>
</head>
<body>
    <?php include('../../../templates/header.php'); ?>

    <!-- Barre de navigation secondaire -->
    <nav class="sub-navbar">
    <ul>
    <li><a href="../espace-projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'espace-projet.php' ? 'active' : '' ?>">Mes annonces</a></li>
    <li><a href="../recrutement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'recrutement.php' ? 'active' : '' ?>">Recrutement</a></li>
    <li><a href="../projet.php" class="<?= basename($_SERVER['PHP_SELF']) == 'projet.php' ? 'active' : '' ?>">Projet</a></li>
    <li><a href="../ressource/ressources.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ressources.php' ? 'active' : '' ?>">Ressources</a></li>
    </ul>
    </nav>

    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
                <li><a href="?type=guides">Guides & Articles</a></li>
                <li><a href="?type=templates">Templates & Documents</a></li>
                <li><a href="?type=outils">Outils Recommandés</a></li>
            </ul>
        </aside>

        <!-- Zone principale -->
        <section class="content-area">
            <!-- Affichage dynamique des cartes en fonction du type sélectionné -->
            <?php if ($type == 'guides'): ?>
                <!-- Section Guides & Articles -->
                <div class="cards-container">
                    <div class="card">
                        
                        <h3>Créer son business plan</h3>
                        <p>Accédez à un guide complet pour créer votre business plan et financer votre projet.</p>
                        <a href="https://www.legalplace.fr/guides/creer-business-plan-complet/" class="btn-details" target="_blank">Voir le guide</a>
                    </div>

                    <div class="card">
                        
                        <h3>Les bases du marketing digital</h3>
                        <p>Découvrez les principes du marketing digital pour booster votre startup.</p>
                        <a href="https://leadmedia.fr/comprendre-les-bases-du-marketing-digital/" class="btn-details" target="_blank">Voir le guide</a>
                    </div>

                    <!-- Ajoutez d'autres cartes ici -->
                </div>

            <?php elseif ($type == 'templates'): ?>
                <!-- Section Templates & Documents -->
                <div class="cards-container">
                    <div class="card">
                       
                        <h3>Template Business Plan</h3>
                        <p>Téléchargez un modèle de business plan pour commencer à rédiger le vôtre.</p>
                        <a href="https://www.bcdtravel.com/?mtm_campaign=adwords&mtm_kwd=adwords-business%20plan%20template%20free&mtm_source=google&mtm_medium=sem&mtm_content=adwords-ad-744784137279&mtm_group=adgroup-181441187167&mtm_placement=adwords-network-type-g&utm_campaign=adwords&utm_source=google&utm_medium=sem&utm_term=adwords-business%20plan%20template%20free&utm_content=adwords-ad-744784137279&gclid=EAIaIQobChMIkpibyfGgjQMVGIRoCR3U5jT5EAAYASAAEgLM-fD_BwE&gad_source=1&gad_campaignid=22415818071&gbraid=0AAAAAomKTQTxtDpcLkWF25_1wf1VBdpVy" class="btn-details" target="_blank">Télécharger le modèle</a>
                    </div>

                    <div class="card">
                       
                        <h3>Tableau prévisionnel financier </h3>
                        <p>Utilisez ce modèle Excel pour préparer vos projections financières.</p>
                        <a href="https://www.emasphere.com/fr/solution/fonctionnalites?utm_medium=paid_search&utm_source=google&utm_term=tableau+de+bord+financier&utm_campaign=%5BG%5D+%5BSEARCH%5D+%5BFR-FR%5D+Acquisition+%28SQL%29&hsa_acc=7251903503&hsa_cam=14542648928&hsa_grp=127619493820&hsa_ad=544031464506&hsa_src=g&hsa_tgt=kwd-296794953813&hsa_kw=tableau+de+bord+financier&hsa_mt=b&hsa_net=adwords&hsa_ver=3&gad_source=1&gad_campaignid=14542648928&gbraid=0AAAAADOO3dGatlAZRTeUX2inJuHLI-RgB&gclid=EAIaIQobChMIoPnszfGgjQMVsVJBAh1lrzAGEAAYASAAEgK7D_D_BwE" class="btn-details" target="_blank">Télécharger le template</a>
                    </div>

                    <!-- Ajoutez d'autres cartes ici -->
                </div>

            <?php elseif ($type == 'outils'): ?>
                <!-- Section Outils Recommandés -->
                <div class="cards-container">
                    <div class="card">
                        <img src="../../../../public/assets/img/mond.png" alt="Outil de gestion de projet" class="card-image">
                        <h3>Outil de gestion de projet</h3>
                        <p>Un excellent outil pour suivre l'avancement de vos projets et travailler en équipe.</p>
                        <a href="https://monday.com/ap/fr/project-management?cq_src=google_ads&cq_cmp=1335460307&cq_term=outil%20de%20gestion%20de%20projet&cq_plac=&cq_net=g&cq_plt=gp&utm_medium=cpc&utm_source=adwordslocals&utm_campaign=fr-fr-prm-workos-project-project_management-h-search-desktop-core-aw&utm_keyword=outil%20de%20gestion%20de%20projet&utm_match_type=e&cluster=&subcluster=&ati=&utm_adgroup=project%20management%20tool&utm_banner=633696779435&gad_source=1&gad_campaignid=1335460307&gbraid=0AAAAADeiQJvuNCG0Mb7zvJqkdNnmoksln&gclid=EAIaIQobChMIyeXCweigjQMV5AYGAB0e7zSyEAAYASAAEgJ-_vD_BwE" class="btn-details" target="_blank">Découvrir l'outil</a>
                    </div>

                    <div class="card">
                    <img src="../../../../public/assets/img/agi.png" alt="Outil de gestion financière" class="card-image">
                        <h3>Outil de gestion financière</h3>
                        <p>Un outil pour gérer facilement vos finances et vos budgets.</p>
                        <a href="https://agicap.com/fr/lp/logiciel-tresorerie/?utm_term=suivi%20finance_p&utm_campaign=16924581977&utm_source=adwords&utm_medium=ppc&utm_content=139211984321&hsa_acc=1885282937&hsa_cam=16924581977&hsa_grp=139211984321&hsa_ad=593205777249&hsa_src=g&hsa_tgt=kwd-1651220179904&hsa_kw=suivi%20finance&hsa_mt=p&hsa_net=adwords&hsa_ver=3&gad_source=1&gad_campaignid=16924581977&gbraid=0AAAAAB0nsuGb4HKlVQJokO8eJLM0VYu-A&gclid=EAIaIQobChMIlpTI6eigjQMVUVdBAh2EpBTFEAAYASAAEgI8YvD_BwE" class="btn-details" target="_blank">Découvrir l'outil</a>
                    </div>

                    <!-- Ajoutez d'autres cartes ici -->
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php include('../../../templates/footer.php'); ?>
</body>
</html>
