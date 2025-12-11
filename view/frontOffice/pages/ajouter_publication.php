<?php
require_once '../../../controller/publicationC.php';


$error = "";
$pub = null;

// ID de l'utilisateur (temporaire)
$id_user = 1;

$publicationC = new publicationController();

// Vérifier que tous les champs sont envoyés
if (
    isset($_POST["nom_pub"]) &&
    isset($_POST["description"]) &&
    isset($_POST["budget"]) &&
    isset($_POST["delai"]) &&
    isset($_POST["categories"])
) {
    // Vérifier que tous les champs NE SONT PAS vides
    if (
        !empty($_POST["nom_pub"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["budget"]) &&
        !empty($_POST["delai"]) &&
        !empty($_POST["categories"])
    ) {

$date_pub = new DateTime();  // génère la date/heure actuelle
        $status = 'en cours';

        // Gestion des catégories
        $categories = is_array($_POST["categories"])
            ? implode(", ", $_POST["categories"])
            : $_POST["categories"];

        // Créer l'objet
        $pub = new Publications(
            null,
            $id_user,
            $_POST['nom_pub'],
            $categories,
            $_POST['description'],
            $_POST['budget'],
            $_POST['delai'],
            $date_pub,
            $status
        );

        // Insérer dans la base
        $publicationC->create_pub($pub);

        // Retour à la page principale
       header('Location: ../clientpub.php');
exit;

    }
}
?>
