<?php
require_once "config.php";
$config = new Config();
$conn = $config->getConnexion();

if($conn) {
    echo "Connexion réussie !";
} else {
    echo "Erreur de connexion.";
}
?>
