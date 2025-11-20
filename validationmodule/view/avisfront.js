// avisfront.js
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".form-avis");

    form.addEventListener("submit", function(e) {
        let valid = true;

        // Sélection des champs
        const nom = document.getElementById("nom");
        const email = document.getElementById("email");
        const note = document.querySelector('input[name="note"]:checked');
        const avis = document.getElementById("avis");

        // Reset messages d'erreur
        document.getElementById("nom_error").textContent = "";
        document.getElementById("email_error").textContent = "";
        document.getElementById("note_error").textContent = "";
        document.getElementById("avis_error").textContent = "";

        // Validation Nom
        if(nom.value.trim() === "") {
            document.getElementById("nom_error").textContent = "Le nom est obligatoire.";
            valid = false;
        }

        // Validation Email simple
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(email.value.trim() === "") {
            document.getElementById("email_error").textContent = "L'email est obligatoire.";
            valid = false;
        } else if(!emailPattern.test(email.value.trim())) {
            document.getElementById("email_error").textContent = "Email invalide.";
            valid = false;
        }

        // Validation Note
        if(!note) {
            document.getElementById("note_error").textContent = "Veuillez sélectionner une note.";
            valid = false;
        }

        // Validation Avis
        if(avis.value.trim() === "") {
            document.getElementById("avis_error").textContent = "Le champ avis est obligatoire.";
            valid = false;
        } else if(avis.value.trim().length < 10) {
            document.getElementById("avis_error").textContent = "L'avis doit contenir au moins 10 caractères.";
            valid = false;
        }

        // Si formulaire invalide, on empêche l'envoi
        if(!valid) {
            e.preventDefault();
        }
    });
});
