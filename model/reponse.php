<?php
class Reponse {
    private $conn;
    private $table = 'reponses';

    public function __construct($db) {
        $this->conn = $db;
    }
    // Helper: check if the table has a column (works with current connection)
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
            // if information_schema not accessible, assume false
            return false;
        }
    }

    // Ajoute une réponse: compatible avec schéma ancien ou enrichi
    public function addReponse($avis_id, $nom, $email, $contenu, $visible = 1, $type = 'freelance', $role_repondeur = null, $statut = null, $is_online = 0, $piece_jointe = null, $categorie = null) {
        // Table: id, avis_id, nom, email, contenu, created_at, version_history, updated_at, visible, type, role_repondeur, statut, is_online, last_activity, piece_jointe, categorie
        // supporte les anciennes tables sans les colonnes optionnelles
        $cols = ['avis_id', 'nom', 'email', 'contenu', 'created_at'];
        $values = [':avis_id', ':nom', ':email', ':contenu', 'NOW()'];

        // prepare initial version_history if supported
        $initialVersionJson = null;
        if ($this->hasColumn('version_history')) {
            $initialHistory = [
                [
                    'version' => 1,
                    'nom' => $nom,
                    'email' => $email,
                    'contenu' => $contenu,
                    'visible' => $this->hasColumn('visible') ? ($visible ? 1 : 0) : null,
                    'type' => $this->hasColumn('type') ? $type : null,
                    'role_repondeur' => $this->hasColumn('role_repondeur') ? $role_repondeur : null,
                    'statut' => $this->hasColumn('statut') ? $statut : null,
                    'is_online' => $this->hasColumn('is_online') ? $is_online : null,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $initialVersionJson = json_encode($initialHistory);
            $cols[] = 'version_history';
            $values[] = ':version_history';
        }

        if ($this->hasColumn('visible')) {
            $cols[] = 'visible';
            $values[] = ':visible';
        }
        if ($this->hasColumn('type')) {
            $cols[] = 'type';
            $values[] = ':type';
        }
        if ($this->hasColumn('role_repondeur')) {
            $cols[] = 'role_repondeur';
            $values[] = ':role_repondeur';
        }
        if ($this->hasColumn('statut')) {
            $cols[] = 'statut';
            $values[] = ':statut';
        }
        if ($this->hasColumn('is_online')) {
            $cols[] = 'is_online';
            $values[] = ':is_online';
        }
        if ($this->hasColumn('piece_jointe')) {
            $cols[] = 'piece_jointe';
            $values[] = ':piece_jointe';
        }
        if ($this->hasColumn('categorie')) {
            $cols[] = 'categorie';
            $values[] = ':categorie';
        }
        // notifier_auteur column removed from schema; no longer handled.
        if ($this->hasColumn('last_activity')) {
            $cols[] = 'last_activity';
            $values[] = 'NOW()';
        }

        $cols_sql = implode(', ', $cols);
        $vals_sql = implode(', ', $values);

        $query = "INSERT INTO {$this->table} ({$cols_sql}) VALUES ({$vals_sql})";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contenu', $contenu);
        if ($this->hasColumn('version_history')) {
            $stmt->bindParam(':version_history', $initialVersionJson);
        }
        if ($this->hasColumn('visible')) {
            $v = $visible ? 1 : 0;
            $stmt->bindParam(':visible', $v, PDO::PARAM_INT);
        }
        if ($this->hasColumn('type')) {
            $t = $type;
            $stmt->bindParam(':type', $t);
        }
        if ($this->hasColumn('role_repondeur')) {
            $stmt->bindParam(':role_repondeur', $role_repondeur);
        }
        if ($this->hasColumn('statut')) {
            $stmt->bindParam(':statut', $statut);
        }
        if ($this->hasColumn('is_online')) {
            $io = $is_online ? 1 : 0;
            $stmt->bindParam(':is_online', $io, PDO::PARAM_INT);
        }
        if ($this->hasColumn('piece_jointe')) {
            $stmt->bindParam(':piece_jointe', $piece_jointe);
        }
        if ($this->hasColumn('categorie')) {
            $stmt->bindParam(':categorie', $categorie);
        }
        // notifier_auteur removed
        return $stmt->execute();
    }

    public function getByAvisId($avis_id) {
        $query = "SELECT * FROM {$this->table} WHERE avis_id = :avis_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les réponses avec les informations de l'avis associé (jointure)
    public function getAllWithAvis() {
        // Assumer que la table `avis` existe dans la même base
        try {
            $query = "SELECT r.*, a.nom AS avis_auteur, a.contenu AS avis_contenu, a.note AS avis_note, a.created_at AS avis_created_at FROM {$this->table} r JOIN avis a ON r.avis_id = a.id ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // fallback: retourner simplement toutes les réponses si la jointure échoue
            return $this->getAll();
        }
    }

    public function deleteByAvisId($avis_id) {
        $query = "DELETE FROM {$this->table} WHERE avis_id = :avis_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Met à jour une réponse. Si le schéma supporte le versioning, l'ancienne
     * version est stockée dans `version_history` et `version_number` est incrémenté.
     * Sinon, on effectue une mise à jour simple compatible.
     */
    public function updateReponse($id, $nom, $email, $contenu, $visible = null, $type = null, $role_repondeur = null, $statut = null) {
        $existing = $this->getById($id);
        if (!$existing) return false;

        // Historique de version (stocké dans version_history)
        $history = [];
        if (!empty($existing['version_history'])) {
            $decoded = json_decode($existing['version_history'], true);
            if (is_array($decoded)) $history = $decoded;
        }
        $nextVersion = count($history) + 1;
        $history[] = [
            'version' => $nextVersion,
            'nom' => $existing['nom'],
            'email' => $existing['email'],
            'contenu' => $existing['contenu'],
            'visible' => isset($existing['visible']) ? $existing['visible'] : null,
            'type' => isset($existing['type']) ? $existing['type'] : null,
            'role_repondeur' => isset($existing['role_repondeur']) ? $existing['role_repondeur'] : null,
            'statut' => isset($existing['statut']) ? $existing['statut'] : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $version_history_json = json_encode($history);

        // Determine which columns are present and should be updated
        $setParts = [
            'nom = :nom',
            'email = :email',
            'contenu = :contenu',
            'version_history = :version_history'
        ];
        
        // Always update updated_at if column exists
        if ($this->hasColumn('updated_at')) {
            $setParts[] = 'updated_at = NOW()';
        }

        // prepare new values if provided, otherwise keep existing values
        $newVisible = $existing['visible'] ?? null;
        $newType = $existing['type'] ?? null;
        $newRoleRepondeur = $existing['role_repondeur'] ?? null;
        $newStatut = $existing['statut'] ?? null;
        if ($this->hasColumn('visible')) {
            if ($visible !== null) $newVisible = $visible ? 1 : 0;
            $setParts[] = 'visible = :visible';
        }
        if ($this->hasColumn('type')) {
            if ($type !== null) $newType = $type;
            $setParts[] = 'type = :type';
        }
        if ($this->hasColumn('role_repondeur')) {
            if ($role_repondeur !== null) $newRoleRepondeur = $role_repondeur;
            $setParts[] = 'role_repondeur = :role_repondeur';
        }
        if ($this->hasColumn('statut')) {
            if ($statut !== null) $newStatut = $statut;
            $setParts[] = 'statut = :statut';
        }

        $setSql = implode(', ', $setParts);
        $query = "UPDATE {$this->table} SET {$setSql} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':version_history', $version_history_json);
        if ($this->hasColumn('visible')) {
            $stmt->bindParam(':visible', $newVisible, PDO::PARAM_INT);
        }
        if ($this->hasColumn('type')) {
            $stmt->bindParam(':type', $newType);
        }
        if ($this->hasColumn('role_repondeur')) {
            $stmt->bindParam(':role_repondeur', $newRoleRepondeur);
        }
        if ($this->hasColumn('statut')) {
            $stmt->bindParam(':statut', $newStatut);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteById($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Marque un utilisateur comme en ligne ou hors ligne et met à jour last_activity
     */
    public function setOnlineStatus($id, $is_online = true) {
        if (!$this->hasColumn('is_online')) return false;
        
        $parts = [];
        if ($this->hasColumn('is_online')) {
            $parts[] = 'is_online = :is_online';
        }
        if ($this->hasColumn('last_activity')) {
            $parts[] = 'last_activity = NOW()';
        }
        
        if (empty($parts)) return false;
        
        $setSql = implode(', ', $parts);
        $query = "UPDATE {$this->table} SET {$setSql} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        if ($this->hasColumn('is_online')) {
            $io = $is_online ? 1 : 0;
            $stmt->bindParam(':is_online', $io, PDO::PARAM_INT);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Récupère l'historique de versions décodé (tableau) pour une réponse
     */
    public function getVersionHistory($id) {
        $row = $this->getById($id);
        if (!$row) return [];
        if (empty($row['version_history'])) return [];
        $decoded = json_decode($row['version_history'], true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Récupère les statistiques des réponses
     */
    /**
     * Récupère les statistiques détaillées des réponses
     * Inclut les comptages par: statut, visibilité, rôle, type, catégorie
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total";
        
        // Visibilité
        if ($this->hasColumn('visible')) {
            $query .= ",
                    SUM(CASE WHEN visible = 1 THEN 1 ELSE 0 END) as visible_count,
                    SUM(CASE WHEN visible = 0 THEN 1 ELSE 0 END) as hidden_count";
        }
        
        // Statut (en_attente, approuvée, rejetée)
        if ($this->hasColumn('statut')) {
            $query .= ",
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN statut = 'approuvée' THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN statut = 'rejetée' THEN 1 ELSE 0 END) as rejected_count";
        }
        
        // Rôle du répondeur (freelancer, admin, support, client)
        if ($this->hasColumn('role_repondeur')) {
            $query .= ",
                    SUM(CASE WHEN role_repondeur = 'freelancer' THEN 1 ELSE 0 END) as freelancer_count,
                    SUM(CASE WHEN role_repondeur = 'admin' THEN 1 ELSE 0 END) as admin_count,
                    SUM(CASE WHEN role_repondeur = 'support' THEN 1 ELSE 0 END) as support_count,
                    SUM(CASE WHEN role_repondeur = 'client' THEN 1 ELSE 0 END) as client_count";
        }
        
        // Type de réponse (freelance, admin)
        if ($this->hasColumn('type')) {
            $query .= ",
                    SUM(CASE WHEN type = 'freelance' THEN 1 ELSE 0 END) as type_freelance_count,
                    SUM(CASE WHEN type = 'admin' THEN 1 ELSE 0 END) as type_admin_count";
        }
        
        // Catégories
        if ($this->hasColumn('categorie')) {
            $query .= ",
                    SUM(CASE WHEN categorie = 'remerciement' THEN 1 ELSE 0 END) as category_thanks_count,
                    SUM(CASE WHEN categorie = 'justification' THEN 1 ELSE 0 END) as category_justification_count,
                    SUM(CASE WHEN categorie = 'amelioration' THEN 1 ELSE 0 END) as category_improvement_count,
                    SUM(CASE WHEN categorie IS NOT NULL AND categorie != '' THEN 1 ELSE 0 END) as categorized_count";
        }
        
        // Pièces jointes
        if ($this->hasColumn('piece_jointe')) {
            $query .= ",
                    SUM(CASE WHEN piece_jointe IS NOT NULL AND piece_jointe != '' THEN 1 ELSE 0 END) as with_attachment_count";
        }
        
        // Notifications
        // notifier_auteur removed from schema; skip notification_enabled_count
        
        $query .= " FROM {$this->table}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réponses filtrées et triées
     * @param string $sort_by : 'recent', 'oldest', 'most_visible'
     * @param bool $visible_only : afficher seulement les visibles
     * @param int $limit
     * @param int $offset
     */
    public function getReponsesByFilters($sort_by = 'recent', $visible_only = false, $limit = 10, $offset = 0) {
        $sort_sql = "r.created_at DESC"; // default recent
        
        switch($sort_by) {
            case 'oldest':
                $sort_sql = "r.created_at ASC";
                break;
            case 'recent_modified':
                $sort_sql = $this->hasColumn('updated_at') ? "r.updated_at DESC" : "r.created_at DESC";
                break;
            case 'most_liked':
                $sort_sql = "r.created_at DESC";
                break;
        }

        $where_parts = ['1=1'];
        if ($visible_only && $this->hasColumn('visible')) {
            $where_parts[] = "r.visible = 1";
        }
        
        $where_sql = implode(' AND ', $where_parts);

        $query = "SELECT r.*, a.nom AS avis_auteur, a.contenu AS avis_contenu, a.note AS avis_note
                  FROM {$this->table} r
                  LEFT JOIN avis a ON r.avis_id = a.id
                  WHERE {$where_sql}
                  ORDER BY {$sort_sql}
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche les réponses par keyword (nom, email, contenu)
     */
    public function searchReponses($keyword, $visible_only = false, $limit = 10, $offset = 0) {
        $keyword = "%{$keyword}%";
        
        $where_parts = ['(r.nom LIKE :keyword OR r.email LIKE :keyword OR r.contenu LIKE :keyword)'];
        if ($visible_only && $this->hasColumn('visible')) {
            $where_parts[] = 'r.visible = 1';
        }
        
        $where_sql = implode(' AND ', $where_parts);
        
        $query = "SELECT r.*, a.nom AS avis_auteur, a.contenu AS avis_contenu, a.note AS avis_note
                  FROM {$this->table} r
                  LEFT JOIN avis a ON r.avis_id = a.id
                  WHERE {$where_sql}
                  ORDER BY r.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le total des réponses matchant un filtre
     */
    public function countReponsesByFilters($visible_only = false) {
        $where_parts = ['1=1'];
        if ($visible_only && $this->hasColumn('visible')) {
            $where_parts[] = 'visible = 1';
        }
        
        $where_sql = implode(' AND ', $where_parts);
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE {$where_sql}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Compte le total des réponses trouvées lors d'une recherche
     */
    public function countSearchResults($keyword, $visible_only = false) {
        $keyword = "%{$keyword}%";
        
        $where_parts = ['(nom LIKE :keyword OR email LIKE :keyword OR contenu LIKE :keyword)'];
        if ($visible_only && $this->hasColumn('visible')) {
            $where_parts[] = 'visible = 1';
        }
        
        $where_sql = implode(' AND ', $where_parts);
        
        $query = "SELECT COUNT(*) FROM {$this->table}
                  WHERE {$where_sql}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les réponses par avis avec statistiques
     */
    public function getReponsesByAvisWithStats($avis_id) {
        $query = "SELECT r.*,
                    COUNT(DISTINCT al.email) as like_count,
                    CASE WHEN r.is_online = 1 THEN 'En ligne' ELSE 'Hors ligne' END as status_text
                  FROM {$this->table} r
                  LEFT JOIN avis_likes al ON al.avis_id = :avis_id
                  WHERE r.avis_id = :avis_id
                  GROUP BY r.id
                  ORDER BY r.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réponses par rôle
     */
    public function getReponsesByRole($role) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE role_repondeur = :role AND visible = 1
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

