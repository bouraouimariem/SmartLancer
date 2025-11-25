document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('portfolioForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const lien = document.getElementById('lien').value.trim();
        const bio = document.getElementById('bio').value.trim();
        const experience = document.getElementById('experience').value.trim();
        const competence = document.getElementById('competence').value.trim();
        const tarif = document.getElementById('tarif').value.trim();
        const photo = document.getElementById('photo').value;

        // Vérif lien portfolio
        if (lien.length === 0 || !lien.startsWith('http')) {
            alert('Veuillez entrer un lien portfolio valide (commençant par http...).');
            e.preventDefault(); return;
        }

        // Vérif bio
        if (bio.length < 10) {
            alert('La bio doit contenir au moins 10 caractères.');
            e.preventDefault(); return;
        }

        // Vérif expérience
        if (experience.length < 10) {
            alert('L’expérience doit contenir au moins 10 caractères.');
            e.preventDefault(); return;
        }

        // Vérif compétence
        if (competence.length === 0) {
            alert('Veuillez entrer au moins une compétence.');
            e.preventDefault(); return;
        }

        // Vérif tarif
        if (tarif === '' || isNaN(tarif) || tarif <= 0) {
            alert('Veuillez entrer un tarif valide et supérieur à 0.');
            e.preventDefault(); return;
        }

        // Vérif image (optionnel mais format requis si choisie)
        if (photo !== '') {
            const allowed = ['jpg', 'jpeg', 'png', 'gif'];
            const ext = photo.split('.').pop().toLowerCase();

            if (!allowed.includes(ext)) {
                alert('La photo doit être au format JPG, PNG ou GIF.');
                e.preventDefault(); return;
            }
        }
    });
});

