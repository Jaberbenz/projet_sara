<?php
// Définition des constantes pour les paramètres de connexion à la base de données
define("DB_HOST", "db");  // Utiliser 'db' si vous utilisez Docker et que 'db' est le nom de votre service de base de données dans docker-compose.yml
define("DB_USER", "root");
define("DB_PASSWORD", "example");  // Assurez-vous d'utiliser le mot de passe défini dans votre docker-compose.yml pour 'MYSQL_ROOT_PASSWORD'
define("DB_DATABASE", "covoiturage");  // Le nom de la base de données défini dans votre docker-compose.yml pour 'MYSQL_DATABASE'

// Fonction pour établir une connexion à la base de données
function connect() {
    // Utilisation des constantes dans la fonction mysqli_connect
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

    // Vérification de la connexion
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    return $conn;
}
?>
