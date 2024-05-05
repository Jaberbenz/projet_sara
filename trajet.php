<?php
session_start();
require_once './includes/database.php';

$message = "";
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: connexion.php");
    exit;
}

$conn = connect();

if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}

// Récupérer l'ID du trajet à partir de la requête GET
$trajetId = $_GET['id'] ?? null;  // Assurez-vous que l'ID est passé en paramètre dans l'URL

if (!$trajetId) {
    $message = "Aucun ID de trajet spécifié.";
} else {
    // Préparer la requête SQL pour obtenir les détails du trajet
    $sql = "SELECT t.*, u.nom as conducteur_nom FROM trajets t JOIN utilisateurs u ON t.conducteur_id = u.id WHERE t.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation: " . $conn->error);
    }
    $stmt->bind_param("i", $trajetId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les détails
    if ($row = $result->fetch_assoc()) {
        $trajetDetails = $row;
    } else {
        $message = "Aucun détail trouvé pour ce trajet.";
    }

    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Trajet</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Détails du Trajet</h1>
    </header>
    <main>
    <?php if (isset($trajetDetails)): ?>
        <h2>Trajet de <?= htmlspecialchars($trajetDetails['depart']) ?> à <?= htmlspecialchars($trajetDetails['arrivee']) ?></h2>
        <p>Date et heure de départ : <?= htmlspecialchars($trajetDetails['date_depart']) ?> à <?= htmlspecialchars($trajetDetails['heure_depart']) ?></p>
        <p>Conducteur : <?= htmlspecialchars($trajetDetails['conducteur_nom']) ?></p>
        <p>Prix : <?= htmlspecialchars($trajetDetails['prix']) ?>€</p>
        <p>Places disponibles : <?= htmlspecialchars($trajetDetails['places_disponibles']) ?></p>
        <p>Description : <?= htmlspecialchars($trajetDetails['details']) ?></p>
        <?php if ($_SESSION['user_type'] === 'passager' && $trajetDetails['places_disponibles'] > 0): ?>
            <form action="reserve_trajet.php" method="post">
                <input type="hidden" name="trajet_id" value="<?= $trajetDetails['id'] ?>">
                <button type="submit">Réserver ce trajet</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
