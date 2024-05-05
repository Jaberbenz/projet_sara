<?php
session_start();
require_once '../includes/database.php';

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

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Récupérer les informations de l'utilisateur
        $sql = "SELECT nom, telephone, email, date_naissance, genre, date_permis FROM utilisateurs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userInfo = $result->fetch_assoc();
        $stmt->close();

        // Récupérer les trajets initiés par le conducteur
        $sql = "SELECT id, depart, arrivee, date_depart, heure_depart FROM trajets WHERE conducteur_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $trajets = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action == 'update_profile') {
            // Mise à jour des informations de l'utilisateur
            $nom = $_POST['nom'] ?? 'Nom inconnu';
            $telephone = $_POST['telephone'] ?? 'Non spécifié';
            $email = $_POST['email'] ?? 'email@exemple.com';
            $date_naissance = $_POST['date_naissance'] ?? '1900-01-01';
            $genre = $_POST['genre'] ?? 'autre';
            $date_permis = $_POST['date_permis'] ?? '1900-01-01';

            $sql = "UPDATE utilisateurs SET nom = ?, telephone = ?, email = ?, date_naissance = ?, genre = ?, date_permis = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nom, $telephone, $email, $date_naissance, $genre, $date_permis, $userId);
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour des informations: " . $stmt->error);
            }
            $message = "Informations mises à jour avec succès.";
            $stmt->close();
        } elseif ($action == 'add_trip') {
            // Ajout d'un nouveau trajet
            $depart = $_POST['depart'];
            $arrivee = $_POST['arrivee'];
            $date_depart = $_POST['date_depart'];
            $heure_depart = $_POST['heure_depart'];
            $prix = $_POST['prix'];
            $places_disponibles = $_POST['places_disponibles'];
    
            $sql = "INSERT INTO trajets (conducteur_id, depart, arrivee, date_depart, heure_depart, prix, places_disponibles) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
            }
            $stmt->bind_param("issssid", $userId, $depart, $arrivee, $date_depart, $heure_depart, $prix, $places_disponibles);
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'ajout du trajet: " . $stmt->error);
            }
            $message = "Trajet ajouté avec succès.";
            $stmt->close(); }
    }
} catch (Exception $e) {
    $message = $e->getMessage();
} finally {
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte Conducteur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Mon Compte Conducteur</h1>
    </header>

    <main>
        <p><?php echo $message; ?></p>
        <form action="conducteur.php" method="post">
        <input type="hidden" name="action" value="update_profile">
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
                <option value="homme" <?= ($userInfo['genre'] ?? '') == 'homme' ? 'selected' : ''; ?>>Homme</option>
                <option value="femme" <?= ($userInfo['genre'] ?? '') == 'femme' ? 'selected' : ''; ?>>Femme</option>
                <option value="autre" <?= ($userInfo['genre'] ?? '') == 'autre' ? 'selected' : ''; ?>>Autre</option>
            </select>

            <label for="date_permis">Date d'obtention du permis:</label>
            <input type="date" id="date_permis" name="date_permis" value="<?php echo htmlspecialchars($userInfo['date_permis'] ?? ''); ?>" required>

            <button type="submit">Mettre à jour</button>
        </form>

        <h2>Mes trajets initiés</h2>
        <?php if (!empty($trajets)): ?>
        <ul>
            <?php foreach ($trajets as $trajet): ?>
                <li><?= htmlspecialchars($trajet['depart']) ?> à <?= htmlspecialchars($trajet['arrivee']) ?>
                le <?= htmlspecialchars($trajet['date_depart']) ?> à <?= htmlspecialchars($trajet['heure_depart']) ?>,
                Prix: <?= $trajet['prix'] ?>DA
                <a href="gestion_trajet.php?id=<?= $trajet['id'] ?>">Voir détails</a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun trajet trouvé.</p>
    <?php endif; ?>

        <h2>Ajouter un nouveau trajet</h2>
        <form action="conducteur.php" method="post">
        <input type="hidden" name="action" value="add_trip">
            <!-- Champs pour ajouter un nouveau trajet -->
            <input type="text" name="depart" placeholder="Lieu de départ" required>
            <input type="text" name="arrivee" placeholder="Lieu d'arrivée" required>
            <input type="date" name="date_depart" required>
            <input type="time" name="heure_depart" required>
            <input type="number" name="prix" placeholder="Prix (€)" required>
            <input type="number" name="places_disponibles" placeholder="Places disponibles" required>
            <button type="submit">Ajouter le trajet</button>
        </form>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
