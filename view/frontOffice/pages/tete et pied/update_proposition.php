<?php
include '../../../../controller/propositionC.php';

$error = "";

$propoC= null;
$propoController = new propositionController();


if (
    isset($_POST["commentaire_modif"])  && $_POST["montant_propo_modif"] && $_POST["delai_estime_modif"]
) {
    if (
        !empty($_POST["commentaire_modif"])  && !empty($_POST["montant_propo_modif"]) && !empty($_POST["delai_estime_modif"]))
    
    { 

        $date_propo = new DateTime($_POST['date_propo']);
        
        $propoC = new Proposition(
            $_POST['id_propo'],
            $_POST['id_user'],
            $_POST['id_pub'],

            $_POST['commentaire_modif'],
            $_POST['montant_propo_modif'], 
            $_POST['delai_estime_modif'] . ' jours',
            $date_propo,
            $_POST['status']


        );
        //
        
        $propoController->update_propo($propoC, $_POST['id_propo']);

       header('Location: ../projet_freelancer.php');
    } else
        $error = "Missing information";
}
?>
