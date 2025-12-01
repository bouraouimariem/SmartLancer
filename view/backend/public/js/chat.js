document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chatInput');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const emojiBtns = document.querySelectorAll('.emoji-btn');
    const messagesList = document.getElementById('messages-list');

    // Ajouter emoji
    emojiBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            input.value += btn.textContent;
            input.focus();
        });
    });

    // Aperçu fichier/image
    fileInput.addEventListener('change', function() {
        preview.innerHTML = '';
        if(fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            if(file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'preview-image';
                preview.appendChild(img);
            } else {
                const div = document.createElement('div');
                div.textContent = '📄 ' + file.name;
                div.className = 'preview-file';
                preview.appendChild(div);
            }
        }
    });

    // Scroll automatique en bas
    if(messagesList){
        messagesList.scrollTop = messagesList.scrollHeight;
    }
});
