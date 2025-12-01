// reclamation.js - Validation du formulaire

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("rec-form");
    const fields = {
        nom: {
            element: document.getElementById("nom"),
            validate: (v) => v.length >= 3,
            message: "Le nom doit contenir au moins 3 caractères."
        },
        email: {
            element: document.getElementById("email"),
            validate: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
            message: "Veuillez entrer un email valide."
        },
        sujet: {
            element: document.getElementById("sujet"),
            validate: (v) => v.length >= 3,
            message: "Le sujet doit contenir au moins 3 caractères."
        },
        message: {
            element: document.getElementById("message"),
            validate: (v) => v.length >= 10,
            message: "Le message doit contenir au moins 10 caractères."
        },
        telephone: {
            element: document.getElementById("telephone"),
            validate: (v) => /^\d{8,15}$/.test(v),
            message: "Le numéro doit contenir entre 8 et 15 chiffres."
        }
    };

    function showError(inputEl, msg) {
        const span = inputEl.nextElementSibling;
        span.textContent = msg;
        span.style.color = "red";
    }

    function showSuccess(inputEl) {
        const span = inputEl.nextElementSibling;
        span.textContent = "✓ Champ valide";
        span.style.color = "green";
    }

        // Validation dynamique
    Object.values(fields).forEach(field => {
        field.element.addEventListener("keyup", () => {
            const value = field.element.value.trim();
            if (!field.validate(value)) {
                showError(field.element, field.message);
            } else {
                showSuccess(field.element);
            }
        });
    });

    form.addEventListener("submit", (e) => {
        let isValid = true;

        Object.values(fields).forEach(field => {
            const value = field.element.value.trim();
            if (!field.validate(value)) {
                isValid = false;
                showError(field.element, field.message);
            } else {
                showSuccess(field.element);
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });
});
