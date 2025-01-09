<?php

// Informations de connexion à la base de données
$host = 'localhost:3307';
$user = 'root';
$password = '';
$database = 'dailytrip_0';

try {
    // Connexion au serveur MySQL sans sélectionner de base de données
    $conn = new PDO("mysql:host=$host", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la base de données si elle n'existe pas
    $sql = "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET = 'utf8mb4'";
    $conn->exec($sql);
    echo "Base de données '$database' créée avec succès.\n";
    
    // Se connecter à la base de données créée
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("USE $database");

    // Définir le moteur InnoDB pour la création des tables
    $engine = 'ENGINE = InnoDB';

    // Création des tables
    $tables = [
        // TODO: Ajoutez vos requêtes SQL de création de tables ici
        "CREATE TABLE `admin` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `email` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) NOT NULL
        );",
        "CREATE TABLE `images` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `link` VARCHAR(255) NOT NULL,
            `alt` TEXT NOT NULL
        );",
        "CREATE TABLE `gallery` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT
        );",
        "CREATE TABLE `localisation` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `start` VARCHAR(255) NOT NULL,
            `finish` VARCHAR(255) NOT NULL,
            `distance` DECIMAL(8,3) NOT NULL,
            `duration` TIME NOT NULL
        );",
        "CREATE TABLE `category` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NOT NULL
        );",
        "CREATE TABLE `gallery_images` (
            `gallery_id` INTEGER NOT NULL,
            `images_id` INTEGER NOT NULL,
            FOREIGN KEY (`gallery_id`) REFERENCES gallery(`id`),
            FOREIGN KEY (`images_id`) REFERENCES images(`id`)
        );",
        "CREATE TABLE `poi` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `point` VARCHAR(255) NOT NULL,
            `gallery_id` INTEGER NOT NULL,
            `localisation_id` INTEGER NOT NULL,
            FOREIGN KEY (gallery_id) REFERENCES gallery(id),
            FOREIGN KEY (localisation_id) REFERENCES localisation(id)
        );",
        "CREATE TABLE `trips` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `ref` VARCHAR(255) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL, 
            `cover` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `status` BOOLEAN NOT NULL,
            `category_id` INTEGER NOT NULL,
            `gallery_id` INTEGER NOT NULL,
            `localisation_id` INTEGER NOT NULL,
            FOREIGN KEY (category_id) REFERENCES category(id),
            FOREIGN KEY (gallery_id) REFERENCES gallery(id),
            FOREIGN KEY (localisation_id) REFERENCES localisation(id)
        );",
        "CREATE TABLE `rating` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `note` INTEGER NOT NULL,
            `ip_address` VARCHAR(255) NOT NULL,
            `trip_id` INTEGER NOT NULL,
            FOREIGN KEY (trip_id) REFERENCES trips(id)
        );",
        "CREATE TABLE `review` (
            `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
            `fullname` VARCHAR(255) NOT NULL,
            `content` TEXT NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `trip_id` INTEGER NOT NULL,
            FOREIGN KEY (trip_id) REFERENCES trips(id)
        );"
    ];


    // Exécution de la création des tables
    foreach ($tables as $tableSql) {
        try {
            $conn->exec($tableSql);
            echo "Table créée avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la création de la table : " . $e->getMessage() . "\n";
        }
    }

    // Ajout des clés étrangères
    $constraints = [
        // TODO: Ajoutez vos requêtes SQL de contraintes ici
        "ALTER TABLE poi ADD CONSTRAINT FK_gallery FOREIGN KEY (gallery_id) REFERENCES gallery(id);",
        "ALTER TABLE poi ADD CONSTRAINT FK_localisation FOREIGN KEY (localisation_id) REFERENCES localisation(id);",
        "ALTER TABLE gallery_images ADD CONSTRAINT FK_gallery FOREIGN KEY (gallery_id) REFERENCES gallery(id);",
        "ALTER TABLE gallery_images ADD CONSTRAINT FK_images FOREIGN KEY (images_id) REFERENCES images(id);",
        "ALTER TABLE trips ADD CONSTRAINT FK_category FOREIGN KEY (category_id) REFERENCES category(id);",
        "ALTER TABLE trips ADD CONSTRAINT FK_gallery FOREIGN KEY (gallery_id) REFERENCES gallery(id);",
        "ALTER TABLE trips ADD CONSTRAINT FK_localisation FOREIGN KEY (localisation_id) REFERENCES localisation(id);",
        "ALTER TABLE rating ADD CONSTRAINT FK_trips FOREIGN KEY (trip_id) REFERENCES trips(id);",
        "ALTER TABLE review ADD CONSTRAINT FK_trips FOREIGN KEY (trip_id) REFERENCES trips(id);",
    ];

    // Exécution des contraintes de clés étrangères
    foreach ($constraints as $constraintSql) {
        try {
            $conn->exec($constraintSql);
            echo "Contrainte de clé étrangère ajoutée avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout de la contrainte : " . $e->getMessage() . "\n";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit;
} finally {
    // Fermer la connexion
    $conn = null;
}
