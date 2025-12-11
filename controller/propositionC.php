<?php
include_once(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/proposition.php');

class propositionController
{
    public function create_propo($propo)
    {
        $sql = "INSERT INTO propositions 
        (id_user, id_pub, commentaire, montant_propo, delai_estime, date_propo, status) 
        VALUES (:id_user, :id_pub, :commentaire, :montant_propo, :delai_estime, :date_propo, :status)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_user' => $propo->getIdUser(),
                'id_pub' => $propo->getIdPub(),
                'commentaire' => $propo->getCommentaire(),
                'montant_propo' => $propo->getMontantProp(),
                'delai_estime' => $propo->getDelaiEstime(),
                'date_propo' => $propo->getDatePropo()->format('Y-m-d H:i:s'),
                'status' => $propo->getStatus(),
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function list_propo()
    {
        $sql = 'SELECT * FROM propositions ORDER BY date_propo DESC';
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function list_propo_freelancer($id_pub, $id_user)
    {
        $sql = 'SELECT * FROM propositions WHERE id_pub = :id_pub AND id_user = :id_user';
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_pub', $id_pub);
        $stmt->bindValue(':id_user', $id_user);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('ERROR:' . $e->getMessage());
        }
    }

    public function list_propo_client($id_pub)
    {
        $sql = 'SELECT * FROM propositions WHERE id_pub = :id_pub';
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_pub', $id_pub);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('ERROR:' . $e->getMessage());
        }
    }

    public function delete_propo($id_propo)
    {
        $sql = "DELETE FROM propositions WHERE id_propo = :id";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id_propo);

        try {
            $stmt->execute();
        } catch (Exception $e) {
            die('ERROR:' . $e->getMessage());
        }
    }

    public function find_propo($id_pub, $id_user)
    {
        $sql = "SELECT * FROM propositions WHERE id_pub = :id_pub AND id_user = :id_user";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_pub', $id_pub);
        $stmt->bindValue(':id_user', $id_user);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }

    public function update_propo($propo, $id_propo)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE propositions SET 
                    commentaire = :commentaire,
                    montant_propo = :montant_propo,
                    delai_estime = :delai_estime
                WHERE id_propo = :id'
            );

            $query->execute([
                'id' => $id_propo,
                'commentaire' => $propo->getCommentaire(),
                'montant_propo' => $propo->getMontantProp(),
                'delai_estime' => $propo->getDelaiEstime(),
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getPropositionByUserAndPub($id_user, $id_pub)
    {
        $sql = "SELECT * FROM propositions WHERE id_user = :id_user AND id_pub = :id_pub";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_user', $id_user);
        $query->bindValue(':id_pub', $id_pub);

        try {
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function recherche_propo($id_propo)
    {
        $sql = "SELECT * FROM propositions WHERE id_propo = :id_propo";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id_propo', $id_propo);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function list_propo_montant_asc()
    {
        $sql = "SELECT * FROM propositions ORDER BY montant_propo ASC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function list_propo_montant_desc()
    {
        $sql = "SELECT * FROM propositions ORDER BY montant_propo DESC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function list_propo_date_new()
    {
        $sql = "SELECT * FROM propositions ORDER BY date_propo DESC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function list_propo_date_old()
    {
        $sql = "SELECT * FROM propositions ORDER BY date_propo ASC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function get_proposition_by_id($id_propo)
    {
        $sql = "SELECT * FROM propositions WHERE id_propo = :id_propo";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_propo' => $id_propo]);
            return $stmt->fetch();
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    public function modif_status_propo($id_propo)
    {
        $sql = "UPDATE propositions SET status = 'accepte' 
                WHERE id_propo = :id AND status = 'en cours'";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id_propo);
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    public function change_propo_pubstatus($id_pub, $status)
    {
        $sql = "UPDATE propositions 
                SET status = :status 
                WHERE id_pub = :id_pub AND status = 'en cours'";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id_pub', $id_pub);
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    public function list_propo_par_user($id_user)
    {
        $sql = "SELECT * FROM propositions WHERE id_user = :id_user ORDER BY date_propo DESC";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_user' => $id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function update_propo_stat($id_propo, $status)
    {
        $sql = "UPDATE propositions SET status = :status WHERE id_propo = :id";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->bindParam(':status', $status);
            $query->bindParam(':id', $id_propo);
            $query->execute();
        } catch (PDOException $e) {
            echo "Error updating status: " . $e->getMessage();
        }
    }

    /* ✅ AJOUT DE LA FONCTION DEMANDÉE */
    public function getLastProposition()
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM propositions ORDER BY date_propo DESC LIMIT 1";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
public function list_propo_all()
{
    $sql = 'SELECT p.*, u.nom AS user_nom, pub.nom_pub
            FROM propositions p
            LEFT JOIN user u ON p.id_user = u.id_utilisateur
            LEFT JOIN publications pub ON p.id_pub = pub.id_pub
            ORDER BY p.date_propo DESC';
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}

    
}
?>
