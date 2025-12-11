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


let selectedTags = [];

document.getElementById("competence").addEventListener("input", function() {
    let query = this.value.trim();
    if (query.length < 1) {
        document.getElementById("suggestions").classList.add("hidden");
        return;
    }

    fetch("/project/api/get_competence.php?q=" + query)
        .then(res => res.json())
        .then(data => {
            let box = document.getElementById("suggestions");
            box.innerHTML = "";
            box.classList.remove("hidden");

            data.forEach(item => {
                let div = document.createElement("div");
                div.className = "px-3 py-2 hover:bg-gray-200 cursor-pointer";
                div.textContent = item;

                div.onclick = function() {
                    addTag(item);
                };

                box.appendChild(div);
            });
        });
});

function addTag(text) {
    if (selectedTags.includes(text)) return;

    selectedTags.push(text);

    let tag = document.createElement("span");
    tag.className = "bg-green-600 text-white px-3 py-1 rounded-full flex items-center gap-2";
    tag.textContent = text;

    let close = document.createElement("span");
    close.textContent = "×";
    close.className = "cursor-pointer font-bold";
    close.onclick = function() {
        tag.remove();
        selectedTags = selectedTags.filter(t => t !== text);
        document.getElementById("competence_tags").value = selectedTags.join(",");
    };

    tag.appendChild(close);
    document.getElementById("tags").appendChild(tag);

    document.getElementById("competence_tags").value = selectedTags.join(",");
    document.getElementById("competence").value = "";
    document.getElementById("suggestions").innerHTML = "";
    document.getElementById("suggestions").classList.add("hidden");
}



