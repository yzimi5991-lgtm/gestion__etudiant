# 🎓 TP – Application web « Gestion des étudiants »

Application web développée en **PHP procédural**, respectant fidèlement le cahier des charges du TP ainsi que l'ensemble des exercices et extensions bonus.

---

## 📁 Arborescence du projet

```
gestion_etudiants/
├── data/
│   └── etudiants.txt      # Stockage texte : id;nom;prenom;filiere;note;photo;email
├── uploads/               # Dossier pour les photos de profil téléversées
├── test.php               # Exercice 1 : Test de configuration (phpinfo)
├── functions.php          # Exercice 2 & 3 : Fonctions communes, constantes, sessions, lecture/écriture
├── header.php             # Exercice 2 : En-tête HTML commun avec barre de navigation & gestion du thème
├── footer.php             # Exercice 2 : Pied de page commun avec date dynamique
├── index.php              # Exercice 4 & 5 : Formulaire de connexion & mémorisation du dernier login
├── logout.php             # Exercice 5 : Déconnexion et destruction de la session
├── liste.php              # Exercice 6 & 7 : Tableau des étudiants, statistiques, recherche & tri
├── ajouter.php            # Exercice 8, 9 & 10 : Formulaire d'ajout et de modification avec upload photo
├── supprimer.php          # Exercice 10 : Suppression d'un étudiant et de sa photo
├── export.php             # Bonus : Export complet de la liste au format CSV
├── style.css              # Exercice 12 : Feuille de style responsive avec mode clair / sombre
└── script.js              # Exercice 11 : Confirmation de suppression, recherche instantanée, switch thème
```

---

## 🚀 Comment lancer et tester l'application

### Option 1 : Avec le serveur interne de PHP (Recommandé)
1. Ouvrez un terminal dans le dossier du projet :
   ```bash
   cd c:\Users\Ultrapc\Desktop\php\gestion_etudiants
   ```
2. Lancez le serveur local intégré :
   ```bash
   php -S localhost:8000
   ```
3. Ouvrez votre navigateur sur : [http://localhost:8000/index.php](http://localhost:8000/index.php)

*(Ou testez la configuration PHP sur [http://localhost:8000/test.php](http://localhost:8000/test.php))*

### Option 2 : Avec XAMPP / WampServer / Laragon
1. Copiez le dossier `gestion_etudiants` dans le répertoire web de votre serveur (`htdocs` pour XAMPP, `www` pour WAMP).
2. Démarrez Apache.
3. Accédez à [http://localhost/gestion_etudiants/](http://localhost/gestion_etudiants/).

---

## 🔑 Identifiants de connexion (Constante `UTILISATEURS`)

| Identifiant (Login) | Mot de passe | Rôle |
|---|---|---|
| `admin` | `admin123` | Administrateur |
| `prof` | `prof123` | Enseignant |
| `etudiant` | `etu123` | Étudiant |

---

## 📋 Récapitulatif des exercices réalisés

### ✅ Exercice 1 – Environnement et arborescence (1 pt)
- Arborescence `gestion_etudiants/` avec sous-dossiers `data/` et `uploads/`.
- Fichier `test.php` avec `phpinfo()`.
- Création de l'ensemble des fichiers requis et du fichier `data/etudiants.txt`.

### ✅ Exercice 2 – Fichiers communs (2 pt)
- `functions.php` :
  - Constantes `FICHIER` (avec `__DIR__`), `FILIERES` (`GI`, `IDIA`, `GE`, `GC`), et `UTILISATEURS`.
  - Démarrage automatique de session avec `session_start()`.
  - Fonction `h(string $s): string` sécurisant l'affichage via `htmlspecialchars`.
  - Fonctions `connecte(): bool` et `exiger_connexion(): void` avec redirection vers `index.php`.
- `header.php` : Titre variable `$titre`, navigation affichée uniquement si connecté.
- `footer.php` : Pied de page avec affichage dynamique de la date avec `date()`.

### ✅ Exercice 3 – Lecture et écriture du fichier texte (2 pt)
- Format de stockage : `id;nom;prenom;filiere;note;photo;email`.
- `lire_etudiants(): array` : lecture via `file()`, découpage avec `explode()`, retour d'un tableau associatif indexé par l'ID.
- `sauver_etudiants(array $liste): void` : reconstruction avec `implode()` et écriture via `file_put_contents()`.
- `nettoyer(string $s): string` : suppression des espaces superflus (`trim`), points-virgules et retours chariots (`str_replace`).

### ✅ Exercice 4 – Page de connexion (2 pt)
- Formulaire POST avec `login` et `mdp`.
- Vérification dans la constante `UTILISATEURS`.
- Si correct : enregistrement de `$_SESSION['login']` et redirection vers `liste.php`.
- Si incorrect : message d'erreur et formulaire conservé.
- Redirection directe vers `liste.php` si déjà connecté.
- Utilisation de `isset()`, `??` et `$_SERVER['REQUEST_METHOD']`.

### ✅ Exercice 5 – Cookie « dernier login » et déconnexion (1 pt)
- Création du cookie `dernier_user` valable 7 jours (`setcookie`) lors d'une connexion réussie.
- Pré-remplissage du champ login via `$_COOKIE['dernier_user'] ?? ''`.
- `logout.php` : suppression des variables `$_SESSION`, destruction par `session_destroy()`, et redirection vers `index.php`.

### ✅ Exercice 6 – Affichage de la liste (2 pt)
- Protection par `exiger_connexion()`.
- Colonnes : Photo, Nom (en majuscules avec `strtoupper`), Prénom (première lettre majuscule avec `ucfirst`), Filière, Note, Email, Actions.
- Note colorée en vert si $\ge 10$, en rouge sinon via l'opérateur ternaire `($note >= 10) ? 'note-reussie' : 'note-echec'`.
- Ligne « Aucun étudiant » si la liste est vide.
- Message flash de session affiché puis supprimé avec `unset($_SESSION['flash'])`.

### ✅ Exercice 7 – Recherche, tri et statistiques (3 pt)
- Recherche GET `q` : filtrage par nom ou prénom avec `stripos()` et `array_filter()`.
- Tri GET `tri` : par ordre d'ajout (`id`), par `nom`, par `filiere`, ou par `note` décroissante via `usort()`, `strcasecmp()` et l'opérateur combiné `<=>`.
- Statistiques au-dessus du tableau :
  - Nombre d'étudiants affichés (`count()`).
  - Moyenne des notes sur 20 formatée à 2 décimales avec `number_format()`.
  - Meilleure note avec `max()`.
  - Gestion de la liste vide pour éviter la division par zéro.
- Mémorisation des critères du formulaire après soumission.

### ✅ Exercice 8 – Formulaire d'ajout avec validation (2 pt)
- Formulaire POST avec champs `nom`, `prenom`, `filiere` (liste déroulante générée depuis `FILIERES`), `note`, `email`.
- Validation côté serveur : non-vide, validité de la filière avec `in_array()`, note numérique valide entre 0 et 20 avec `is_numeric()`.
- Maintien des saisies en cas d'erreurs et affichage de la liste des erreurs.
- Calcul automatique de l'ID (`max + 1`).
- Sauvegarde et message flash de confirmation.

### ✅ Exercice 9 – Envoi de la photo de profil (1 pt)
- Formulaire `enctype="multipart/form-data"` avec champ `photo`.
- Contrôle du code d'erreur `$_FILES['photo']['error']`, de l'extension (`jpg`, `jpeg`, `png` via `pathinfo`) et de la taille maximale (1 Mo).
- Renommage unique via `uniqid('etu_')` et déplacement dans `uploads/` via `move_uploaded_file()`.
- Affichage de la photo en miniature ronde dans le tableau.

### ✅ Exercice 10 – Modification et suppression (2 pt)
- **Modification** : `ajouter.php?id=...` pré-remplit le formulaire. En cas d'envoi d'une nouvelle photo, l'ancienne est supprimée du disque via `unlink()`.
- **Suppression** : `supprimer.php?id=...` supprime l'étudiant avec `unset()`, supprime sa photo du disque si elle existe (`file_exists`, `unlink`), réécrit le fichier et redirige avec un message flash.
- Conversion systématique de l'ID en entier avec `(int)`.

### ✅ Exercice 11 – Partie JavaScript (1 pt)
- `script.js` :
  - Boîte de dialogue de confirmation `confirm()` avant toute suppression.
  - Recherche instantanée en direct : filtrage dynamique des lignes du tableau dès la saisie dans le champ recherche sans recharger la page (`addEventListener('input')`).

### ✅ Exercice 12 – Mise en forme CSS (1 pt)
- `style.css` :
  - Design moderne, épuré et responsive.
  - En-tête coloré avec menu de navigation.
  - Tableaux soignés avec survol et badges de filières.
  - Formulaires disposés dans des cartes soignées (`.card`).
  - Messages de succès (vert) et d'erreur (rouge).
  - Photos rondes (`border-radius: 50%`) avec avatar textuel de secours si pas de photo.

---

## 🌟 Extensions Bonus Réalisées (0 pt)
1. **Champ Email** : Validé avec `filter_var(..., FILTER_VALIDATE_EMAIL)` et affiché dans le tableau avec lien `mailto:`.
2. **Thème Clair / Sombre** : Cookie `theme` appliqué à toutes les pages et commutateur instantané dans la barre de navigation.
3. **Moyennes par filière** : Tableau associatif calculant et affichant la moyenne des notes pour chaque filière existante.
4. **Anti-doublon** : Refus de l'ajout d'un étudiant si un couple `(nom + prénom)` identique existe déjà.
5. **Export CSV (`export.php`)** : Téléchargement dynamique avec en-têtes HTTP `Content-Type: text/csv` et `Content-Disposition`.
