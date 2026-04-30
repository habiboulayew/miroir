<?php
$mysqli = new mysqli("localhost", "root", "", "drhmouvement", 3306);

if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

echo "Connexion réussie !";
$mysqli->close();
?>