// replyReclamation.js - Validation du formulaire de réponse

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const textarea = document.querySelector("textarea[name='contenu']");

    function showError(msg) {
        let errorDiv = document.getElementById("reply-error");
        if (!errorDiv) {
            errorDiv = document.createElement("p");
            errorDiv.id = "reply-error";
            errorDiv.style.color = "red";
            errorDiv.style.fontWeight = "bold";
            form.prepend(errorDiv);
        }
        errorDiv.textContent = msg;
    }

    function showSuccess() {
        let errorDiv = document.getElementById("reply-error");
        if (!errorDiv) {
            errorDiv = document.createElement("p");
            errorDiv.id = "reply-error";
            form.prepend(errorDiv);
        }
        errorDiv.textContent = "✓ Contenu valide";
        errorDiv.style.color = "green";
    }

    // Compteur de caractères
    const counter = document.createElement('p');
    counter.id = 'char-counter';
    counter.style.fontSize = '14px';
    counter.style.marginTop = '-10px';
    counter.style.marginBottom = '10px';
    textarea.after(counter);

    const submitBtn = form.querySelector("button[type='submit']");
    submitBtn.disabled = true;
    submitBtn.style.opacity = "0.6";
    submitBtn.style.cursor = "not-allowed";

    function updateCounter(value) {
        counter.textContent = `${value.length} / 5 caractères minimum`;
    }

    // Validation dynamique
        textarea.addEventListener("keyup", () => {
        const value = textarea.value.trim();
        updateCounter(value);

        if (value.length < 5) {
            textarea.style.border = "2px solid red";
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.6";
            submitBtn.style.cursor = "not-allowed";
            showError("La réponse doit contenir au moins 5 caractères.");
        } else {
            textarea.style.border = "2px solid green";
            submitBtn.disabled = false;
            submitBtn.style.opacity = "1";
            submitBtn.style.cursor = "pointer";
            showSuccess();
        }
    });("keyup", () => {
        const value = textarea.value.trim();
        if (value.length < 5) {
            showError("La réponse doit contenir au moins 5 caractères.");
        } else {
            showSuccess();
        }
    });

    // Validation au submit
    form.addEventListener("submit", (e) => {
        const value = textarea.value.trim();
        if (value.length < 5) {
            e.preventDefault();
            showError("La réponse doit contenir au moins 5 caractères.");
        } else {
            showSuccess();
        }
    });
});
