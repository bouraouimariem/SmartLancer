<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ResetPasswordController {

    // 1) Envoie un lien par email
    public function sendResetLink() {

        if (empty($_POST['email'])) {
            header("Location: index.php?route=forgot_password&error=Email requis");
            exit();
        }

        $email = trim($_POST['email']); // CORRECTION: récupérer l'email depuis POST
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            header("Location: index.php?route=forgot_password&error=Email introuvable");
            exit();
        }

        // Générer token sécurisé
        $token = bin2hex(random_bytes(32));

        // Sauvegarder token
        $userModel->storeResetToken($email, $token);

        // Lien de reset
        $resetLink = "http://localhost/project/index.php?route=reset_password&token=$token";

        // ====== ENVOI EMAIL AVEC PHPMAILER ======
        $mail = new PHPMailer(true); // CORRECTION: créer l'objet PHPMailer

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjemaaichraf7@gmail.com';
            $mail->Password   = 'cacn gqua xyki lqoq'; // Mot de passe d'application Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinataire et expéditeur
            $mail->setFrom('benjemaaichraf7@gmail.com', 'SmartLancer'); // ✅ CORRIGÉ
            $mail->addAddress($email);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset - SmartLancer';
            $mail->Body    = "
                Hello,<br><br>
                You requested to reset your password.<br>
                Click the link below:<br><br>
                <a href='$resetLink'>$resetLink</a><br><br>
                If you did not request this, ignore this email.
            ";

            $mail->send();
            
            header("Location: index.php?route=forgot_password&success=Un email a été envoyé !");
            exit();
            
        } catch (Exception $e) {
            header("Location: index.php?route=forgot_password&error=Erreur d'envoi: " . urlencode($mail->ErrorInfo));
            exit();
        }
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
        exit();
    }
}
?>