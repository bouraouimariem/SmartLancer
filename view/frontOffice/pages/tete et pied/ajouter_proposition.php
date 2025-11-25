<?php

include '../../../../controller/propositionC.php';

$error = "";
$prop = null;
$propositionC = new propositionController();

if (
    isset($_POST["commentaire"]) && isset($_POST["montant_propo"]) && isset($_POST["delai_estime"])
) {
    if (!empty($_POST["commentaire"]) && !empty($_POST["montant_propo"]) && !empty($_POST["delai_estime"])) {
        
        session_start();
if (!isset($_SESSION['id_user'])) {
    echo "❌ Vous devez être connecté pour faire une proposition.";
    exit;
}
$id_user = $_SESSION['id_user']; // ✅ bon nom ici


        $id_pub = $_POST['id_pub']; 
        $date_propo = new DateTime();
        $status = 'en cours';
        $delai_estime = $_POST['delai_estime'] . ' jours';
        
        $prop = new Propositions(
            null,
            $id_user,
            $id_pub,
            $_POST['commentaire'],
            $_POST['montant_propo'],
            $delai_estime,
            $date_propo,
            $status
        );

        $propositionC->create_propo($prop);
        header('Location: ../projet_freelancer.php');
        exit;
    }
}
?>
