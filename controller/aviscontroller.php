<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';

class aviscontroller {
    private $avisModel;

    public function __construct() {
        $database = new database();
        $db = $database->getConnection();
        $this->avisModel = new Avis($db);
    }

    public function addAvis() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom']);
            $email = htmlspecialchars($_POST['email']);
            //$note = (int)$_POST['note'];
            $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
            $contenu = htmlspecialchars($_POST['avis']);

            if ($this->avisModel->addAvis($nom, $email, $note, $contenu)) {
                echo "Avis ajouté avec succès !";
            } else {
                echo "Erreur lors de l'ajout de l'avis.";
            }
        }
    }

    public function showAvis() {
        // Cette méthode doit uniquement inclure la vue une seule fois
        
        include_once __DIR__ . '/../view/avisfront.php';
    }




    public function showProfil() {
        // Même principe, inclure la vue une seule fois
        require_once __DIR__ . '/../view/profilfreelancer.php';
    }

}
