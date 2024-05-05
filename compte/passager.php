<?php
session_start();
require_once '../includes/database.php';  // Assurez-vous que le chemin est correct

$message = "";
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: connexion.php"); 
    exit;
}

$conn = connect();  // Utilisez la fonction connect() du fichier inclu
// Récupérer les informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT nom, telephone, email, date_naissance, genre FROM utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
    $stmt->close();
}

// Mettre à jour les informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];

    $sql = "UPDATE utilisateurs SET nom = ?, telephone = ?, email = ?, date_naissance = ?, genre = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nom, $telephone, $email, $date_naissance, $genre, $userId);
    if ($stmt->execute()) {
        $message = "Informations mises à jour avec succès.";
    } else {
        $message = "Erreur lors de la mise à jour des informations.";
    }
    $stmt->close();
}

// Récupérer les trajets réservés par l'utilisateur
$reservedTrips = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM reservations WHERE passager_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reservedTrips[] = $row;
    }
    $stmt->close();
}
echo "UserID: " . $userId;  // Vérifiez si cela affiche l'ID utilisateur correct.
// Après avoir récupéré les trajets
if (!empty($reservedTrips)) {
    echo "Trajets trouvés: " . count($reservedTrips);
} else {
    echo "Aucun trajet trouvé pour l'utilisateur avec ID: " . $userId;
}
echo "Nombre de trajets trouvés: " . $result->num_rows;  // Cela montrera combien de rangées ont été trouvées.


$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Mon Compte</h1>
    </header>

    <main>
        <p><?php echo $message; ?></p>
        <form action="passager.php" method="post">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userInfo['nom'] ?? ''); ?>" required>

            <label for="telephone">Téléphone:</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($userInfo['telephone'] ?? ''); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email'] ?? ''); ?>" required>

            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($userInfo['date_naissance'] ?? ''); ?>" required>

            <label for="genre">Genre:</label>
            <select id="genre" name="genre" required>
                <option value="homme" <?php echo (isset($userInfo['genre']) && $userInfo['genre'] === 'homme') ? 'selected' : ''; ?>>Homme</option>
                <option value="femme" <?php echo (isset($userInfo['genre']) && $userInfo['genre'] === 'femme') ? 'selected' : ''; ?>>Femme</option>
                <option value="autre" <?php echo (isset($userInfo['genre']) && $userInfo['genre'] === 'autre') ? 'selected' : ''; ?>>Autre</option>
            </select>

            <button type="submit">Mettre à jour</button>
        </form>
        <h2>Mes trajets réservés</h2>
    <?php if (!empty($reservedTrips)): ?>
        <ul>
            <?php foreach ($reservedTrips as $trip): ?>
                <li>
                    Trajet de <?= htmlspecialchars($trip['depart']) ?> à <?= htmlspecialchars($trip['arrivee']) ?> <br>
                    Date et heure de départ : <?= htmlspecialchars($trip['date_depart']) ?> à <?= htmlspecialchars($trip['heure_depart']) ?> <br>
                    Prix : <?= htmlspecialchars($trip['prix']) ?>€ <br>
                    Statut de la réservation : <?= htmlspecialchars($trip['status']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun trajet réservé pour le moment.</p>
    <?php endif; ?>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
