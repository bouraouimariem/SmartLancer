<?php
include '../../../../controller/publicationC.php';

$error = "";

$pubC= null;
$pubController = new publicationController();


if (
    isset($_POST["nom_pub_modif"])  && $_POST["description_modif"] && $_POST["budget_modif"] && $_POST["delai_modif"]
) {
    if (
        !empty($_POST["nom_pub_modif"])  && !empty($_POST["description_modif"]) && !empty($_POST["budget_modif"]) && !empty($_POST["delai_modif"]))
    
    { 
        $existingPublication = $pubController->getPublicationById($_POST['id_pub']);
        if (!$existingPublication) {
            die("Publication not found");
        }
        $date_pub = new DateTime($_POST['date_pub']);
        
        $pubC = new Publications(
            $_POST['id_pub'],
            $_POST['id_user'],
            $_POST['nom_pub_modif'],
            $_POST['categorie'], // Ensure this is an integer
            $_POST['description_modif'],
            $_POST['budget_modif'],
            $_POST['delai_modif'],
            $date_pub,
            $_POST['status']

        );
        //
        
        $pubController->updatepub($pubC, $_POST['id_pub']);

       header('Location: ../projet_client.php');
    } else
        $error = "Missing information";
}
?>
