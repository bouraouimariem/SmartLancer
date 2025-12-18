<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailVerificationController {

    /**
     * Envoyer l'email de vérification
     */
    public function sendVerificationEmail($email, $name) {
        
        // Générer un token de vérification unique
        $token = bin2hex(random_bytes(32));
        
        // Sauvegarder le token en base de données
        $userModel = new User();
        $userModel->storeVerificationToken($email, $token);
        
        // Créer le lien de vérification
        $verificationLink = "http://localhost/project/index.php?route=verify_email&token=$token";
        
        // ====== ENVOI EMAIL AVEC PHPMAILER ======
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjemaaichraf7@gmail.com';
            $mail->Password   = 'cacn gqua xyki lqoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinataire et expéditeur
            $mail->setFrom('benjemaaichraf7@gmail.com', 'SmartLancer');
            $mail->addAddress($email);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Vérifiez votre adresse email - SmartLancer';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #15803d 0%, #166534 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>🎉 Bienvenue sur SmartLancer !</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <h2 style='color: #333;'>Bonjour $name,</h2>
                        <p style='color: #666; line-height: 1.6;'>
                            Merci de vous être inscrit sur SmartLancer ! Pour finaliser votre inscription 
                            et accéder à toutes les fonctionnalités, veuillez vérifier votre adresse email.
                        </p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$verificationLink' 
                               style='background: linear-gradient(135deg, #15803d 0%, #166534 100%); 
                                      color: white; 
                                      padding: 15px 30px; 
                                      text-decoration: none; 
                                      border-radius: 8px; 
                                      display: inline-block;
                                      font-weight: bold;'>
                                ✅ Vérifier mon email
                            </a>
                        </div>
                        
                        <p style='color: #666; font-size: 14px;'>
                            Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
                        </p>
                        <p style='background-color: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 5px; word-break: break-all; font-size: 12px;'>
                            $verificationLink
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='color: #999; font-size: 12px;'>
                            Si vous n'avez pas créé de compte sur SmartLancer, ignorez cet email.
                        </p>
                    </div>
                    
                    <div style='background-color: #333; padding: 20px; text-align: center;'>
                        <p style='color: #999; margin: 0; font-size: 12px;'>
                            © 2025 SmartLancer - Tous droits réservés
                        </p>
                    </div>
                </div>
            ";

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email de vérification: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Vérifier le token et activer le compte
     */
    public function verifyEmail() {
        session_start(); // Ajout de session_start()
        
        if (empty($_GET['token'])) {
            $_SESSION['error'] = "Token de vérification manquant";
            header("Location: index.php?route=login");
            exit();
        }
        
        $token = trim($_GET['token']);
        
        $userModel = new User();
        $result = $userModel->verifyEmailToken($token);
        
        if ($result) {
            // Marquer l'email comme vérifié
            $userModel->markEmailAsVerified($token);
            
            $_SESSION['success'] = "✅ Email vérifié avec succès ! Vous pouvez maintenant vous connecter.";
            header("Location: index.php?route=login");
            exit();
        } else {
            $_SESSION['error'] = "Token de vérification invalide ou expiré";
            header("Location: index.php?route=login");
            exit();
        }
    }
    
    /**
     * Renvoyer l'email de vérification
     */
    public function resendVerificationEmail() {
        session_start(); // Ajout de session_start()
        
        if (empty($_POST['email'])) {
            $_SESSION['error'] = "Email requis";
            header("Location: index.php?route=login");
            exit();
        }
        
        $email = trim($_POST['email']);
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            $_SESSION['error'] = "Email introuvable";
            header("Location: index.php?route=login");
            exit();
        }
        
        // Vérifier si l'email est déjà vérifié
        if ($user['email_verified'] == 1) {
            $_SESSION['success'] = "Votre email est déjà vérifié";
            header("Location: index.php?route=login");
            exit();
        }
        
        // Renvoyer l'email
        $this->sendVerificationEmail($email, $user['name']);
        
        $_SESSION['success'] = "📧 Email de vérification renvoyé ! Vérifiez votre boîte de réception.";
        header("Location: index.php?route=login");
        exit();
    }

    // ========================================
    // 🆕 EMAILS POUR BAN/UNBAN/DELETE
    // ========================================

    /**
     * Envoyer un email de notification de bannissement
     */
    public function sendBanNotification($email, $name, $reason = null) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjemaaichraf7@gmail.com';
            $mail->Password   = 'cacn gqua xyki lqoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('benjemaaichraf7@gmail.com', 'SmartLancer Admin');
            $mail->addAddress($email);

            $reasonText = $reason ? "<p style='color: #666; line-height: 1.6;'><strong>Raison :</strong> $reason</p>" : "";

            $mail->isHTML(true);
            $mail->Subject = '⛔ Votre compte SmartLancer a été suspendu';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>⛔ Compte Suspendu</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <h2 style='color: #333;'>Bonjour $name,</h2>
                        <p style='color: #666; line-height: 1.6;'>
                            Nous vous informons que votre compte SmartLancer a été temporairement suspendu 
                            par notre équipe d'administration.
                        </p>
                        
                        $reasonText
                        
                        <div style='background-color: #fee2e2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0;'>
                            <p style='color: #991b1b; margin: 0;'>
                                <strong>⚠️ Conséquences :</strong><br>
                                Vous ne pouvez plus accéder à votre compte jusqu'à nouvel ordre.
                            </p>
                        </div>
                        
                        <p style='color: #666; line-height: 1.6;'>
                            Si vous pensez qu'il s'agit d'une erreur ou souhaitez faire appel de cette décision, 
                            veuillez contacter notre support à <strong>benjemaaichraf7@gmail.com</strong>.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='color: #999; font-size: 12px;'>
                            Cette action a été effectuée par un administrateur SmartLancer.
                        </p>
                    </div>
                    
                    <div style='background-color: #333; padding: 20px; text-align: center;'>
                        <p style='color: #999; margin: 0; font-size: 12px;'>
                            © 2025 SmartLancer - Tous droits réservés
                        </p>
                    </div>
                </div>
            ";

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email de bannissement: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Envoyer un email de notification de débannissement
     */
    public function sendUnbanNotification($email, $name) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjemaaichraf7@gmail.com';
            $mail->Password   = 'cacn gqua xyki lqoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('benjemaaichraf7@gmail.com', 'SmartLancer Admin');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = '✅ Votre compte SmartLancer a été réactivé';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #15803d 0%, #166534 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>✅ Compte Réactivé</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <h2 style='color: #333;'>Bonjour $name,</h2>
                        <p style='color: #666; line-height: 1.6;'>
                            Bonne nouvelle ! Votre compte SmartLancer a été réactivé par notre équipe d'administration.
                        </p>
                        
                        <div style='background-color: #d1fae5; padding: 15px; border-left: 4px solid #15803d; margin: 20px 0;'>
                            <p style='color: #065f46; margin: 0;'>
                                <strong>🎉 Vous pouvez maintenant :</strong><br>
                                • Vous reconnecter à votre compte<br>
                                • Accéder à toutes les fonctionnalités<br>
                                • Reprendre vos activités normalement
                            </p>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/project/index.php?route=login' 
                               style='background: linear-gradient(135deg, #15803d 0%, #166534 100%); 
                                      color: white; 
                                      padding: 15px 30px; 
                                      text-decoration: none; 
                                      border-radius: 8px; 
                                      display: inline-block;
                                      font-weight: bold;'>
                                🔓 Se connecter maintenant
                            </a>
                        </div>
                        
                        <p style='color: #666; line-height: 1.6;'>
                            Nous vous rappelons de respecter nos conditions d'utilisation pour éviter toute nouvelle suspension.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='color: #999; font-size: 12px;'>
                            Si vous avez des questions, contactez-nous à benjemaaichraf7@gmail.com
                        </p>
                    </div>
                    
                    <div style='background-color: #333; padding: 20px; text-align: center;'>
                        <p style='color: #999; margin: 0; font-size: 12px;'>
                            © 2025 SmartLancer - Tous droits réservés
                        </p>
                    </div>
                </div>
            ";

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email de débannissement: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Envoyer un email de notification de suppression de compte
     */
    public function sendAccountDeletionNotification($email, $name) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjemaaichraf7@gmail.com';
            $mail->Password   = 'cacn gqua xyki lqoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('benjemaaichraf7@gmail.com', 'SmartLancer Admin');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = '🗑️ Votre compte SmartLancer a été supprimé';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #7c2d12 0%, #581c87 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>🗑️ Compte Supprimé</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <h2 style='color: #333;'>Bonjour $name,</h2>
                        <p style='color: #666; line-height: 1.6;'>
                            Nous vous informons que votre compte SmartLancer a été définitivement supprimé 
                            par notre équipe d'administration.
                        </p>
                        
                        <div style='background-color: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0;'>
                            <p style='color: #78350f; margin: 0;'>
                                <strong>⚠️ Important :</strong><br>
                                • Toutes vos données ont été supprimées<br>
                                • Vous ne pouvez plus accéder à votre compte<br>
                                • Cette action est irréversible
                            </p>
                        </div>
                        
                        <p style='color: #666; line-height: 1.6;'>
                            Si vous souhaitez revenir sur SmartLancer, vous devrez créer un nouveau compte.
                        </p>
                        
                        <p style='color: #666; line-height: 1.6;'>
                            Pour toute question ou contestation, contactez notre support à 
                            <strong>benjemaaichraf7@gmail.com</strong>.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='color: #999; font-size: 12px;'>
                            Merci d'avoir utilisé SmartLancer.
                        </p>
                    </div>
                    
                    <div style='background-color: #333; padding: 20px; text-align: center;'>
                        <p style='color: #999; margin: 0; font-size: 12px;'>
                            © 2025 SmartLancer - Tous droits réservés
                        </p>
                    </div>
                </div>
            ";

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email de suppression: " . $mail->ErrorInfo);
            return false;
        }
    }
}