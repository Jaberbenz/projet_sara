<?php
session_start();
require_once 'includes/database.php';

$depart = $_GET['depart'] ?? null;
$arrivee = $_GET['arrivee'] ?? null;
$date = $_GET['date'] ?? null;

$results = [];


$sort = $_GET['sort'] ?? null;

// Construire la clause ORDER BY basée sur le paramètre de tri
$orderClause = "";
switch ($sort) {
    case 'prix':
        $orderClause = " ORDER BY prix ASC";
        break;
    case 'heure_depart':
        $orderClause = " ORDER BY heure_depart ASC";
        break;
    default:
        $orderClause = ""; // Pas de tri spécifique, ou ajoutez un tri par défaut
}

if ($depart && $arrivee && $date) {
    $conn = connect();  // Utilisez la fonction connect()

    $sql = "SELECT * FROM trajets WHERE depart LIKE ? AND arrivee LIKE ? AND date_depart = ?" . $orderClause;
    $stmt = $conn->prepare($sql);
    $depart = "%$depart%";
    $arrivee = "%$arrivee%";
    $stmt->bind_param("sss", $depart, $arrivee, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Résultats de votre recherche de covoiturage</h1>
        <a href="index.php">Retour à l'accueil</a>
    </header>

    <main>
    <a href="recherche.php?depart=<?= $depart ?>&arrivee=<?= $arrivee ?>&date=<?= $date ?>&sort=prix">Trier par Prix</a>
    <a href="recherche.php?depart=<?= $depart ?>&arrivee=<?= $arrivee ?>&date=<?= $date ?>&sort=heure_depart">Trier par Heure de Départ</a>

        <?php if (!empty($results)): ?>
            <ul>
                <?php foreach ($results as $trajet): ?>
                    <li><?= htmlspecialchars($trajet['depart']) ?> à <?= htmlspecialchars($trajet['arrivee']) ?> le <?= htmlspecialchars($trajet['date_depart']) ?> à <?= htmlspecialchars($trajet['heure_depart']) ?>, Prix: <?= htmlspecialchars($trajet['prix']) ?>€</li>
                    <!-- Dans recherche.php, dans votre boucle d'affichage des résultats -->
                  <a href="trajet.php?id=<?= $trajet['id'] ?>">Voir détails</a>

                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun trajet trouvé pour les critères spécifiés.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
