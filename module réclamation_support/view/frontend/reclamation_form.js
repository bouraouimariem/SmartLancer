document.getElementById("myForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let nom = document.getElementById('nom').value.trim();
    let email = document.getElementById('email').value.trim();
    let sujet = document.getElementById('sujet').value.trim();
    let message = document.getElementById('message').value.trim();

    let isValid = true;

    // Regex
    let nameRegex = /^[A-Za-z-\s]+$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function displayMessage(id, message, isError) {
        let element = document.getElementById(id + "_error");
        element.style.color = isError ? "red" : "green";
        element.innerText = message;
    }

    // Vérification nom
    if (nom.length < 3 || !nameRegex.test(nom)) {
        displayMessage("nom", "Le nom doit contenir au moins 3 lettres et uniquement des lettres ou tirets.", true);
        isValid = false;
    } else {
        displayMessage("nom", "Correct", false);
    }

    // Vérification email
    if (!emailRegex.test(email)) {
        displayMessage("email", "Veuillez entrer un email valide.", true);
        isValid = false;
    } else {
        displayMessage("email", "Correct", false);
    }

    // Vérification sujet
    if (sujet.length < 3) {
        displayMessage("sujet", "Le sujet doit contenir au moins 3 caractères.", true);
        isValid = false;
    } else {
        displayMessage("sujet", "Correct", false);
    }

    // Vérification message
    if (message.length < 10) {
        displayMessage("message", "Le message doit contenir au moins 10 caractères.", true);
        isValid = false;
    } else {
        displayMessage("message", "Correct", false);
    }

    // Si tout est valide
    if (isValid) {
        alert("Réclamation envoyée avec succès !");
        document.getElementById("myForm").submit();
    }
});
