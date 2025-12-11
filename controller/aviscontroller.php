<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';
require_once __DIR__ . '/../model/validator.php';

class aviscontroller {
    private $avisModel;

    public function __construct() {
        $database = new database();
        $db = $database->getConnection();
        $this->avisModel = new Avis($db);
    }

    public function addAvis() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Réinitialiser les erreurs
            Validator::resetErrors();

            // Récupérer et valider les données
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
            $contenu = $_POST['avis'] ?? '';

            // Validations
            $isValid = true;
            if (!Validator::validateNom($nom)) {
                $isValid = false;
            }
            if (!Validator::validateEmail($email)) {
                $isValid = false;
            }
            if (!Validator::validateNote($note)) {
                $isValid = false;
            }
            if (!Validator::validateContenu($contenu)) {
                $isValid = false;
            }

            if (!$isValid) {
                echo "Erreur : " . implode(' | ', Validator::getErrors());
                return;
            }

            // Nettoyer les données
            $nom = Validator::sanitize($nom);
            $email = Validator::sanitize($email);
            $contenu = Validator::sanitize($contenu);

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
