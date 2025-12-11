function valider_publication() {

    // Récupération des champs
    let nom = document.getElementById("nom_pub").value;
    let desc = document.getElementById("description").value;
    let budget = document.getElementById("budget").value;
    let delai = document.getElementById("delai").value;

    // Cases à cocher (catégories)
    let web = document.getElementById("cat_web").checked;
    let mobile = document.getElementById("cat_mobile").checked;
    let design = document.getElementById("cat_design").checked;
    let marketing = document.getElementById("cat_marketing").checked;
    let ai = document.getElementById("cat_ai").checked;

    // 1. Vérification du titre/nom
    if (nom.length < 3) {
        alert("Veuillez vérifier le nom du projet !");
        return false;
    }

    // 2. Vérification description
    if (desc.length < 10) {
        alert("La description doit contenir au moins 10 caractères !");
        return false;
    }

    // 3. Budget : non vide + positif
    if (budget === "" || budget <= 0) {
        alert("Veuillez saisir un budget valide !");
        return false;
    }

    // 4. Délai : non vide + positif
    if (delai === "" || delai <= 0) {
        alert("Veuillez saisir un délai valide !");
        return false;
    }

    // 5. Vérifier au moins UNE catégorie cochée
    if (!web && !mobile && !design && !marketing && !ai) {
        alert("Veuillez choisir au moins une catégorie !");
        return false;
    }

    return true;
}
