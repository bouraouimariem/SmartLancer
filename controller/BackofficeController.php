<?php
require_once __DIR__ . '/../model/User.php';

class BackofficeController {

    

    public function deleteUser() {

        

        if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?route=login");
            exit();
        }

        if (isset($_POST['id'])) {
            $model = new User();
            $model->deleteUser($_POST['id']);
        }

        header("Location: index.php?route=backoffice");
        exit();
    }


    public function banUser() {
    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
        header("Location: index.php?route=login");
        exit();
    }

    if (isset($_POST['id']) && isset($_POST['status'])) {

        $model = new User();

        // Mettre à jour le statut (ban / unban)
        $model->setBanStatus($_POST['id'], $_POST['status']);

        // Récupérer les infos de l'utilisateur
        $user = $model->getUserById($_POST['id']);
        $email = $user['email'];
        $name = $user['name'];
        $userId = $user['id'];

        // Si statut = 1 => BANNI
        if ($_POST['status'] == 1) {

            // 🔥  Génération d’un token
            $token = bin2hex(random_bytes(32));

            // 🔥  Sauvegarder le token dans la base de données
            $model->saveBanToken($userId, $token);

            $link = "http://localhost/project/index.php?route=view_ban&token=" . $token;

            $subject = "Votre compte a été banni - SmartLancer";

            $message = "
            Bonjour $name,

            Votre compte SmartLancer a été temporairement suspendu.

            ❗ *Pourquoi votre compte a été banni ?*
            - Violation potentielle des conditions d’utilisation
            - Comportement inapproprié ou signalé
            - Contenu frauduleux ou suspect

            ✔ *En savoir plus sur votre bannissement :*
            $link

            Si vous pensez que c’est une erreur, contactez-nous : support@smartlancer.com

            Cordialement,
            L'équipe SmartLancer
            ";

            $headers = "From: SmartLancer <no-reply@smartlancer.com>";

            // Envoyer email
            mail($email, $subject, $message, $headers);
        }
    }

    header("Location: index.php?route=backoffice");
    exit();
}

public function viewBan() {
    if (!isset($_GET['token'])) {
        echo "Token invalide";
        return;
    }

    $model = new User();
    $user = $model->getUserByToken($_GET['token']);

    include __DIR__ . '/../view/backoffice/view_ban.php';
}


public function index() {
    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
        header("Location: index.php?route=login");
        exit();
    }

    $model = new User();

    // Récupérer les paramètres GET pour recherche/filtre/tri
    $search = $_GET['search'] ?? '';
    $roleFilter = $_GET['role'] ?? '';
    $sortBy = $_GET['sort'] ?? 'id';
    $order = $_GET['order'] ?? 'ASC';

    // Obtenir les utilisateurs filtrés
    $users = $model->getFilteredUsers($search, $roleFilter, $sortBy, $order);

    include __DIR__ . '/../view/backoffice/dashboard.php';
}





}
