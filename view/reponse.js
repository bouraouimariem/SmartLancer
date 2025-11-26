document.addEventListener('DOMContentLoaded', function() {
    const modal = createModal();
    document.body.appendChild(modal.container);

    // Open add modal
    document.addEventListener('click', function(e) {
        const openBtn = e.target.closest('.open-reponse-btn');
        if (openBtn) {
            const avisId = openBtn.getAttribute('data-avis-id');
            openModal({ avis_id: avisId });
        }

        const editBtn = e.target.closest('.edit-reponse');
        if (editBtn) {
            const id = editBtn.getAttribute('data-id');
            const item = document.querySelector('.reponse-item[data-id="' + id + '"]');
            if (!item) return;
            openModal({
                id: id,
                avis_id: item.getAttribute('data-avis-id'),
                nom: item.getAttribute('data-nom'),
                email: item.getAttribute('data-email'),
                contenu: item.getAttribute('data-contenu')
            });
        }

        const delBtn = e.target.closest('.delete-reponse');
        if (delBtn) {
            const id = delBtn.getAttribute('data-id');
            if (!confirm('Supprimer cette réponse ?')) return;
            // ask for email to authorize deletion
            const email = prompt('Pour confirmer, entrez l\'email utilisé pour publier la réponse :');
            if (email === null) return; // cancelled
            const emailTrim = (email || '').trim();
            if (!validateEmail(emailTrim)) { alert('Email invalide.'); return; }

            fetch('/validationmodule/controller/reponsecontroller.php', {
                method: 'POST',
                body: new URLSearchParams({ action: 'delete', id: id, email: emailTrim })
            }).then(r => r.json()).then(json => {
                if (json.success) {
                    const el = document.querySelector('.reponse-item[data-id="' + id + '"]');
                    if (el) el.remove();
                } else {
                    alert(json.message || 'Erreur');
                }
            }).catch(() => alert('Erreur réseau'));
        }
    });

    // Modal handlers
    function createModal() {
        const container = document.createElement('div');
        container.id = 'reponse-modal';
        container.style.display = 'none';
        container.style.position = 'fixed';
        container.style.left = '0';
        container.style.top = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.background = 'rgba(0,0,0,0.5)';
        container.style.alignItems = 'center';
        container.style.justifyContent = 'center';
        container.style.zIndex = '9999';

        const box = document.createElement('div');
        box.style.width = '520px';
        box.style.maxWidth = '95%';
        box.style.background = '#fff';
        box.style.borderRadius = '10px';
        box.style.padding = '18px';
        box.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';

        box.innerHTML = `
            <h3 id="modal-title">Ajouter une réponse</h3>
            <form id="reponse-form" style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                <input type="hidden" name="id" id="resp-id">
                <input type="hidden" name="avis_id" id="resp-avis-id">
                <input type="text" name="nom" id="resp-nom" placeholder="Votre nom" style="padding:8px; border-radius:8px; border:1px solid #ddd;">
                <input type="text" name="email" id="resp-email" placeholder="Votre email" style="padding:8px; border-radius:8px; border:1px solid #ddd;">
                <textarea name="contenu" id="resp-contenu" placeholder="Votre réponse" style="padding:8px; border-radius:8px; border:1px solid #ddd; height:120px;"></textarea>
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" id="resp-cancel" class="btn-action">Annuler</button>
                    <button type="submit" id="resp-save" class="btn-action">Enregistrer</button>
                </div>
            </form>
        `;

        container.appendChild(box);

        // events
        container.addEventListener('click', function(e) {
            if (e.target === container) closeModal();
        });

        box.querySelector('#resp-cancel').addEventListener('click', closeModal);

        box.querySelector('#reponse-form').addEventListener('submit', function(e) {
            e.preventDefault();
            saveReponse();
        });

        return { container, box };
    }

    function openModal(data) {
        const container = document.getElementById('reponse-modal');
        const idEl = container.querySelector('#resp-id');
        const avisEl = container.querySelector('#resp-avis-id');
        const nomEl = container.querySelector('#resp-nom');
        const emailEl = container.querySelector('#resp-email');
        const contenuEl = container.querySelector('#resp-contenu');
        const title = container.querySelector('#modal-title');

        idEl.value = data.id || '';
        avisEl.value = data.avis_id || '';
        nomEl.value = data.nom || '';
        emailEl.value = data.email || '';
        contenuEl.value = data.contenu || '';
        title.textContent = data.id ? 'Modifier la réponse' : 'Ajouter une réponse';

        container.style.display = 'flex';
    }

    function closeModal() {
        const container = document.getElementById('reponse-modal');
        if (container) container.style.display = 'none';
    }

    function saveReponse() {
        const container = document.getElementById('reponse-modal');
        const id = container.querySelector('#resp-id').value;
        const avis_id = container.querySelector('#resp-avis-id').value;
        const nom = container.querySelector('#resp-nom').value.trim();
        const email = container.querySelector('#resp-email').value.trim();
        const contenu = container.querySelector('#resp-contenu').value.trim();

        // Client-side validation (pure JS, no HTML5 validation)
        if (!avis_id) { alert('Avis invalide.'); return; }
        if (!nom || nom.length < 2) { alert('Le nom doit contenir au moins 2 caractères.'); return; }
        if (!email || !validateEmail(email)) { alert('Email invalide.'); return; }
        if (!contenu || contenu.length < 3) { alert('Le contenu doit contenir au moins 3 caractères.'); return; }

        const action = id ? 'edit' : 'add';
        const params = new URLSearchParams({ action: action, avis_id: avis_id, nom: nom, email: email, contenu: contenu });
        if (id) params.append('id', id);

        fetch('/validationmodule/controller/reponsecontroller.php', {
            method: 'POST',
            body: params
        }).then(r => r.json()).then(json => {
            if (!json.success) {
                alert(json.message || 'Erreur');
                return;
            }

            const resp = json.reponse;
            if (action === 'add') {
                appendReponseToDom(resp);
            } else {
                updateReponseInDom(resp);
            }

            closeModal();
        }).catch(() => alert('Erreur réseau'));
    }

    function appendReponseToDom(r) {
        const container = document.getElementById('responses-' + r.avis_id);
        if (!container) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'reponse-item';
        wrapper.setAttribute('data-id', r.id);
        wrapper.setAttribute('data-avis-id', r.avis_id);
        wrapper.setAttribute('data-nom', r.nom);
        wrapper.setAttribute('data-email', r.email);
        wrapper.setAttribute('data-contenu', r.contenu);
        wrapper.style.marginBottom = '10px';
        wrapper.innerHTML = `
            <div>
                <strong>${escapeHtml(r.nom)}</strong>
                <span style="color:#777; font-size:12px; margin-left:8px;">le ${formatDate(r.created_at)}</span>
            </div>
            <p style="margin:6px 0;" class="reponse-contenu">${nl2br(escapeHtml(r.contenu))}</p>
            <div style="display:flex; gap:8px;">
                <button class="btn-action edit-reponse" data-id="${r.id}">Modifier</button>
                <button class="btn-action btn-delete delete-reponse" data-id="${r.id}">Supprimer</button>
            </div>
        `;
        // If list is empty, add container wrapper similar to server rendering
        if (!container.querySelector('.reponse-item')) {
            container.innerHTML = '<div style="padding:10px; border-left:3px solid #eee; background:#fafafa; border-radius:8px;"></div>' + container.innerHTML;
        }
        const inner = container.querySelector('div');
        inner.appendChild(wrapper);
    }

    function updateReponseInDom(r) {
        const el = document.querySelector('.reponse-item[data-id="' + r.id + '"]');
        if (!el) return;
        el.setAttribute('data-nom', r.nom);
        el.setAttribute('data-email', r.email);
        el.setAttribute('data-contenu', r.contenu);
        el.querySelector('strong').textContent = r.nom;
        const p = el.querySelector('.reponse-contenu');
        if (p) p.innerHTML = nl2br(escapeHtml(r.contenu));
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"'`]/g, function (s) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;','`':'&#96;'})[s];
        });
    }

    function nl2br(str) {
        return str.replace(/\r?\n/g, '<br>');
    }

    function formatDate(dtString) {
        // basic formatting fallback
        if (!dtString) return '';
        const d = new Date(dtString);
        if (isNaN(d)) return dtString;
        return d.toLocaleString();
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

});
