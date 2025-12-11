<?php
class Avis {
    private $conn;
    private $table = "avis";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Helper: check if the table has a column (compatible with current DB connection)
    private function hasColumn($column) {
        try {
            $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :col";
            $stmt = $this->conn->prepare($sql);
            $table = $this->table;
            $stmt->bindParam(':table', $table);
            $stmt->bindParam(':col', $column);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    
    public function addAvis($nom, $email, $note, $contenu) {
        $query = "INSERT INTO {$this->table} (nom, email, note, contenu) VALUES (:nom, :email, :note, :contenu)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }

    
    public function getAllAvis() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAvisById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function deleteAvis($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    public function updateAvis($id, $nom, $email, $note, $contenu) {
        $setParts = [
            'nom = :nom',
            'email = :email',
            'note = :note',
            'contenu = :contenu'
        ];

        if ($this->hasColumn('updated_at')) {
            $setParts[] = 'updated_at = NOW()';
        }

        $setSql = implode(', ', $setParts);
        $query = "UPDATE {$this->table} SET {$setSql} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    public function getLikesCount($avis_id) {
        $query = "SELECT COUNT(*) FROM avis_likes WHERE avis_id = :avis_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    
    public function hasUserLiked($avis_id, $email) {
        $query = "SELECT COUNT(*) FROM avis_likes WHERE avis_id = :avis_id AND email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    
    public function addLike($avis_id, $email) {
        if ($this->hasUserLiked($avis_id, $email)) {
            return false;
        }
        $query = "INSERT INTO avis_likes (avis_id, email) VALUES (:avis_id, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    /**
     * Récupère les statistiques globales des avis
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_avis,
                    AVG(note) as average_note,
                    MIN(note) as min_note,
                    MAX(note) as max_note,
                    SUM(CASE WHEN note = 5 THEN 1 ELSE 0 END) as note_5_count,
                    SUM(CASE WHEN note = 4 THEN 1 ELSE 0 END) as note_4_count,
                    SUM(CASE WHEN note = 3 THEN 1 ELSE 0 END) as note_3_count,
                    SUM(CASE WHEN note = 2 THEN 1 ELSE 0 END) as note_2_count,
                    SUM(CASE WHEN note = 1 THEN 1 ELSE 0 END) as note_1_count,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 ELSE 0 END) as this_month_count
                  FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les avis filtrés et triés
     * @param string $sort_by : 'recent', 'oldest', 'highest_rated', 'lowest_rated'
     * @param int $min_note : note minimale (1-5)
     * @param int $max_note : note maximale (1-5)
     * @param int $limit
     * @param int $offset
     */
    public function getAvisByFilters($sort_by = 'recent', $min_note = 1, $max_note = 5, $limit = 10, $offset = 0) {
        $sort_sql = "created_at DESC"; // default recent
        
        switch($sort_by) {
            case 'oldest':
                $sort_sql = "created_at ASC";
                break;
            case 'highest_rated':
                $sort_sql = "note DESC, created_at DESC";
                break;
            case 'lowest_rated':
                $sort_sql = "note ASC, created_at DESC";
                break;
            case 'most_liked':
                $sort_sql = "(SELECT COUNT(*) FROM avis_likes WHERE avis_id = {$this->table}.id) DESC, created_at DESC";
                break;
        }

        $min_note = max(1, min(5, (int)$min_note));
        $max_note = max(1, min(5, (int)$max_note));

        $query = "SELECT * FROM {$this->table} 
                  WHERE note >= :min_note AND note <= :max_note
                  ORDER BY {$sort_sql}
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_note', $min_note, PDO::PARAM_INT);
        $stmt->bindParam(':max_note', $max_note, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche les avis par keyword (nom, email, contenu)
     */
    public function searchAvis($keyword, $limit = 10, $offset = 0) {
        $keyword = "%{$keyword}%";
        $query = "SELECT * FROM {$this->table}
                  WHERE nom LIKE :keyword OR email LIKE :keyword OR contenu LIKE :keyword
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le total des avis matchant un filtre
     */
    public function countAvisByFilters($min_note = 1, $max_note = 5) {
        $min_note = max(1, min(5, (int)$min_note));
        $max_note = max(1, min(5, (int)$max_note));
        
        $query = "SELECT COUNT(*) FROM {$this->table} 
                  WHERE note >= :min_note AND note <= :max_note";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_note', $min_note, PDO::PARAM_INT);
        $stmt->bindParam(':max_note', $max_note, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Compte le total des avis trouvés lors d'une recherche
     */
    public function countSearchResults($keyword) {
        $keyword = "%{$keyword}%";
        $query = "SELECT COUNT(*) FROM {$this->table}
                  WHERE nom LIKE :keyword OR email LIKE :keyword OR contenu LIKE :keyword";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les avis par note (distribution)
     */
    public function getAvisByNote($note) {
        $query = "SELECT * FROM {$this->table} WHERE note = :note ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':note', $note, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
