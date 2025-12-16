

<br />
<div align="center">
  <a href="https://github.com/bouraouimariem/SmartLancer">
    <img src="reclamation/assets/logo.png" alt="SmarLancer Logo" width="120" height="60">
  </a>
  <h3 align="center">SmarLancer</h3>
  <p align="center">
    Une plateforme web pour connecter les clients et les freelances facilement.
  </p>
</div>



## Table des matières
- [À propos du projet](#à-propos-du-projet)
- [Technologies utilisées](#technologies-utilisées)
- [Installation](#installation)
- [Modules](#modules)
- [Utilisation](#utilisation)
- [Contributions](#contributions)
- [Licence](#licence)
- [Contact](#contact)



## À propos du projet

**SmarLancer** est une plateforme web permettant de **mettre en relation les clients et les freelances**.  
Elle est développée avec **HTML, CSS, JavaScript et PHP**, et utilise **MySQL** via **XAMPP** pour la gestion de la base de données.

Cette plateforme offre une interface simple et intuitive pour :  

- Créer et gérer les profils utilisateurs  
- Publier et gérer des projets  
- Envoyer et recevoir des propositions pour les projets  
- Gérer les réclamations et les réponses  
- Ajouter des commentaires et des avis sur les projets  



## Technologies utilisées

- [HTML5](https://developer.mozilla.org/fr/docs/Web/HTML)  
- [CSS3](https://developer.mozilla.org/fr/docs/Web/CSS)  
- [JavaScript](https://developer.mozilla.org/fr/docs/Web/JavaScript)  
- [PHP](https://www.php.net/)  
- [MySQL](https://www.mysql.com/)  
- [XAMPP](https://www.apachefriends.org/index.html)  



## Installation

Pour démarrer le projet **SmarLancer** sur votre machine :  

### Prérequis

- Installer [XAMPP](https://www.apachefriends.org/index.html)  
- Avoir un navigateur moderne  
- VS Code pour éditer le code  

### Étapes

1. **Cloner le dépôt :**  
   ```bash
   git clone https://github.com/bouraouimariem/SmartLancer.git
````

2. **Copier les fichiers** dans le dossier `htdocs` de XAMPP
   Exemple : `C:\xampp\htdocs\SmarLancer`
3. **Créer la base de données MySQL** via phpMyAdmin :

   ```sql
   CREATE DATABASE smarlancer;
   ```
4. **Configurer la connexion à la base de données** (`config.php`) :

   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "smarlancer";

   $conn = new mysqli($servername, $username, $password, $dbname);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```
5. **Démarrer Apache et MySQL** via le XAMPP Control Panel
6. **Ouvrir le projet dans le navigateur** :

   ```
   http://localhost/SmarLancer/
   ```



## Modules

1. **Gestion des utilisateurs et profils**

   * Inscription et connexion sécurisées
   * Gestion et modification du profil
   * Gestion des mots de passe

2. **Gestion des projets et propositions**

   * Création et publication des projets
   * Consultation des projets disponibles
   * Envoi et réception de propositions

3. **Gestion des réclamations et réponses**

   * Soumission et traitement des réclamations
   * Réponses et suivi par les administrateurs

4. **Gestion des commentaires et avis**

   * Ajouter des avis et commentaires sur les projets
   * Historique des avis et notation



## Utilisation

* Créez un compte et complétez votre profil
* Publiez un projet ou parcourez les projets existants
* Envoyez et recevez des propositions
* Gérez vos réclamations et ajoutez vos commentaires

### Exemples d’images

<div align="center">
    <img src="reclamation/assets/home2.png" alt="Page d'accueil" width="400">
    <br/><br/>
    <img src="reclamation/assets/home.png" alt="Page d'accueil" width="400">
    <br/><br/>
    <img src="reclamation/assets/login1.png" alt="Page de connexion" width="400">
    <br/><br/>
    <img src="reclamation/assets/login2.png" alt="Page de connexion" width="400">
</div>



## Contributions

Les contributions sont les bienvenues !

1. Fork le projet
2. Créez une branche pour votre fonctionnalité :

   ```bash
   git checkout -b feature/NouvelleFonctionnalite
   ```
3. Committez vos modifications :

   ```bash
   git commit -m "Ajout de la fonctionnalité X"
   ```
4. Poussez la branche sur GitHub :

   ```bash
   git push origin feature/NouvelleFonctionnalite
   ```
5. Ouvrez une Pull Request



## Licence

Distribué sous la licence MIT. Voir `LICENSE.txt` pour plus de détails.



## Contact

* Mariem Bouraoui : [mariembouraoui2024@gmail.com](mailto:mariembouraoui2024@gmail.com)
* Maha Azaiz : [bnslimene.raslene15@gmail.com](mailto:bnslimene.raslene15@gmail.com)
*  Ben Slimene Raslene : [bnslimene.raslene15@gmail.com](mailto:b@gmail.com)
*  Ichraf ben jemaa : [bnslimene.raslene15@gmail.com](mailto:@gmail.com)

Project Link : [https://github.com/bouraouimariem/SmartLancer](https://github.com/bouraouimariem/SmartLancer)

