document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('rec-form');
    if (!form) return;

    const fields = [
        {
            id: 'nom',
            min: 3,
            pattern: /^[A-Za-zÀ-ÖØ-öø-ÿ\s-]+$/,
            msgLength: "Le nom est trop court (minimum 3 caractères).",
            msgPattern: "Le nom ne doit contenir que des lettres et espaces."
        },
        {
            id: 'email',
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            msgPattern: "Email invalide."
        },
        {
            id: 'sujet',
            min: 3,
            pattern: /^[A-Za-z0-9À-ÖØ-öø-ÿ\s-]+$/,
            msgLength: "Le sujet est trop court (minimum 3 caractères).",
            msgPattern: "Le sujet ne doit pas contenir de caractères spéciaux."
        },
        {
            id: 'message',
            min: 10,
            pattern: /^[A-Za-z0-9À-ÖØ-öø-ÿ\s.,!?'"()-]+$/,
            msgLength: "Le message est trop court (minimum 10 caractères).",
            msgPattern: "Le message contient des caractères spéciaux interdits."
        },
        {
            id: 'telephone',
            min: 6,
            pattern: /^[0-9+ ]+$/,
            msgLength: "Le téléphone est trop court.",
            msgPattern: "Le téléphone ne doit contenir que des chiffres, + et espaces."
        }
    ];

    // Validation formulaire
    form.addEventListener('submit', function (e) {
        let valid = true;
        let firstErrorMsg = '';

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

            if (f.min && val.replace(/\s/g,'').length < f.min) {
                if (!firstErrorMsg) firstErrorMsg = `${f.id}: ${f.msgLength}`;
                if (span) span.textContent = f.msgLength;
                input.classList.add('input-error');
                valid = false;
                return;
            }

            if (f.pattern && !f.pattern.test(val)) {
                if (!firstErrorMsg) firstErrorMsg = `${f.id}: ${f.msgPattern}`;
                if (span) span.textContent = f.msgPattern;
                input.classList.add('input-error');
                valid = false;
                return;
            }

            input.classList.add('input-success');
        });

        // Vérification indicatif + téléphone
        const country = document.getElementById("country");
        const telephone = document.getElementById("telephone");
        if (country && telephone && country.value !== "" && !telephone.value.startsWith(country.value)) {
            e.preventDefault();
            alert("Le numéro de téléphone doit commencer par l'indicatif du pays choisi.");
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // Gestion indicatif pays
    const country = document.getElementById("country");
    const telephone = document.getElementById("telephone");

    if (country && telephone) {
        country.addEventListener("change", function() {
            const code = this.value;
            if (code !== "" && !telephone.value.startsWith(code)) {
                telephone.value = code + " ";
                telephone.focus();
                // Place le curseur après l'indicatif
                telephone.setSelectionRange(telephone.value.length, telephone.value.length);
            }
        });

        // Empêche suppression de l'indicatif
        telephone.addEventListener("keydown", function(e) {
            const code = country.value;
            if (code && this.selectionStart <= code.length) {
                if (["Backspace","Delete"].includes(e.key)) {
                    e.preventDefault();
                }
            }
        });
    }
});
