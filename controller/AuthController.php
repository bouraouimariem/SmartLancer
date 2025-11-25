<?php
// controller/AuthController.php
require_once __DIR__ . '/../model/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        session_start();
    }

    // handle and display register view
    public function register() {
        // si POST -> traiter l'inscription
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'Client';

            // validations côté serveur (minimum)
            $errors = [];
            if (strlen($name) < 3) $errors[] = "Le nom doit contenir au moins 3 caractères.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
            if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            if (!in_array($role, ['Client', 'Freelancer', 'Admin'])) $errors[] = "Rôle invalide.";

            // vérifier si email existe déjà
            if (empty($errors)) {
                $existing = $this->userModel->findByEmail($email);
                if ($existing) {
                    $errors[] = "Cet email est déjà utilisé.";
                }
            }

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $created = $this->userModel->create($name, $email, $hash, $role);
                if ($created) {
                    // tu peux rediriger vers login ou auto-login
                    $_SESSION['success'] = "Compte créé avec succès. Connectez-vous.";
                    header('Location: index.php?route=login');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du compte.";
                }
            }

            // si erreurs -> afficher view avec $errors
            require __DIR__ . '/../view/frontoffice/register.php';
            return;
        }

        // GET -> afficher form
        require __DIR__ . '/../view/frontoffice/register.php';

    }

    // handle and display login view
    public function login()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        if (empty($password)) $errors[] = "Mot de passe requis.";

        if (empty($errors)) {

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['banned'] == 1) {
    $errors[] = "Votre compte est banni. Contactez l’administrateur.";
    require __DIR__ . '/../view/frontoffice/login.php';
    return;
}


                // SESSION
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // 🎯 AJOUT : Vérifier si freelancer a un portfolio
                if ($user['role'] === 'Freelancer') {

                    require_once __DIR__ . '/../model/Portfolio.php';
                    $portfolioModel = new Portfolio();
                    $portfolio = $portfolioModel->getByUserId($user['id']);


                    if ($portfolio) {
                        // ✔ Déjà un portfolio → Aller à l’espace Freelancer
                        header("Location: view/frontoffice/freelancer/freelancer_welcome.php");
                        exit;
                    } else {
                        // ❌ Pas encore de portfolio → Aller à la création
                        header("Location: view/frontoffice/freelancer/portfolio.php");
                        exit;
                    }
                }

                // ✔ Client
                if ($user['role'] === 'Client') {
                    header("Location: index.php?route=client");
                    exit;
                }

                // ✔ Admin
                if ($user['role'] === 'Admin') {
                    header("Location: index.php?route=admin");
                    exit;
                }
            }

            $errors[] = "Email ou mot de passe incorrect.";
        }

        require __DIR__ . '/../view/frontoffice/login.php';
        return;
    }

    require __DIR__ . '/../view/frontoffice/login.php';

}


public function logout() {
    session_start();
    session_unset();
    session_destroy();

    header("Location: index.php?route=login");
    exit;
}


}
