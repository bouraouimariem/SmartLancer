<?php
include '../../../../controller/propositionC.php';
include '../../../../controller/publicationC.php';
include '../../../../controller/notificationC.php';

session_start();

/* ============================================
    Vérification connexion Freelancer
============================================ */
if (!isset($_SESSION['id_user'])) {
    echo "❌ Vous devez être connecté pour faire une proposition.";
    exit;
}

$id_user_freelancer = $_SESSION['id_user']; 


/* ============================================
    Vérification des champs envoyés
============================================ */
if (
    isset($_POST["commentaire"]) &&
    isset($_POST["montant_propo"]) &&
    isset($_POST["delai_estime"]) &&
    isset($_POST["id_pub"])
) {
    if (
        !empty($_POST["commentaire"]) &&
        !empty($_POST["montant_propo"]) &&
        !empty($_POST["delai_estime"])
    ) {

        $id_pub = $_POST['id_pub'];
        $commentaire = $_POST['commentaire'];
        $montant = $_POST['montant_propo'];
        $delai_estime = $_POST['delai_estime'] . " jours";
        $date = new DateTime();
        $status = "en cours";

        /* ============================================
            Création Objet Proposition
        ============================================ */
        $prop = new Propositions(
            null,
            $id_user_freelancer,
            $id_pub,
            $commentaire,
            $montant,
            $delai_estime,
            $date,
            $status
        );

        $propositionC = new propositionController();
        $propositionC->create_propo($prop);

        
        /* ============================================
            Récupération du CLIENT propriétaire du projet
        ============================================ */
        $publicationC = new publicationController();
        $publication = $publicationC->getPublicationById($id_pub);

        if ($publication) {
            $id_client = $publication['id_user'];  // DESTINATAIRE

            /* ============================================
                Ajout d'une notification pour le client
            ============================================ */
            $notificationC = new NotificationController();

            $titre = "Nouvelle proposition reçue";
            $message = "Vous avez reçu une nouvelle proposition pour votre projet";

            $notificationC->ajouterNotification($id_client, $id_pub, $titre, $message);
        }


        /* ============================================
            Redirection
        ============================================ */
        header('Location: ../projet_freelancer.php');
        exit;

    }
}

echo "❌ Erreur : veuillez remplir tous les champs.";
?>
