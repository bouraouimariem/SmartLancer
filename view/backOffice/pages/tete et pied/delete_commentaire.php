<?php
include_once '../../../../controller/commentaireC.php';

if (isset($_GET['id_com'])) {
    $commentaireC = new CommentaireC();
    $commentaireC->supprimerCommentaire($_GET['id_com']);
    header('Location: ../blogs.php'); // redirige vers la page principale
    exit();
} else {
    echo "❌ ID du commentaire non spécifié.";
}
?>
