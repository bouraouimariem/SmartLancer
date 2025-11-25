<?php
include_once __DIR__ . '/../config.php'; // connexion à la base
include_once __DIR__ . '/../model/commentaire.php';

class CommentaireC {

    // 🔹 Ajouter un commentaire
    public function ajouterCommentaire($commentaire) {
        $sql = "INSERT INTO commentaire (namec, contenue, date_com, nbr_jaime, id_blog)
                VALUES (:namec, :contenue, :date_com, :nbr_jaime, :id_blog)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'namec' => $commentaire->getNamec(),
                'contenue' => $commentaire->getContenue(),
                'date_com' => $commentaire->getDateCom(),
                'nbr_jaime' => $commentaire->getNbrJaime(),
                'id_blog' => $commentaire->getIdBlog()
            ]);
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    // 🔹 Afficher tous les commentaires
    public function afficherCommentaires() {
        $sql = "SELECT * FROM commentaire";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

     // ✅ Like un commentaire
    public function likeCommentaire($id_com) {
        $db = config::getConnexion();
        $stmt = $db->prepare("UPDATE commentaire SET nbr_jaime = nbr_jaime + 1 WHERE id_com = :id");
        $stmt->execute(['id' => $id_com]);
    }

 

   

    // 🔹 Afficher les commentaires d’un blog spécifique
    public function afficherCommentairesParBlog($id_blog) {
        $sql = "SELECT * FROM commentaire WHERE id_blog = :id_blog ORDER BY date_com DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_blog' => $id_blog]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

     // 🔹 Récupérer les commentaires d’un blog précis
    public function getCommentairesByBlog($id_blog) {
        $sql = "SELECT * FROM commentaire WHERE id_blog = :id_blog ORDER BY date_com DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_blog' => $id_blog]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // 🔹 Supprimer un commentaire
    public function supprimerCommentaire($id_com) {
        $sql = "DELETE FROM commentaire WHERE id_com = :id_com";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_com' => $id_com]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // 🔹 Mettre à jour un commentaire (ex : modifier contenu)
    public function modifierCommentaire($commentaire, $id_com) {
        $sql = "UPDATE commentaire SET 
                    namec = :namec,
                    contenue = :contenue,
                    date_com = :date_com,
                    nbr_jaime = :nbr_jaime,
                    id_blog = :id_blog
                WHERE id_com = :id_com";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'namec' => $commentaire->getNamec(),
                'contenue' => $commentaire->getContenue(),
                'date_com' => $commentaire->getDateCom(),
                'nbr_jaime' => $commentaire->getNbrJaime(),
                'id_blog' => $commentaire->getIdBlog(),
                'id_com' => $id_com
            ]);
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    public function countCommentairesByBlog($id_blog) {
    $db = config::getConnexion();
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM commentaire WHERE id_blog = :id_blog");
    $stmt->execute(['id_blog' => $id_blog]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

}
?>
