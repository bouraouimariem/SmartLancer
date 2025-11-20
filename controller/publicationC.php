<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/publication.php';

class publicationController
{
//CREATE
    public function create_pub($pub)
    {
        $sql = "INSERT INTO publications 
                (id_user, nom_pub, categorie, description, budget, delai_requise, date_pub, status)
                VALUES 
                (:id_user, :nom_pub, :categorie, :description, :budget, :delai_requise, :date_pub, :status)";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_user'        => $pub->getIdUser(),
                'nom_pub'        => $pub->getNomPub(),
                'categorie'      => $pub->getCategorie(),
                'description'    => $pub->getDescription(),
                'budget'         => $pub->getBudget(),
                'delai_requise'  => $pub->getDelaiRequise(),
                'date_pub'       => $pub->getDatePub()->format('Y-m-d H:i:s'),
                'status'         => $pub->getStatus()
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

//READ

    // read une publication par id
    public function getPublicationById($id_pub)
    {
        $sql = "SELECT * FROM publications WHERE id_pub = :id_pub";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id_pub' => $id_pub]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Liste de toutes les publications
    public function list_pub_all()
    {
        $sql = "SELECT * FROM publications ORDER BY date_pub DESC";
        $db = config::getConnexion();

        try {
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Liste des publications d’un utilisateur
    public function listpub_for_user($id_user)
    {
        $sql = "SELECT * FROM publications WHERE id_user = :id_user ORDER BY date_pub DESC";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

//UPDATE
    public function updatepub($pub, $id_pub)
    {
        $sql = "UPDATE publications SET 
                nom_pub = :nom_pub,
                description = :description,
                budget = :budget,
                delai_requise = :delai_requise,
                categorie = :categorie
                WHERE id_pub = :id";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'            => $id_pub,
                'nom_pub'       => $pub->getNomPub(),
                'description'   => $pub->getDescription(),
                'budget'        => $pub->getBudget(),
                'delai_requise' => $pub->getDelaiRequise(),
                'categorie'     => $pub->getCategorie()
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

//DELETE
    public function delete_pub($id_pub)
    {
        $db = config::getConnexion();

        try {
            // Supprimer d'abord les propositions liées
            $sql1 = "DELETE FROM propositions WHERE id_pub = :id";
            $req1 = $db->prepare($sql1);
            $req1->execute(['id' => $id_pub]);

            // Puis supprimer la publication
            $sql2 = "DELETE FROM publications WHERE id_pub = :id";
            $req2 = $db->prepare($sql2);
            $req2->execute(['id' => $id_pub]);

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
