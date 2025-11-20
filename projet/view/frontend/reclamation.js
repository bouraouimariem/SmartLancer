const form = document.querySelector("form");
const inputs = form.querySelectorAll("input, textarea");

form.addEventListener("submit", function (e) {
    let isValid = true;

    inputs.forEach(input => {
        const errorSpan = input.nextElementSibling;
        const value = input.value.trim();

        // Réinitialisation
        input.classList.remove("input-success", "input-error");
        errorSpan.textContent = "";

        // Validation
        if (input.name === "nom") {
            const reg = /^[A-Za-zÀ-ÖØ-öø-ÿ\s-]{3,}$/;
            if (!reg.test(value)) {
                setError(input, errorSpan, "Nom invalide (min 3 lettres)");
                isValid = false;
            } else setSuccess(input, errorSpan);
        }

        if (input.name === "email") {
            const reg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!reg.test(value)) {
                setError(input, errorSpan, "Email invalide");
                isValid = false;
            } else setSuccess(input, errorSpan);
        }

        if (input.name === "sujet") {
            if (value.length < 3) {
                setError(input, errorSpan, "Sujet trop court");
                isValid = false;
            } else setSuccess(input, errorSpan);
        }

        if (input.name === "message") {
            if (value.length < 10) {
                setError(input, errorSpan, "Message trop court");
                isValid = false;
            } else setSuccess(input, errorSpan);
        }
    });

    if (!isValid) e.preventDefault();
});

function setError(input, span, msg) {
    input.classList.add("input-error");
    span.textContent = msg;
}

function setSuccess(input, span) {
    input.classList.add("input-success");
    span.textContent = "Correct ✔";
    span.style.color = "green";
}
