// view/register.js
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registerForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    const conditions = document.getElementById('conditions').checked;

    if (name.length < 3) {
      alert('Le nom doit contenir au moins 3 caractères.');
      e.preventDefault(); return;
    }
    if (!email.includes('@') || !email.includes('.')) {
      alert('Email invalide.');
      e.preventDefault(); return;
    }
    if (password.length < 6) {
      alert('Le mot de passe doit contenir au moins 6 caractères.');
      e.preventDefault(); return;
    }
    if (!['Client', 'Freelancer', 'Admin'].includes(role)) {
      alert('Veuillez choisir un rôle.');
      e.preventDefault(); return;
    }
    if (!conditions) {
      alert('Vous devez accepter les conditions.');
      e.preventDefault(); return;
    }
  });
});
