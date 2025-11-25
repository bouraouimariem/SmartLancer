<?php
include '../../../../controller/utilisateurC.php';

if (isset($_GET['id_user'])) {
  $utilisateurC = new utilisateurController();
  $utilisateurC->delete_user($_GET['id_user']);
}

header('Location: ../gestion_utilisateurs.php');
exit;
?>
