<?php
session_start();
require 'includes/database.php'; // Assurez-vous que ce fichier gère la connexion à votre base de données

$conn = connect(); // Utilisation de la fonction de connexion définie dans database.php

$nom = $telephone = $email = $mot_de_passe = "";
$types = "conducteur"; // Par défaut, le type est 'passager'
$date_naissance = $genre = $date_permis = null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecte et traitement des données du formulaire
    $nom = $_POST['nom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $types = $_POST['types'];
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];
    $date_permis = $types === 'conducteur' ? $_POST['date_permis'] : null;

    if (empty($nom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Veuillez remplir correctement tous les champs requis.";
    } else {
        $sql = "INSERT INTO utilisateurs (nom, telephone, email, mot_de_passe, types, date_naissance, genre, date_permis)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }
        $stmt->bind_param("ssssssss", $nom, $telephone, $email, $mot_de_passe, $types, $date_naissance, $genre, $date_permis);

        if ($stmt->execute()) {
            $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
        } else {
            $message = "Erreur: " . $stmt->error;
        }

        $stmt->close();
    }
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Covoiturage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Inscription au Covoiturage</h1>
    </header>

    <main>
        <p><?php echo $message; ?></p>
        <form action="inscription.php" method="post">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="telephone">Téléphone:</label>
            <input type="text" id="telephone" name="telephone" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe:</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <label for="types">Vous êtes:</label>
            <select id="types" name="types" required>
                <option value="passager">Passager</option>
                <option value="conducteur">Conducteur</option>
            </select>

            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" required>

            <label for="genre">Genre:</label>
            <select id="genre" name="genre">
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
                <option value="autre">Autre</option>
            </select>

            <label for="date_permis" class="conducteurField">Date d'obtention du permis:</label>
            <input type="date" id="date_permis" name="date_permis" class="conducteurField">

            <button type="submit">S'inscrire</button>
        </form>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>

    <script>
        // Script pour afficher/occulter les champs spécifiques aux conducteurs
        document.getElementById('types').addEventListener('change', function () {
            var display = this.value === 'conducteur' ? 'block' : 'none';
            document.querySelectorAll('.conducteurField').forEach(function (element) {
                element.style.display = display;
            });
        });
    </script>
</body>
</html>
