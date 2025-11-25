<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/blog.php';




class BlogController {
    function listBlogs() {
        $sql = "SELECT * FROM blog ORDER BY date_creation DESC";
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

   public function addBlog($blog)
{
    $sql = "INSERT INTO blog (Titre, contenue, Date_creation, image, nb_view)
            VALUES (:Titre, :contenue, :Date_creation, :image, :nb_view)";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute([
            'Titre' => $blog->getTitre(),
            'contenue' => $blog->getContenue(),
            'Date_creation' => $blog->getDateCreation(),
            'image' => $blog->getImage(),
            'nb_view' => $blog->getNbView()
        ]);
    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}


    function deleteBlog($id) {
        $sql = "DELETE FROM blog WHERE id_blog=:id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
    }

    function updateBlog($blog) {
    $sql = "UPDATE blog 
            SET titre = :titre, 
                contenue = :contenue, 
                image = :image, 
                nb_view = :nb_view
            WHERE id_blog = :id_blog";
    $db = config::getConnexion();
    $query = $db->prepare($sql);
    $query->execute([
        'id_blog' => $blog->getIdBlog(),
        'titre' => $blog->getTitre(),
        'contenue' => $blog->getContenue(),
        'image' => $blog->getImage(),
        'nb_view' => $blog->getNbView()
    ]);
}


    function getBlogById($id) {
        $sql = "SELECT * FROM blog WHERE id_blog=:id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch();
    }
}
?>
