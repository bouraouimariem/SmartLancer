document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".form-avis");
    const nom = document.getElementById("nom");
    const email = document.getElementById("email");
    const noteRadios = document.querySelectorAll('input[name="note"]');
    const avis = document.getElementById("avis");

    const nomError = document.getElementById("nom_error");
    const emailError = document.getElementById("email_error");
    const noteError = document.getElementById("note_error");
    const avisError = document.getElementById("avis_error");

    // --- Fonctions d'affichage des messages ---
    function showError(fieldError, message) {
        fieldError.textContent = message;
        fieldError.classList.add("active");
    }

    function hideError(fieldError) {
        fieldError.textContent = "";
        fieldError.classList.remove("active");
    }

    // --- Validation Nom ---
    function validateNom() {
        const nomPattern = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]{3,}$/; // minimum 3 lettres, accents autorisés
        if(nom.value.trim() === "") {
            showError(nomError, "Le nom est obligatoire.");
            nom.classList.add("error");
            nom.classList.remove("success");
            return false;
        } else if(!nomPattern.test(nom.value.trim())) {
            showError(nomError, "Le nom doit contenir au moins 3 lettres et uniquement des lettres.");
            nom.classList.add("error");
            nom.classList.remove("success");
            return false;
        } else {
            hideError(nomError);
            nom.classList.remove("error");
            nom.classList.add("success");
            return true;
        }
    }

    // --- Validation Email ---
    function validateEmail() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(email.value.trim() === "") {
            showError(emailError, "L'email est obligatoire.");
            email.classList.add("error");
            email.classList.remove("success");
            return false;
        } else if(!emailPattern.test(email.value.trim())) {
            showError(emailError, "Email invalide.");
            email.classList.add("error");
            email.classList.remove("success");
            return false;
        } else {
            hideError(emailError);
            email.classList.remove("error");
            email.classList.add("success");
            return true;
        }
    }

    // --- Validation Note ---
    function validateNote() {
        for(let radio of noteRadios) if(radio.checked){ hideError(noteError); return true; }
        showError(noteError, "Veuillez sélectionner une note (1 à 5).");
        return false;
    }

    // --- Validation Avis ---
    function validateAvis() {
        if(avis.value.trim() === "") {
            showError(avisError, "Le champ avis est obligatoire.");
            avis.classList.add("error");
            avis.classList.remove("success");
            return false;
        } else if(avis.value.trim().length < 10) {
            showError(avisError, "L'avis doit contenir au moins 10 caractères.");
            avis.classList.add("error");
            avis.classList.remove("success");
            return false;
        } else {
            hideError(avisError);
            avis.classList.remove("error");
            avis.classList.add("success");
            return true;
        }
    }

    // --- Validation en temps réel ---
    nom.addEventListener("input", validateNom);
    email.addEventListener("input", validateEmail);
    avis.addEventListener("input", validateAvis);
    noteRadios.forEach(r => r.addEventListener("change", validateNote));

    // --- Validation au submit ---
    form.addEventListener("submit", function(e) {
        const validNom = validateNom();
        const validEmail = validateEmail();
        const validNote = validateNote();
        const validAvis = validateAvis();

        if(!(validNom && validEmail && validNote && validAvis)) {
            e.preventDefault(); // Empêche l'envoi si un champ est invalide
        }
    });
});
