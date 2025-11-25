<?php
require_once __DIR__ . '/../model/User.php';

class ResetPasswordController {

    // 1) Envoie un lien par email
    public function sendResetLink() {

        if (empty($_POST['email'])) {
            header("Location: index.php?route=forgot_password&error=Email requis");
            exit();
        }

        $email = $_POST['email'];

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            header("Location: index.php?route=forgot_password&error=Email introuvable");
            exit();
        }

        // Générer un token sécurisé
        $token = bin2hex(random_bytes(32));

        // Stocker dans la base
        $userModel->storeResetToken($email, $token);

        // Lien de réinitialisation
        $resetLink = "http://localhost/project/index.php?route=reset_password&token=$token";

        // ENVOI EMAIL (simple pour le moment)
        echo "<div style='padding:20px; background:#d1fae5; border:1px solid #059669; color:#065f46; 
     font-size:16px; margin:20px;'>
        <strong>Lien de réinitialisation :</strong><br>
        <a href='$resetLink'>$resetLink</a>
      </div>";
exit;


        header("Location: index.php?route=forgot_password&success=Lien envoyé !");
    }

    // 2) Mise à jour du mot de passe
    public function updatePassword() {

        if (empty($_POST['token']) || empty($_POST['password'])) {
            header("Location: index.php?route=reset_password&error=Champs requis");
            exit();
        }

        $token = $_POST['token'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $userModel = new User();
        $email = $userModel->verifyToken($token);

        if (!$email) {
            header("Location: index.php?route=reset_password&error=Token invalide");
            exit();
        }

        // Mise à jour
        $userModel->updatePassword($email, $password);

        header("Location: index.php?route=login&success=Mot de passe mis à jour !");
    }
}
