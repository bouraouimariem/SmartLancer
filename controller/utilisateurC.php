<?php
include_once(__DIR__ . '/../config.php');

class utilisateurController {
  public function list_user_all() {
    $sql = "SELECT * FROM user";
    $db = config::getConnexion();
    return $db->query($sql)->fetchAll();
  }

  public function list_user_nom_asc() {
    $sql = "SELECT * FROM user ORDER BY nom ASC";
    $db = config::getConnexion();
    return $db->query($sql)->fetchAll();
  }

  public function list_user_nom_desc() {
    $sql = "SELECT * FROM user ORDER BY nom DESC";
    $db = config::getConnexion();
    return $db->query($sql)->fetchAll();
  }

  public function list_user_date_new() {
    $sql = "SELECT * FROM user ORDER BY date_inscription DESC";
    $db = config::getConnexion();
    return $db->query($sql)->fetchAll();
  }

  public function list_user_date_old() {
    $sql = "SELECT * FROM user ORDER BY date_inscription ASC";
    $db = config::getConnexion();
    return $db->query($sql)->fetchAll();
  }

  public function recherche_user($id) {
    $sql = "SELECT * FROM user WHERE id_utilisateur = :id";
    $db = config::getConnexion();
    $req = $db->prepare($sql);
    $req->execute(['id' => $id]);
    return $req->fetch();
  }

  public function delete_user($id) {
    $sql = "DELETE FROM user WHERE id_utilisateur  = :id";
    $db = config::getConnexion();
    $req = $db->prepare($sql);
    $req->execute(['id' => $id]);
  }
}
?>
