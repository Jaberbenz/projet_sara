CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    types ENUM('passager', 'conducteur') NOT NULL,
    date_naissance DATE NULL,
    genre ENUM('homme', 'femme', 'autre') NULL,
    date_permis DATE NULL
);

CREATE TABLE trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT,
    depart VARCHAR(255) NOT NULL,
    arrivee VARCHAR(255) NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    prix FLOAT NOT NULL,
    places_disponibles INT NOT NULL,
    details TEXT,
    FOREIGN KEY (conducteur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trajet_id INT,
    passager_id INT,
    status ENUM('en_attente', 'acceptée', 'refusée') DEFAULT 'en_attente',
    FOREIGN KEY (trajet_id) REFERENCES trajets(id),
    FOREIGN KEY (passager_id) REFERENCES utilisateurs(id)
);
