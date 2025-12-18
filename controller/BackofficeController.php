<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/EmailVerificationController.php';

class BackofficeController {

    public function deleteUser() {
        
        if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?route=login");
            exit();
        }

        if (isset($_POST['id'])) {
            $model = new User();
            
            // 🔥 RÉCUPÉRER LES INFOS AVANT LA SUPPRESSION
            $user = $model->getUserById($_POST['id']);
            
            if ($user) {
                // 🔥 ENVOYER L'EMAIL DE NOTIFICATION
                $emailController = new EmailVerificationController();
                $emailController->sendAccountDeletionNotification($user['email'], $user['name']);
                
                // Supprimer l'utilisateur
                $model->deleteUser($_POST['id']);
            }
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

            // Récupérer les infos de l'utilisateur AVANT le ban
            $user = $model->getUserById($_POST['id']);
            $email = $user['email'];
            $name = $user['name'];
            $userId = $user['id'];

            // Mettre à jour le statut (ban / unban)
            $model->setBanStatus($userId, $_POST['status']);

            // 🔥 ENVOI EMAIL AVEC PHPMAILER
            $emailController = new EmailVerificationController();

            // Si statut = 1 => BANNI
            if ($_POST['status'] == 1) {

                // Génération d'un token pour la page de détails
                $token = bin2hex(random_bytes(32));
                $model->saveBanToken($userId, $token);

                // 🔥 ENVOYER L'EMAIL DE BANNISSEMENT
                $reason = "Violation des conditions d'utilisation ou comportement inapproprié";
                $emailController->sendBanNotification($email, $name, $reason);

            } else {
                // Si statut = 0 => DÉBANNI
                
                // 🔥 ENVOYER L'EMAIL DE DÉBANNISSEMENT
                $emailController->sendUnbanNotification($email, $name);
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