<?php
class Commentaire {
    private $id_com;
    private $namec;
    private $contenue;
    private $date_com;
    private $nbr_jaime;
    private $id_blog;

    // Constructeur
    public function __construct($id_com, $namec, $contenue, $date_com, $nbr_jaime, $id_blog) {
        $this->id_com = $id_com;
        $this->namec = $namec;
        $this->contenue = $contenue;
        $this->date_com = $date_com;
        $this->nbr_jaime = $nbr_jaime;
        $this->id_blog = $id_blog;
    }

    // 🔹 Getters
    public function getIdCom() { return $this->id_com; }
    public function getNamec() { return $this->namec; }
    public function getContenue() { return $this->contenue; }
    public function getDateCom() { return $this->date_com; }
    public function getNbrJaime() { return $this->nbr_jaime; }
    public function getIdBlog() { return $this->id_blog; }

    // 🔹 Setters (au cas où tu modifies un commentaire)
    public function setIdCom($id_com) { $this->id_com = $id_com; }
    public function setNamec($namec) { $this->namec = $namec; }
    public function setContenue($contenue) { $this->contenue = $contenue; }
    public function setDateCom($date_com) { $this->date_com = $date_com; }
    public function setNbrJaime($nbr_jaime) { $this->nbr_jaime = $nbr_jaime; }
    public function setIdBlog($id_blog) { $this->id_blog = $id_blog; }
}
?>
