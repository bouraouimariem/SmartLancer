<?php

include_once(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/publication.php');
require_once __DIR__ . '/notificationC.php';

class publicationController
{
    // ----------------------------
    // CREATE PUBLICATION + NOTIF
    // ----------------------------
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
                'id_user' => $pub->getIdUser(),
                'nom_pub' => $pub->getNomPub(),
                'categorie' => $pub->getCategorie(),
                'description' => $pub->getDescription(),
                'budget' => $pub->getBudget(),
                'delai_requise' => $pub->getDelaiRequise(),
                'date_pub' => $pub->getDatePub()->format('Y-m-d H:i:s'),
                'status' => $pub->getStatus(),
            ]);

            // ID publication
            $id_pub = $db->lastInsertId();

            // Notification admin
            $notif = new NotificationController();
            $notif->ajouterNotification(
                0, // admin
                $id_pub,
                "Nouvelle publication",
                "Un utilisateur a ajouté une nouvelle publication."
            );

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // ----------------------------
    // LIST BY USER
    public function listpub_for_user($id_user)
    {
        $sql = "SELECT * FROM publications WHERE id_user = :id_user ORDER BY date_pub DESC";

        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ----------------------------
    // LIST ALL

    public function list_pub_all()
    {
        $sql = "SELECT * FROM publications ORDER BY date_pub DESC";

        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ----------------------------
    // LIST FOR FREELANCER

    public function list_pub_for_freelancer($id_user)
    {
        $sql = "SELECT * FROM publications p 
                WHERE p.status = 'en cours' 
                AND NOT EXISTS (
                    SELECT 1 FROM propositions pr 
                    WHERE pr.id_pub = p.id_pub 
                    AND pr.id_user = :id_user
                )";

        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_user', $id_user);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

 // <<<<< FIN RÉELLE DE LA CLASSE publicationController


    public function list_pub_propo($id_user)
    {
        $sql = "SELECT * FROM publications p WHERE EXISTS (SELECT 1 FROM propositions pr WHERE pr.id_pub = p.id_pub AND pr.id_user = :id_user)";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_user', $id_user);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function delete_pub($id_pub)
    {
        $sql1 = "DELETE FROM propositions WHERE id_pub = :id";
        $sql2 = "DELETE FROM publications WHERE id_pub = :id";

        $db = config::getConnexion();
        $req1 = $db->prepare($sql1);
        $req1->bindValue(':id', $id_pub);
        $req2 = $db->prepare($sql2);
        $req2->bindValue(':id', $id_pub);
        try {
            $req1->execute();
            $req2->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    function updatepub($pub, $id_pub)
    {
        var_dump($pub);
        try {
            $db = config::getConnexion();

            $query = $db->prepare(
                'UPDATE publications SET 
                    nom_pub = :nom_pub,
                    description = :description,
                    budget = :budget,
                    delai_requise = :delai_requise
                WHERE id_pub = :id'
            );


            $query->execute([
                'id' => $id_pub,
                'nom_pub' => $pub->getNomPub(),
                'description' => $pub->getDescription(),
                'budget' => $pub->getBudget(),
                'delai_requise' => $pub->getDelaiRequise()
            ]);

            echo $query->rowCount() . " records UPDATED successfully <br>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public function getPublicationById($id_pub)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare("SELECT * FROM publications WHERE id_pub = :id_pub");
            $query->execute(['id_pub' => $id_pub]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error retrieving publication: " . $e->getMessage());
        }
    }
    public function recherche_pub($id_pub)
    {
        $sql = "SELECT * FROM publications WHERE id_pub = :id_pub";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id_pub', $id_pub);
            $query->execute();
            $result = $query->fetch();
            return $result;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    public function list_pub_budget_asc()
    {
        $sql = "SELECT * FROM publications ORDER BY budget ASC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    public function list_pub_budget_desc()
    {
        $sql = "SELECT * FROM publications ORDER BY budget DESC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    public function list_pub_date_new()
    {
        $sql = "SELECT * FROM publications ORDER BY date_pub DESC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function list_pub_date_old()
    {
        $sql = "SELECT * FROM publications ORDER BY date_pub ASC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    function getPublicationsByBudgetRange($min, $max) {
        $sql = "SELECT * FROM publications WHERE budget BETWEEN :min AND :max";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':min', $min, PDO::PARAM_INT);
            $query->bindValue(':max', $max, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    public function modif_status_pub($id_pub)
    {
        $sql = "UPDATE publications SET status = 'accepte' WHERE id_pub = :id AND status = 'en cours'";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id_pub);
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    

public function filterForFreelancer($id_user, $search = '', $categorie = '', $min = 0, $max = 999999)
{
    $sql = "SELECT p.* FROM publications p
            WHERE p.status = 'en cours'
              AND p.budget BETWEEN :min AND :max
              AND NOT EXISTS (
                  SELECT 1 FROM propositions pr
                  WHERE pr.id_pub = p.id_pub
                    AND pr.id_user = :id_user
              )";

    if (!empty($search)) {
        $sql .= " AND p.nom_pub LIKE :search";
    }
    if (!empty($categorie)) {
        $sql .= " AND p.categorie = :categorie";
    }

    $db = config::getConnexion();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':min', (int)$min, PDO::PARAM_INT);
    $stmt->bindValue(':max', (int)$max, PDO::PARAM_INT);
    $stmt->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);

    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }
    if (!empty($categorie)) {
        $stmt->bindValue(':categorie', $categorie, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function countPublications() {
    $sql = "SELECT COUNT(*) AS total FROM publications";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute();
        $result = $query->fetch();
        return $result['total'];
    } catch (Exception $e) {
        die('Erreur: '.$e->getMessage());
    }
}

public function getLastThreePublications() {
    $sql = "SELECT * FROM publications ORDER BY id_pub DESC LIMIT 3";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}


public function getLastPublication()
{
    $db = config::getConnexion();
    $sql = "SELECT * FROM publications ORDER BY date_pub DESC LIMIT 1";
    try {
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

}  // ←<<<<<<<<< ICI ! FIN DE LA CLASSE publicationController

=======
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
>>>>>>> 1e444ac301c0d52777258097cbcb0b957affa058
?>
