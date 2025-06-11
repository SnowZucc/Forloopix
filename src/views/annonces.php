<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Annonces - Start-Hut</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
      <link rel="stylesheet" href="/Forloopix/public/assets/css/styles-louis.css">
      <?php include('../templates/head.php'); ?>
  </head>
  <body>
    <?php include('../templates/header.php'); ?> <!-- Header -->

    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Forloopix/config/config.php');
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $q = $_GET['q'] ?? '';
    $categorie = $_GET['categorie'] ?? '';
    $competence = $_GET['competence'] ?? '';
    $min_remuneration = $_GET['min_remuneration'] ?? '';
    $max_remuneration = $_GET['max_remuneration'] ?? '';

    $conditions = [];
    $params = [];

    if (!empty($q)) {
        $conditions[] = "(p.annonce_titre LIKE ? OR p.annonce_description LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    if (!empty($categorie)) {
        $conditions[] = "p.annonce_categorie = ?";
        $params[] = $categorie;
    }

    if (!empty($competence)) {
        $conditions[] = "p.annonce_competences_recherchees = ?";
        $params[] = $competence;
    }

    if (is_numeric($min_remuneration)) {
        $conditions[] = "p.annonce_remuneration >= ?";
        $params[] = (int)$min_remuneration;
    }

    if (is_numeric($max_remuneration)) {
        $conditions[] = "p.annonce_remuneration <= ?";
        $params[] = (int)$max_remuneration;
    }

    $sql = "SELECT p.*, d.lien 
            FROM Projets p 
            LEFT JOIN Documents d ON p.id = d.projet AND d.type = 'image'";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($sql);

    if ($params) {
        $types = '';
        foreach ($params as $param) {
            $types .= is_int($param) ? 'i' : 's';
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="content">
      <form method="GET" action="annonces.php">
        <div class="filtres-container">
          <input type="text" name="q" class="dropdown" placeholder="Rechercher" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

          <select name="categorie" class="dropdown">
            <option value="">Catégorie</option>
            <option value="technologies" <?= ($_GET['categorie'] ?? '') === 'technologies' ? 'selected' : '' ?>>Technologies</option>
            <option value="education" <?= ($_GET['categorie'] ?? '') === 'education' ? 'selected' : '' ?>>Éducation</option>
            <option value="business" <?= ($_GET['categorie'] ?? '') === 'business' ? 'selected' : '' ?>>Business</option>
          </select>

          <select name="competence" class="dropdown">
            <option value="">Compétence</option>
            <option value="developpeur" <?= ($_GET['competence'] ?? '') === 'developpeur' ? 'selected' : '' ?>>Développeur</option>
            <option value="designer" <?= ($_GET['competence'] ?? '') === 'designer' ? 'selected' : '' ?>>Designer</option>
            <option value="marketing" <?= ($_GET['competence'] ?? '') === 'marketing' ? 'selected' : '' ?>>Marketing</option>
            <option value="communication" <?= ($_GET['competence'] ?? '') === 'communication' ? 'selected' : '' ?>>Communication</option>
          </select>

          <!-- ✅ Champs numériques pour filtrer la rémunération -->
          <input type="number" name="min_remuneration" class="dropdown" placeholder="Min €" value="<?= htmlspecialchars($_GET['min_remuneration'] ?? '') ?>">
          <input type="number" name="max_remuneration" class="dropdown" placeholder="Max €" value="<?= htmlspecialchars($_GET['max_remuneration'] ?? '') ?>">

          <button type="submit" class="btn-filtrer">Filtrer</button>
        </div>
      </form>

      <div class="grid">
        <?php
        while($row = $result->fetch_assoc()) {
            echo "<a href='annonce.php?id=" . $row["id"] . "' class='nav-links'>";
            echo "<figure>";
            echo "<img src='" . ($row["lien"] ?? 'https://vection-cms-prod.s3.eu-central-1.amazonaws.com/Adobe_Stock_525614074_8ab9bd18e3.jpeg') . "'>";
            echo "<figcaption>";
            echo "<h3>" . htmlspecialchars($row["annonce_titre"]) . "</h3>";
            echo "<p>" . htmlspecialchars($row["annonce_description"]) . "</p>";
            echo "</figcaption>";
            echo "</figure>";
            echo "</a>";
        }
        $conn->close();
        ?>
      </div>
    </div>

    <?php include('../templates/footer.php'); ?>
  </body>
</html>