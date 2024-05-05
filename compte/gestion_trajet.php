<?php
session_start();
require_once '../includes/database.php';  // Assurez-vous que le chemin est correct

$message = "";
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: connexion.php"); 
    exit;
}

$conn = connect(); 

// Gérer les actions de mise à jour de statut des réservations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        $reservationId = $_POST['reservationId'];
        $sql = "UPDATE reservations SET status = 'acceptée' WHERE id = ?";
    } elseif (isset($_POST['reject'])) {
        $reservationId = $_POST['reservationId'];
        $sql = "UPDATE reservations SET status = 'refusée' WHERE id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservationId);
    if ($stmt->execute()) {
        echo "<p>La réservation a été mise à jour.</p>";
    } else {
        echo "<p>Erreur lors de la mise à jour de la réservation.</p>";
    }
    $stmt->close();
}

// Récupérer les trajets et les réservations associées
$sql = "SELECT t.id, t.depart, t.arrivee, t.date_depart, t.heure_depart, r.id AS reservation_id, r.passager_id, r.status
        FROM trajets t
        LEFT JOIN reservations r ON t.id = r.trajet_id
        WHERE t.conducteur_id = ?
        ORDER BY t.date_depart ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$trajets = [];
while ($row = $result->fetch_assoc()) {
    $trajets[$row['id']]['details'] = $row;
    $trajets[$row['id']]['reservations'][] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Trajets</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Gestion des Trajets</h1>
    </header>

    <main>
        <?php foreach ($trajets as $trajet): ?>
            <h3>Trajet de <?= htmlspecialchars($trajet['details']['depart']) ?> à <?= htmlspecialchars($trajet['details']['arrivee']) ?> le <?= htmlspecialchars($trajet['details']['date_depart']) ?> à <?= htmlspecialchars($trajet['details']['heure_depart']) ?></h3>
            <?php foreach ($trajet['reservations'] as $reservation): ?>
                <p>Réservation ID: <?= $reservation['reservation_id'] ?>, Statut: <?= $reservation['status'] ?></p>
                <form action="gestion_trajet.php" method="post">
                    <input type="hidden" name="reservationId" value="<?= $reservation['reservation_id'] ?>">
                    <?php if ($reservation['status'] == 'en attente'): ?>
                        <button type="submit" name="accept">Accepter</button>
                        <button type="submit" name="reject">Refuser</button>
                    <?php endif; ?>
                </form>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
