<?php
class Blog {
    private $id_blog;
    private $titre;
    private $contenue;
    private $date_creation;
    private $image;
    private $nb_view;

    public function __construct($id_blog, $titre, $contenue, $date_creation, $image, $nb_view) {
        $this->id_blog = $id_blog;
        $this->titre = $titre;
        $this->contenue = $contenue;
        $this->date_creation = $date_creation;
        $this->image = $image;
        $this->nb_view = $nb_view;
    }

    // ✅ Setter
    public function setIdBlog($id_blog) {
        $this->id_blog = $id_blog;
    }

    // ✅ Getters
    public function getIdBlog() { return $this->id_blog; }
    public function getTitre() { return $this->titre; }
    public function getContenue() { return $this->contenue; }
    public function getDateCreation() { return $this->date_creation; }
    public function getImage() { return $this->image; }
    public function getNbView() { return $this->nb_view; }
}
?>
