<?php
/**
 * Classe de validation robuste côté serveur
 * Toutes les validations doivent être faites côté PHP
 */
class Validator
{
    private static $errors = [];

    /**
     * Valide un nom (minimum 3 caractères, uniquement lettres et espaces)
     */
    public static function validateNom($nom)
    {
        $nom = trim($nom);
        if (empty($nom)) {
            self::$errors['nom'] = 'Le nom est obligatoire.';
            return false;
        }
        if (strlen($nom) < 3) {
            self::$errors['nom'] = 'Le nom doit contenir au moins 3 caractères.';
            return false;
        }
        if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\']{3,}$/u', $nom)) {
            self::$errors['nom'] = 'Le nom doit contenir uniquement des lettres, espaces, tirets ou apostrophes.';
            return false;
        }
        return true;
    }

    /**
     * Valide un email
     */
    public static function validateEmail($email)
    {
        $email = trim($email);
        if (empty($email)) {
            self::$errors['email'] = 'L\'email est obligatoire.';
            return false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::$errors['email'] = 'Veuillez entrer une adresse email valide.';
            return false;
        }
        if (strlen($email) > 255) {
            self::$errors['email'] = 'L\'email est trop long (max 255 caractères).';
            return false;
        }
        return true;
    }

    /**
     * Valide un email optionnel (peut être vide)
     */
    public static function validateEmailOptional($email)
    {
        $email = trim($email);
        if (empty($email)) {
            return true; // optionnel
        }
        return self::validateEmail($email);
    }

    /**
     * Valide une note (1-5)
     */
    public static function validateNote($note)
    {
        $note = (int)$note;
        if ($note < 1 || $note > 5) {
            self::$errors['note'] = 'La note doit être entre 1 et 5.';
            return false;
        }
        return true;
    }

    /**
     * Valide un contenu (minimum 10 caractères, maximum 5000)
     */
    public static function validateContenu($contenu, $minLength = 10, $maxLength = 5000)
    {
        $contenu = trim($contenu);
        if (empty($contenu)) {
            self::$errors['contenu'] = 'Le contenu est obligatoire.';
            return false;
        }
        if (strlen($contenu) < $minLength) {
            self::$errors['contenu'] = "Le contenu doit contenir au moins {$minLength} caractères.";
            return false;
        }
        if (strlen($contenu) > $maxLength) {
            self::$errors['contenu'] = "Le contenu ne peut pas dépasser {$maxLength} caractères.";
            return false;
        }
        return true;
    }

    /**
     * Valide un contenu de réponse (minimum 5 caractères, maximum 5000)
     */
    public static function validateReponseContenu($contenu, $minLength = 5, $maxLength = 5000)
    {
        $contenu = trim($contenu);
        if (empty($contenu)) {
            self::$errors['contenu'] = 'Votre réponse ne peut pas être vide.';
            return false;
        }
        if (strlen($contenu) < $minLength) {
            self::$errors['contenu'] = "La réponse doit contenir au moins {$minLength} caractères.";
            return false;
        }
        if (strlen($contenu) > $maxLength) {
            self::$errors['contenu'] = "La réponse ne peut pas dépasser {$maxLength} caractères.";
            return false;
        }
        return true;
    }

    /**
     * Valide un champ de texte (optionnel)
     */
    public static function validateTextOptional($value, $maxLength = 255)
    {
        $value = trim($value);
        if (empty($value)) {
            return true; // optionnel
        }
        if (strlen($value) > $maxLength) {
            return false;
        }
        return true;
    }

    /**
     * Valide une visibilité (public ou privé)
     */
    public static function validateVisibility($visible)
    {
        $visible = (int)$visible;
        return in_array($visible, [0, 1]);
    }

    /**
     * Valide un rôle
     */
    public static function validateRole($role, $allowedRoles = ['admin', 'client', 'freelancer', 'support'])
    {
        $role = trim($role);
        return in_array($role, $allowedRoles);
    }

    /**
     * Valide un type
     */
    public static function validateType($type, $allowedTypes = ['freelance', 'admin'])
    {
        $type = trim($type);
        return in_array($type, $allowedTypes);
    }

    /**
     * Valide une catégorie
     */
    public static function validateCategorie($categorie)
    {
        $categorie = trim($categorie);
        if (empty($categorie)) {
            return true; // optionnel
        }
        $allowed = ['remerciement', 'justification', 'amelioration', 'autre'];
        return in_array($categorie, $allowed);
    }

    /**
     * Nettoie et retourne un string en sécurité
     */
    public static function sanitize($value)
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valide un fichier uploadé
     */
    public static function validateFile($file, $allowedMimes = [], $maxSize = 2097152)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            self::$errors['file'] = 'Aucun fichier téléchargé.';
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (dépassement INI).',
                UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (dépassement formulaire).',
                UPLOAD_ERR_PARTIAL => 'Fichier téléchargé partiellement.',
                UPLOAD_ERR_NO_FILE => 'Aucun fichier sélectionné.',
                UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque.',
                UPLOAD_ERR_EXTENSION => 'Extension de fichier non autorisée.',
            ];
            self::$errors['file'] = $errors[$file['error']] ?? 'Erreur d\'upload inconnue.';
            return false;
        }

        if ($file['size'] > $maxSize) {
            self::$errors['file'] = 'Fichier trop volumineux (max ' . ($maxSize / 1024 / 1024) . 'MB).';
            return false;
        }

        // Validez le MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!empty($allowedMimes) && !in_array($mime, $allowedMimes, true)) {
            self::$errors['file'] = 'Type de fichier non autorisé: ' . $mime;
            return false;
        }

        return true;
    }

    /**
     * Obtient tous les messages d'erreur
     */
    public static function getErrors()
    {
        return self::$errors;
    }

    /**
     * Obtient un message d'erreur spécifique
     */
    public static function getError($field)
    {
        return self::$errors[$field] ?? null;
    }

    /**
     * Réinitialise les erreurs
     */
    public static function resetErrors()
    {
        self::$errors = [];
    }

    /**
     * Vérifie s'il y a des erreurs
     */
    public static function hasErrors()
    {
        return !empty(self::$errors);
    }
}
