// Déterminer le chemin de base de l'application (GLOBAL)
const baseUrl = window.location.pathname.includes('/validationmodule/') 
    ? '/validationmodule'
    : '';
const controllerUrl = baseUrl + '/controller/reponsecontroller.php';

console.log('Base URL:', baseUrl);
console.log('Controller URL:', controllerUrl);

document.addEventListener('DOMContentLoaded', function() {
    const modal = createModal();
    document.body.appendChild(modal.container);

    // Store the current file being uploaded for preview
    let currentUploadedFile = null;

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
                contenu: item.getAttribute('data-contenu'),
                role: item.getAttribute('data-role'),
                piece: item.getAttribute('data-piece'),
                niveau: item.getAttribute('data-niveau')
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
                    if (el) {
                        el.style.animation = 'slideOutRight 0.3s ease-out forwards';
                        setTimeout(() => el.remove(), 300);
                    }
                    showNotification('🗑️ Réponse supprimée avec succès !', 'success');
                } else {
                    showNotification(json.message || '❌ Erreur lors de la suppression', 'error');
                }
            }).catch(() => showNotification('❌ Erreur réseau', 'error'));
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
        container.style.overflowY = 'auto';
        container.style.backdropFilter = 'blur(2px)';

        const box = document.createElement('div');
        box.style.width = '580px';
        box.style.maxWidth = '95%';
        box.style.background = '#fff';
        box.style.borderRadius = '12px';
        box.style.padding = '30px';
        box.style.boxShadow = '0 20px 60px rgba(0,0,0,0.3)';
        box.style.margin = '20px auto';
        box.style.borderTop = '4px solid #075e3a';

        box.innerHTML = `
            <h2 id="modal-title" style="margin-top:0; margin-bottom:20px; color:#075e3a; font-size:22px;">Ajouter une réponse</h2>
            <form id="reponse-form" style="display:flex; flex-direction:column; gap:0;">
                <input type="hidden" name="id" id="resp-id">
                <input type="hidden" name="avis_id" id="resp-avis-id">
                <input type="hidden" name="visible" id="resp-visible" value="1">
                <input type="hidden" name="type" id="resp-type" value="freelance">
                <input type="hidden" name="is_online" id="resp-is-online" value="0">

                <!-- Section: Identité -->
                <div class="form-group">
                    <label for="resp-nom">Votre nom <span style="color:#dc3545;">*</span></label>
                    <input type="text" name="nom" id="resp-nom" placeholder="Entrez votre nom" style="padding:11px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px;">
                </div>

                <div class="form-group">
                    <label for="resp-email">Votre email <span class="optional-label">(optionnel)</span></label>
                    <input type="text" name="email" id="resp-email" placeholder="votre.email@exemple.com" style="padding:11px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px;">
                </div>

                <!-- Section: Rôle -->
                <div class="form-group">
                    <label for="resp-role">Rôle du répondeur <span style="color:#dc3545;">*</span></label>
                    <select name="role_repondeur" id="resp-role" style="padding:11px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">-- Sélectionnez un rôle --</option>
                        <option value="freelancer">Freelance</option>
                        <option value="admin">Admin</option>
                        <option value="support">Support</option>
                    </select>
                </div>

                <!-- Section: Réponse -->
                <div class="form-group">
                    <label for="resp-contenu">Votre réponse <span style="color:#dc3545;">*</span></label>
                    <textarea name="contenu" id="resp-contenu" placeholder="Écrivez votre réponse ici..." style="padding:11px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px; min-height:120px;"></textarea>
                </div>

                <!-- Section: Pièce jointe -->
                <div class="form-group">
                    <label for="resp-fichier">Pièce jointe <span class="optional-label">(optionnel)</span></label>
                    <input type="file" name="piece_jointe" id="resp-fichier" style="padding:8px; border:1.5px solid #ddd; border-radius:8px;">
                    <small style="color:#666; margin-top:4px;">📎 Images, PDF ou DOCX • Max 2MB</small>
                    <div id="resp-file-preview" style="display:none; margin-top:10px; padding:12px 14px; background:linear-gradient(135deg,#fef3e2 0%,#fde8cc 100%); border-left:4px solid #ffc107; border-radius:6px; align-items:center; gap:10px; font-size:13px; font-weight:500; box-shadow: 0 2px 6px rgba(255,193,7,0.15);"></div>
                </div>

                <!-- Section: Visibilité -->
                <div class="form-group">
                    <label>Visibilité <span style="color:#dc3545;">*</span></label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="visibilite" id="resp-public" value="1" checked> 
                            <span>🌐 Public</span>
                        </label>
                        <label>
                            <input type="radio" name="visibilite" id="resp-private" value="0"> 
                            <span>🔒 Privé</span>
                        </label>
                    </div>
                    <small style="color:#666; margin-top:6px;">Public: visible à tous • Privé: seulement pour l'auteur de l'avis</small>
                </div>

                <!-- Section: Catégorie -->
                <div class="form-group">
                    <label for="resp-categorie">Catégorie <span class="optional-label">(optionnel)</span></label>
                    <select name="categorie" id="resp-categorie" style="padding:11px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <option value="remerciement">✅ Remerciement</option>
                        <option value="justification">📝 Justification</option>
                        <option value="amelioration">💡 Amélioration proposée</option>
                        <option value="autre">❓ Autre</option>
                    </select>
                </div>

                <!-- Notification automatic; option removed from UI -->

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="button" id="resp-cancel" class="btn btn-secondary">Annuler</button>
                    <button type="submit" id="resp-save" class="btn btn-primary">✓ Enregistrer</button>
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

        // Notification is now automatic server-side; UI checkbox removed

        // File preview
        box.querySelector('#resp-fichier').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = box.querySelector('#resp-file-preview');
            if (file) {
                currentUploadedFile = file;
                const fileExt = file.name.split('.').pop().toUpperCase();
                let fileIcon = '📎';
                if (fileExt === 'PDF') fileIcon = '📄';
                else if (['JPG', 'JPEG', 'PNG', 'GIF'].includes(fileExt)) fileIcon = '🖼️';
                else if (['DOC', 'DOCX'].includes(fileExt)) fileIcon = '📝';
                
                preview.style.display = 'flex';
                preview.innerHTML = `
                    <span style="font-size:18px;">${fileIcon}</span>
                    <div style="flex:1;">
                        <div style="font-weight:600; color:#333;">✓ ${escapeHtml(file.name)}</div>
                        <div style="color:#666; font-size:12px;">${(file.size / 1024).toFixed(1)} KB</div>
                    </div>
                    <span style="background:#ffc107; color:#333; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600;">${fileExt}</span>
                `;
            } else {
                currentUploadedFile = null;
                preview.style.display = 'none';
            }
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
        const roleEl = container.querySelector('#resp-role');
        const title = container.querySelector('#modal-title');
        const visibiliteRadios = container.querySelectorAll('input[name="visibilite"]');
        const categorieEl = container.querySelector('#resp-categorie');
        const fileInput = container.querySelector('#resp-fichier');
        const filePreview = container.querySelector('#resp-file-preview');

        idEl.value = data.id || '';
        avisEl.value = data.avis_id || '';
        nomEl.value = data.nom || '';
        emailEl.value = data.email || '';
        contenuEl.value = data.contenu || '';
        
        // Handle visibility (old data.visible or new data.visibilite)
        const visibilite = typeof data.visibilite !== 'undefined' ? data.visibilite : (data.visible ? 1 : 0);
        visibiliteRadios.forEach(r => r.checked = false);
        container.querySelector('input[name="visibilite"][value="' + visibilite + '"]').checked = true;

        if (roleEl) roleEl.value = data.role_repondeur || data.role || '';
        if (categorieEl) categorieEl.value = data.categorie || '';
        // notifier removed from UI; notification handled server-side
        
        // Reset file input for new responses
        if (!data.id) {
            fileInput.value = '';
            filePreview.style.display = 'none';
            currentUploadedFile = null;
        }

        const visibleEl = container.querySelector('#resp-visible');
        const typeEl = container.querySelector('#resp-type');
        const isOnlineEl = container.querySelector('#resp-is-online');
        if (visibleEl) visibleEl.value = visibilite;
        if (typeEl) typeEl.value = data.type || 'freelance';
        if (isOnlineEl) isOnlineEl.value = (typeof data.is_online !== 'undefined') ? data.is_online : '0';
        
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
        const role_repondeur = container.querySelector('#resp-role').value.trim();
        const visibilite = container.querySelector('input[name="visibilite"]:checked').value;
        const categorie = container.querySelector('#resp-categorie').value.trim();
        // notifier removed from UI; notifications are sent automatically by the server
        const fileInput = container.querySelector('#resp-fichier');
        const visible = visibilite ? '1' : '0';
        const type = 'freelance';
        const is_online = '0';

        // Client-side validation
        if (!avis_id) { alert('Avis invalide.'); return; }
        if (!nom || nom.length < 2) { alert('Le nom doit contenir au moins 2 caractères.'); return; }
        if (email && !validateEmail(email)) { alert('Email invalide.'); return; }
        if (!contenu || contenu.length < 3) { alert('La réponse doit contenir au moins 3 caractères.'); return; }
        if (!role_repondeur) { alert('Veuillez sélectionner un rôle.'); return; }

        // File validation if present
        if (fileInput.files[0]) {
            const file = fileInput.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) { alert('Le fichier dépasse 2MB.'); return; }
            if (!allowedMimes.includes(file.type)) { alert('Type de fichier non autorisé. Utilisez: JPG, PNG, GIF, PDF ou DOCX.'); return; }
        }

        const action = id ? 'edit' : 'add';
        const formData = new FormData();
        formData.append('action', action);
        formData.append('avis_id', avis_id);
        formData.append('nom', nom);
        formData.append('email', email);
        formData.append('contenu', contenu);
        formData.append('visible', visible);
        formData.append('type', type);
        formData.append('role_repondeur', role_repondeur);
        formData.append('is_online', is_online);
        formData.append('categorie', categorie);
        
        if (id) formData.append('id', id);
        
        // Add file if present
        if (fileInput.files[0]) {
            formData.append('piece_jointe', fileInput.files[0]);
        }

        const fetchOptions = { method: 'POST', body: formData };

        console.log('Envoi à:', controllerUrl);
        console.log('Action:', action);
        console.log('Données:', { avis_id, nom, email, contenu, visible, type, role_repondeur, categorie });
        console.log('Fichier sélectionné:', fileInput.files[0] ? fileInput.files[0].name : 'AUCUN');
        
        // Log FormData contents
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(`FormData[${key}]:`, `File: ${value.name} (${value.size} bytes, type: ${value.type})`);
            } else {
                console.log(`FormData[${key}]:`, value);
            }
        }

        fetch(controllerUrl, fetchOptions)
            .then(r => {
                console.log('Réponse HTTP:', r.status, r.statusText);
                return r.text();
            })
            .then(text => {
                console.log('Texte reçu:', text);
                try {
                    const json = JSON.parse(text);
                    console.log('JSON reçu:', json);
                    
                    if (!json.success) {
                        alert(json.message || 'Erreur');
                        return;
                    }

                    const resp = json.reponse;
                    if (action === 'add') {
                        appendReponseToDom(resp);
                        showNotification('✅ Réponse ajoutée avec succès !', 'success');
                    } else {
                        updateReponseInDom(resp);
                        showNotification('✏️ Réponse modifiée avec succès !', 'success');
                    }

                    closeModal();
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    alert('Erreur: Réponse serveur invalide. Vérifiez la console.');
                }
            })
            .catch(err => {
                console.error('Erreur fetch:', err);
                console.error('URL était:', controllerUrl);
                alert('Erreur réseau: ' + err.message);
            });
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
        wrapper.setAttribute('data-visible', typeof r.visible !== 'undefined' ? r.visible : '1');
        wrapper.setAttribute('data-type', typeof r.type !== 'undefined' ? r.type : 'freelance');
        wrapper.setAttribute('data-role', typeof r.role_repondeur !== 'undefined' ? r.role_repondeur : '');
        wrapper.setAttribute('data-piece', r.piece_jointe || '');
        wrapper.setAttribute('data-categorie', r.categorie || '');
        wrapper.setAttribute('data-is-online', typeof r.is_online !== 'undefined' ? r.is_online : '0');
        wrapper.style.marginBottom = '10px';
        
        let extraHTML = '';
        
        // Type badge
        if (r.type) {
            extraHTML += `<div style="margin-top:6px"><strong>Type:</strong> <span style="background:#cce5ff;padding:4px 8px;border-radius:4px;color:#004085">${escapeHtml(r.type)}</span></div>`;
        }

        // Visibilité badge
        const visibiliteClass = r.visible ? 'visibilite-public' : 'visibilite-private';
        const visibiliteText = r.visible ? '🌐 Public' : '🔒 Privé';
        extraHTML += `<div class="visibilite-badge ${visibiliteClass}">${visibiliteText}</div>`;

        // Catégorie
        if (r.categorie) {
            extraHTML += `<div style="margin-top:6px"><strong>Catégorie:</strong> <span style="background:#e7f3ff;padding:4px 8px;border-radius:4px;color:#0056b3">${escapeHtml(r.categorie)}</span></div>`;
        }

        // Pièce jointe avec meilleure présentation
        let attachmentHTML = '';
        if (r.piece_jointe) {
            const fileName = r.piece_jointe.split('/').pop();
            const fileExt = fileName.split('.').pop().toUpperCase();
            let fileIcon = '📎';
            if (fileExt === 'PDF') fileIcon = '📄';
            else if (['JPG', 'JPEG', 'PNG', 'GIF'].includes(fileExt)) fileIcon = '🖼️';
            else if (['DOC', 'DOCX'].includes(fileExt)) fileIcon = '📝';
            
            const fileUrl = baseUrl + '/' + r.piece_jointe;
            
            attachmentHTML = `
            <div style="margin-top:10px; padding:10px 12px; background:linear-gradient(135deg,#fef3e2 0%,#fde8cc 100%); border-left:4px solid #ffc107; border-radius:6px; display:flex; align-items:center; gap:10px; box-shadow: 0 2px 6px rgba(255,193,7,0.15);">
                <span style="font-size:20px;">${fileIcon}</span>
                <div style="flex:1;">
                    <div style="font-weight:600; color:#333; font-size:13px;">📎 Pièce jointe</div>
                    <a href="${escapeHtml(fileUrl)}" target="_blank" download style="color:#0056b3; text-decoration:none; font-size:12px; display:inline-flex; align-items:center; gap:4px; transition:all 0.3s ease; font-weight:500;">
                        ${escapeHtml(fileName)} 
                        <span style="transition:transform 0.3s ease;">⬇️</span>
                    </a>
                </div>
                <span style="background:#ffc107; color:#333; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600;">${fileExt}</span>
            </div>`;
        }

        wrapper.innerHTML = `
            <div>
                <strong>${escapeHtml(r.nom)}</strong>
                <span style="color:#777; font-size:12px; margin-left:8px;">le ${formatDate(r.created_at)}</span>
            </div>
            <p style="margin:6px 0;" class="reponse-contenu">${nl2br(escapeHtml(r.contenu))}</p>
            ${attachmentHTML}
            ${extraHTML}
            <div style="display:flex; gap:8px; margin-top:8px;">
                <button class="btn secondary edit-reponse" data-id="${r.id}">Modifier</button>
                <button class="btn secondary delete-reponse" data-id="${r.id}">Supprimer</button>
            </div>
        `;
        
        container.appendChild(wrapper);
    }

    function updateReponseInDom(r) {
        const el = document.querySelector('.reponse-item[data-id="' + r.id + '"]');
        if (!el) return;
        
        // Reconstruire complètement l'élément avec les nouvelles données
        el.setAttribute('data-nom', r.nom);
        el.setAttribute('data-email', r.email);
        el.setAttribute('data-contenu', r.contenu);
        if (r.piece_jointe !== undefined) el.setAttribute('data-piece', r.piece_jointe || '');
        if (r.role_repondeur !== undefined) el.setAttribute('data-role', r.role_repondeur || '');
        if (r.visible !== undefined) el.setAttribute('data-visible', r.visible ? '1' : '0');
        if (r.type !== undefined) el.setAttribute('data-type', r.type || 'freelance');
        if (r.categorie !== undefined) el.setAttribute('data-categorie', r.categorie || '');
        if (r.is_online !== undefined) el.setAttribute('data-is-online', r.is_online || '0');
        
        // Reconstruire le contenu HTML
        let extraHTML = '';
        
        // Type badge
        if (r.type) {
            extraHTML += `<div style="margin-top:6px"><strong>Type:</strong> <span style="background:#cce5ff;padding:4px 8px;border-radius:4px;color:#004085">${escapeHtml(r.type)}</span></div>`;
        }

        // Visibilité badge
        const visibiliteClass = r.visible ? 'visibilite-public' : 'visibilite-private';
        const visibiliteText = r.visible ? '🌐 Public' : '🔒 Privé';
        extraHTML += `<div class="visibilite-badge ${visibiliteClass}">${visibiliteText}</div>`;

        // Catégorie
        if (r.categorie) {
            extraHTML += `<div style="margin-top:6px"><strong>Catégorie:</strong> <span style="background:#e7f3ff;padding:4px 8px;border-radius:4px;color:#0056b3">${escapeHtml(r.categorie)}</span></div>`;
        }

        // Pièce jointe
        let attachmentHTML = '';
        if (r.piece_jointe) {
            const fileName = r.piece_jointe.split('/').pop();
            const fileExt = fileName.split('.').pop().toUpperCase();
            let fileIcon = '📎';
            if (fileExt === 'PDF') fileIcon = '📄';
            else if (['JPG', 'JPEG', 'PNG', 'GIF'].includes(fileExt)) fileIcon = '🖼️';
            else if (['DOC', 'DOCX'].includes(fileExt)) fileIcon = '📝';
            
            const fileUrl = baseUrl + '/' + r.piece_jointe;
            
            attachmentHTML = `
            <div style="margin-top:10px; padding:10px 12px; background:linear-gradient(135deg,#fef3e2 0%,#fde8cc 100%); border-left:4px solid #ffc107; border-radius:6px; display:flex; align-items:center; gap:10px; box-shadow: 0 2px 6px rgba(255,193,7,0.15);">
                <span style="font-size:20px;">${fileIcon}</span>
                <div style="flex:1;">
                    <div style="font-weight:600; color:#333; font-size:13px;">📎 Pièce jointe</div>
                    <a href="${escapeHtml(fileUrl)}" target="_blank" download style="color:#0056b3; text-decoration:none; font-size:12px; display:inline-flex; align-items:center; gap:4px; transition:all 0.3s ease; font-weight:500;">
                        ${escapeHtml(fileName)} 
                        <span style="transition:transform 0.3s ease;">⬇️</span>
                    </a>
                </div>
                <span style="background:#ffc107; color:#333; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600;">${fileExt}</span>
            </div>`;
        }
        
        // Reconstruire le HTML complet
        el.innerHTML = `
            <div>
                <strong>${escapeHtml(r.nom)}</strong>
                <span style="color:#777; font-size:12px; margin-left:8px;">le ${formatDate(r.created_at)}</span>
            </div>
            <p style="margin:6px 0;" class="reponse-contenu">${nl2br(escapeHtml(r.contenu))}</p>
            ${attachmentHTML}
            ${extraHTML}
            <div style="display:flex; gap:8px; margin-top:8px;">
                <button class="btn secondary edit-reponse" data-id="${r.id}">Modifier</button>
                <button class="btn secondary delete-reponse" data-id="${r.id}">Supprimer</button>
            </div>
        `;
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"'`]/g, function (s) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;','`':'&#96;'})[s];
        });
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        
        let bgGradient = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        let duration = 4000;
        
        if (type === 'error') {
            bgGradient = 'linear-gradient(135deg, #dc3545 0%, #e74c3c 100%)';
        } else if (type === 'info') {
            bgGradient = 'linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%)';
            duration = 5000;
        } else if (type === 'warning') {
            bgGradient = 'linear-gradient(135deg, #ffc107 0%, #ff9800 100%)';
            duration = 4500;
        }
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgGradient};
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            font-weight: 600;
            font-size: 14px;
            z-index: 99999;
            animation: slideInRight 0.4s ease-out;
            max-width: 400px;
            word-wrap: break-word;
        `;

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(400px) rotateY(10deg);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) rotateY(0);
                }
            }
            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0) rotateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(400px) rotateY(10deg);
                }
            }
        `;
        
        if (!document.querySelector('style[data-notification-styles]')) {
            style.setAttribute('data-notification-styles', 'true');
            document.head.appendChild(style);
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // Auto-remove after duration
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.4s ease-out forwards';
            setTimeout(() => notification.remove(), 400);
        }, duration);
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
