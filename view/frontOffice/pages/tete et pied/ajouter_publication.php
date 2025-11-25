<?php
include '../../../../controller/publicationC.php';
$error = "";
$pub = null;
$publicationC = new publicationController();
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}
$id_user = $_SESSION['id_user'];

if (
    isset($_POST["nom_pub"]) && isset($_POST["description"]) && isset($_POST["budget"])
    && isset($_POST["delai"]) && isset($_POST["categories"])
) {
    if (
        !empty($_POST["nom_pub"]) && !empty($_POST["description"]) && !empty($_POST["budget"])
        && !empty($_POST["delai"]) && !empty($_POST["categories"])
    ) {
        $date_pub = new DateTime();
        $status = 'en cours';
        // Process categories
        if (is_array($_POST["categories"])) {
            $categories = implode(", ", $_POST["categories"]); // Convert array to string
        } else {
            $categories = "";
        }
        
        $delai = $_POST['delai'];
        $pub = new Publications(
            null,
            $id_user,
            $_POST['nom_pub'],
            $categories, // Fixed categories handling
            $_POST['description'],
            $_POST['budget'],
            $delai,
            $date_pub,
            $status
        );

        $publicationC->create_pub($pub);
    

        header('location: ../projet_client.php');
        exit;
    }
}

?>