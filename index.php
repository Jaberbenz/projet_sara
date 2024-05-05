<?php
session_start();

// Gestion de la déconnexion
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php"); // Redirection vers la page d'accueil après la déconnexion
    exit;
}

// Fichier inclus pour les fonctions de base de données et potentiellement la gestion de la connexion
require_once 'includes/database.php'; 


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Covoiturage</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js"></script>
</head>
<body>
    <header>
        <h1>Covoiturage Algérie</h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Lien conditionnel basé sur le type d'utilisateur stocké dans la session -->
                <?php if ($_SESSION['user_type'] == 'conducteur'): ?>
                    <a href="./compte/conducteur.php">Mon Compte</a>
                <?php elseif ($_SESSION['user_type'] == 'passager'): ?>
                    <a href="./compte/passager.php">Mon Compte</a>
                <?php endif; ?>
                <form action="index.php" method="post">
                    <button type="submit" name="logout">Déconnexion</button>
                </form>
            <?php else: ?>
                <a href="inscription.php">Inscription</a>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Trouvez votre trajet idéal</h2>
        <form action="recherche.php" method="get">
            <input type="text" name="depart" placeholder="Lieu de départ">
            <input type="text" name="arrivee" placeholder="Lieu d'arrivée">
            <input type="date" name="date">
            <button type="submit">Rechercher</button>
        </form>
    </main>

    <footer>
        <p>Contactez-nous à <a href="mailto:contact@covoiturage.dz">contact@covoiturage.dz</a></p>
    </footer>
</body>
</html>
