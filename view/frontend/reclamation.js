// reclamation.js
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('rec-form');
    if (!form) return; // sécurité si pas de formulaire sur la page

    const fields = [
        {id: 'nom', min: 3, pattern: /^[A-Za-zÀ-ÖØ-öø-ÿ\s-]+$/},
        {id: 'email', pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/},
        {id: 'sujet', min: 3, pattern: /^[A-Za-z0-9À-ÖØ-öø-ÿ\s-]+$/},
        {id: 'message', min: 10, pattern: /^[A-Za-z0-9À-ÖØ-öø-ÿ\s.,!?'"()-]+$/}
    ];

    form.addEventListener('submit', function (e) {
        let valid = true;
        let firstErrorMsg = ''; // stocke le premier message d'erreur pour l'alerte

        fields.forEach(f => {
            const input = document.getElementById(f.id);
            if (!input) return;

            const span = input.nextElementSibling;
            const val = input.value.trim();

            if (span) span.textContent = '';
            input.classList.remove('input-error', 'input-success');

            if (!val) {
                const msg = 'Ce champ est obligatoire';
                if (!firstErrorMsg) firstErrorMsg = `${f.id}: ${msg}`;
                if (span) span.textContent = msg;
                input.classList.add('input-error');
                valid = false;
                return;
            }

            if (f.pattern && !f.pattern.test(val)) {
                const msg = 'Format invalide';
                if (!firstErrorMsg) firstErrorMsg = `${f.id}: ${msg}`;
                if (span) span.textContent = msg;
                input.classList.add('input-error');
                valid = false;
                return;
            }

            if (f.min && val.length < f.min) {
                const msg = `Trop court (min ${f.min} caractères)`;
                if (!firstErrorMsg) firstErrorMsg = `${f.id}: ${msg}`;
                if (span) span.textContent = msg;
                input.classList.add('input-error');
                valid = false;
                return;
            }

            input.classList.add('input-success');
        });

        if (!valid) {
            e.preventDefault();
            if (firstErrorMsg) alert(`Erreur de saisie :\n${firstErrorMsg}`);
        } else {
            // Vider le formulaire après un léger délai pour laisser le serveur traiter
            setTimeout(() => {
                fields.forEach(f => {
                    const input = document.getElementById(f.id);
                    if (!input) return;
                    input.value = '';
                    const span = input.nextElementSibling;
                    if (span) span.textContent = '';
                    input.classList.remove('input-error', 'input-success');
                });
                const actionInput = document.getElementById('form-action');
                if (actionInput) actionInput.value = 'add';
            }, 50);
        }
    });
});
