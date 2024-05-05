<?php
session_start();
require_once 'includes/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];  // Mot de passe en clair soumis par l'utilisateur

    $conn = connect();  // Utilisation de la fonction de connexion de database.php

    if ($conn) {
        $sql = "SELECT id, email, types, mot_de_passe FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['mot_de_passe'])) {
                // Le mot de passe est correct, créer une session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['types'];
                header("Location: index.php");  // Rediriger vers la page d'accueil
                exit;
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Aucun utilisateur trouvé avec cet email.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Covoiturage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Connexion</h1>
    </header>

    <main>
        <p><?php echo $message; ?></p>
        <form action="connexion.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Connexion</button>
        </form>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
