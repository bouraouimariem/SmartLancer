document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('propositionForm1').addEventListener('submit', function(event) {
        let valid = true;

        const commentaire = document.getElementById('commentaire_propo').value.trim();
        const montantPropo = parseFloat(document.getElementById('montant_propo').value);
        const delaiEstime = parseInt(document.getElementById('delai_estime_propo').value);

        const commentaireError = document.getElementById('commentaireError');
        const montantError = document.getElementById('montantError');
        const delaiError = document.getElementById('delaiError');

        commentaireError.textContent = '';
        montantError.textContent = '';
        delaiError.textContent = '';

        if (commentaire.length <= 15) {
            commentaireError.textContent = 'Le commentaire doit contenir plus de 15 caractères.';
            valid = false;
        }

        if (isNaN(montantPropo) || montantPropo <= 0) {
            montantError.textContent = 'Veuillez entrer un montant valide supérieur à 0.';
            valid = false;
        }

        if (isNaN(delaiEstime) || delaiEstime <= 0) {
            delaiError.textContent = 'Veuillez entrer un délai estimé valide supérieur à 0.';
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});



document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('propositionForm2').addEventListener('submit', function(event) {
        let valid = true;

        const commentaire = document.getElementById('commentaire_propo_modif').value.trim();
        const montantPropo = parseFloat(document.getElementById('montant_propo_modif').value);
        const delaiEstime = parseInt(document.getElementById('delai_estime_propo_modif').value);

        const commentaireError = document.getElementById('commentaire_modifERROR');
        const montantError = document.getElementById('montant_modifERROR');
        const delaiError = document.getElementById('delai_estime_modifERROR');

        commentaireError.textContent = '';
        montantError.textContent = '';
        delaiError.textContent = '';

        if (commentaire.length <= 15) {
            commentaireError.textContent = 'Le commentaire doit contenir plus de 15 caractères.';
            valid = false;
        }

        if (isNaN(montantPropo) || montantPropo <= 0) {
            montantError.textContent = 'Veuillez entrer un montant valide supérieur à 0.';
            valid = false;
        }

        if (isNaN(delaiEstime) || delaiEstime <= 0) {
            delaiError.textContent = 'Veuillez entrer un délai estimé valide supérieur à 0.';
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});



document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('createProjectForm').addEventListener('submit', function(event) {
        let valid = true;

        const nomPub = document.getElementById('nom_pub').value.trim();
        const description = document.getElementById('description1').value.trim();
        const budget = parseFloat(document.getElementById('budget1').value);
        const delai = parseInt(document.getElementById('delai1').value);
        const categories = document.querySelectorAll('input[name="categories[]"]:checked');

        const nomError = document.getElementById('nomError');
        const descriptionError = document.getElementById('descriptionError');
        const budgetError = document.getElementById('budgetError');
        const delaiError = document.getElementById('delaiError');
        const categoriesError = document.getElementById('categoriesError');

        nomError.textContent = '';
        descriptionError.textContent = '';
        budgetError.textContent = '';
        delaiError.textContent = '';
        categoriesError.textContent = '';

        if (nomPub.length === 0) {
            nomError.textContent = 'Le nom du projet est obligatoire.';
            valid = false;
        }

        if (description.length < 10) {
            descriptionError.textContent = 'La description doit contenir au moins 10 caractères.';
            valid = false;
        }

        if (isNaN(budget) || budget <= 0) {
            budgetError.textContent = 'Veuillez entrer un budget valide supérieur à 0.';
            valid = false;
        }

        if (isNaN(delai) || delai <= 0) {
            delaiError.textContent = 'Veuillez entrer un délai estimé valide supérieur à 0.';
            valid = false;
        }

        if (categories.length === 0) {
            categoriesError.textContent = 'Veuillez sélectionner au moins une catégorie.';
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('clientform_edit').addEventListener('submit', function(event) {
        let valid = true;

        const nomPub = document.getElementById('nom_pub_modif').value.trim();
        const description = document.getElementById('description_modif').value.trim();
        const budget = parseFloat(document.getElementById('budget_modif').value);
        const delai = parseInt(document.getElementById('delai_modif').value);

        const nomError = document.getElementById('nom_pub_modifERROR');
        const descriptionError = document.getElementById('description_modifERROR');
        const budgetError = document.getElementById('budget_modifERROR');
        const delaiError = document.getElementById('delai_modifERROR');

        nomError.textContent = '';
        descriptionError.textContent = '';
        budgetError.textContent = '';
        delaiError.textContent = '';
        categoriesError.textContent = '';

        if (nomPub.length === 0) {
            nomError.textContent = 'Le nom du projet est obligatoire.';
            valid = false;
        }

        if (description.length < 10) {
            descriptionError.textContent = 'La description doit contenir au moins 10 caractères.';
            valid = false;
        }

        if (isNaN(budget) || budget <= 0) {
            budgetError.textContent = 'Veuillez entrer un budget valide supérieur à 0.';
            valid = false;
        }

        if (isNaN(delai) || delai <= 0) {
            delaiError.textContent = 'Veuillez entrer un délai estimé valide supérieur à 0.';
            valid = false;
        }

        if (categories.length === 0) {
            categoriesError.textContent = 'Veuillez sélectionner au moins une catégorie.';
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});

