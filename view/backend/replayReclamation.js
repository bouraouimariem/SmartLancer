document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const responseField = document.getElementById("response");

    function validateResponse() {
        const text = responseField.value.trim();

        if (text.length === 0) {
            alert("!Veuillez saisir une réponse.");
            return false;
        }

        if (text.length < 3) {
            alert("!La réponse doit contenir au moins 3 caractères.");
            return false;
        }

        return true;
    }

    // Lors de l’envoi du formulaire
    form.addEventListener("submit", function (e) {
        if (!validateResponse()) {
            e.preventDefault(); // Empêche l'envoi
        }
    });

});
