<?php

class MailController
{
    // Fonction d’envoi d’email (en utilisant mail() avec Mailjet)
    public function sendMail($to, $subject, $message)
    {
        $headers  = "From: " . MJ_SENDER_EMAIL . "\r\n";
        $headers .= "Reply-To: " . MJ_SENDER_EMAIL . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($to, $subject, $message, $headers);
    }

    // Email lors d’une nouvelle proposition
    public function sendMailProposition($propo)
    {
        $to = MJ_SENDER_EMAIL; 
        $subject = "Nouvelle proposition reçue";

        // >>> UTILISATION DES GETTERS CORRECTS <<<
        $message = "
            <h2>Nouvelle proposition</h2>
            <p><strong>Montant :</strong> {$propo->getMontantProp()} DT</p>
            <p><strong>Délai estimé :</strong> {$propo->getDelaiEstime()} jours</p>
        ";

        return $this->sendMail($to, $subject, $message);
    }

    // Email lors de l’acceptation
    public function sendMailAcceptation($propo)
    {
        $to = MJ_SENDER_EMAIL;
        $subject = "Proposition acceptée";

        /*
            ⚠ Ici $propo vient de :
            $propoObj = (object) [
                "montant_propo" => $propo["montant_propo"],
                "delai_estime" => $propo["delai_estime"],
                "nom_pub" => $pub["nom_pub"]
            ];
            Donc ce n’est PAS un model → pas de getters !
        */

        $message = "
            <h2>Votre proposition a été acceptée</h2>
            <p>Publication : <strong>{$propo->nom_pub}</strong></p>
            <p>Montant : {$propo->montant_propo} DT</p>
            <p>Délai : {$propo->delai_estime} jours</p>
        ";

        return $this->sendMail($to, $subject, $message);
    }
}
