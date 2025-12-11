<?php
require_once __DIR__ . '/../model/Portfolio.php';

class PortfolioController {

    public function create() {
        

        if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Freelancer') {
            header("Location: index.php?route=login");
            exit();
        }

        $model = new Portfolio();

        // Upload image
        $photoName = null;
        if (!empty($_FILES['photo']['name'])) {
            $photoName = time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../uploads/' . $photoName);
        }

        // Save in DB
        $model->create([
    'id_utilisateur' => $_SESSION['id'],
    'photo'          => $photoName,
    'lien'           => $_POST['lien'],
    'bio'            => $_POST['bio'],
    'experience'     => $_POST['experience'],
    'competence' => $_POST['competence_tags'],
    'tarif'          => $_POST['tarif']
]);



        // Redirect to freelancer home
        header("Location: index.php?route=freelancer");
        exit;

    }


    public function show() {

    require_once __DIR__ . '/../model/Portfolio.php';
    $model = new Portfolio();

    // Si id est passé dans l'URL, on affiche le profil PUBLIC
    if (isset($_GET['id'])) {
        $portfolio = $model->getByUserId($_GET['id']);

        if (!$portfolio) {
            echo "Aucun profil trouvé.";
            exit();
        }

        require __DIR__ . '/../view/frontoffice/freelancer/public_profile.php';
        return;
    }

    // Sinon → profil du freelancer connecté
    if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Freelancer") {
        header("Location: index.php?route=login");
        exit();
    }

    $portfolio = $model->getByUserId($_SESSION['id']);

    if (!$portfolio) {
        echo "Aucun profil trouvé.";
        exit();
    }

    require __DIR__ . '/../view/frontoffice/freelancer/profiles.php';
}


    public function update() {

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Freelancer") {
        header("Location: index.php?route=login");
        exit();
    }

    require_once __DIR__ . '/../model/Portfolio.php';
    $model = new Portfolio();

    // Récupérer l'ancien portfolio
    $portfolio = $model->getByUserId($_SESSION['id']);

    if (!$portfolio) {
        echo "Portfolio introuvable.";
        exit;
    }

    // Nouvelle photo
    $photoName = $portfolio['photo'];

    if (!empty($_FILES['photo']['name'])) {
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../uploads/' . $photoName);
    }

    // Préparer les données
    $data = [
        'id_utilisateur' => $_SESSION['id'],
        'photo'          => $photoName,
        'lien'           => $_POST['lien'],
        'bio'            => $_POST['bio'],
        'experience'     => $_POST['experience'],
        'competence' => $_POST['competence_tags'],
        'tarif'          => $_POST['tarif']
    ];

    // Mise à jour dans la BD
    $model->updatePortfolio($data);

    // Retour à la page profil
    header("Location: index.php?route=profil");
    exit();
}

public function listAll() {
    $model = new Portfolio();
    $freelancers = $model->getAllFreelancers();

    require __DIR__ . '/../view/frontoffice/all_freelancers.php';
}


}
