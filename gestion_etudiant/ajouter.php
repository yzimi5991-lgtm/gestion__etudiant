<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edition = $id > 0;

$nom = '';
$prenom = '';
$filiere = 'GI';
$note = '';
$photo_actuelle = '';
$erreurs = [];

if ($edition) {
    if (!isset($etudiants[$id])) {
        $_SESSION['flash'] = "Étudiant introuvable.";
        header('Location: liste.php');
        exit;
    }
    $etudiant = $etudiants[$id];
    $nom = $etudiant['nom'];
    $prenom = $etudiant['prenom'];
    $filiere = $etudiant['filiere'];
    $note = $etudiant['note'];
    $photo_actuelle = $etudiant['photo'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $filiere = nettoyer($_POST['filiere'] ?? '');
    $note_saisie = $_POST['note'] ?? '';

    // Validations
    if ($nom === '') $erreurs[] = "Le nom est obligatoire.";
    if ($prenom === '') $erreurs[] = "Le prénom est obligatoire.";
    if (!in_array($filiere, FILIERES)) $erreurs[] = "Filière invalide.";
    if (!is_numeric($note_saisie) || (float)$note_saisie < 0 || (float)$note_saisie > 20) {
        $erreurs[] = "La note doit être un nombre compris entre 0 et 20.";
    }

    $nom_photo = $photo_actuelle;

    // Gestion de l'upload de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] <= 1024 * 1024) { // 1 Mo max
                $exts_autorisees = ['jpg', 'jpeg', 'png'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $exts_autorisees)) {
                    $nouveau_nom_photo = uniqid('etu_') . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], 'uploads/' . $nouveau_nom_photo)) {
                        // Supprimer l'ancienne photo si elle existe
                        if ($photo_actuelle && file_exists('uploads/' . $photo_actuelle)) {
                            unlink('uploads/' . $photo_actuelle);
                        }
                        $nom_photo = $nouveau_nom_photo;
                    } else {
                        $erreurs[] = "Erreur lors du déplacement du fichier uploadé.";
                    }
                } else {
                    $erreurs[] = "Extension de fichier non autorisée (jpg, jpeg, png acceptés).";
                }
            } else {
                $erreurs[] = "Le fichier photo dépasse la taille maximale autorisée de 1 Mo.";
            }
        } else {
            $erreurs[] = "Erreur lors du téléchargement de la photo.";
        }
    }

    if (empty($erreurs)) {
        $note = (float)$note_saisie;
        if ($edition) {
            $etudiants[$id] = [
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'filiere' => $filiere,
                'note' => $note,
                'photo' => $nom_photo
            ];
            $_SESSION['flash'] = "Étudiant modifié avec succès.";
        } else {
            $nouvel_id = empty($etudiants) ? 1 : max(array_keys($etudiants)) + 1;
            $etudiants[$nouvel_id] = [
                'id' => $nouvel_id,
                'nom' => $nom,
                'prenom' => $prenom,
                'filiere' => $filiere,
                'note' => $note,
                'photo' => $nom_photo
            ];
            $_SESSION['flash'] = "Étudiant ajouté avec succès.";
        }

        sauver_etudiants($etudiants);
        header('Location: liste.php');
        exit;
    }
}

$titre = $edition ? "Modifier un étudiant" : "Ajouter un étudiant";
include 'header.php';
?>

<div class="card">
    <h2><?= $edition ? "Modifier l'étudiant" : "Ajouter un nouvel étudiant" ?></h2>

    <?php if (!empty($erreurs)): ?>
        <div class="error">
            <ul>
                <?php foreach ($erreurs as $err): ?>
                    <li><?= h($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= h($nom) ?>" required>
        </div>
        <div>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= h($prenom) ?>" required>
        </div>
        <div>
            <label for="filiere">Filière :</label>
            <select id="filiere" name="filiere">
                <?php foreach (FILIERES as $f): ?>
                    <option value="<?= $f ?>" <?= $filiere === $f ? 'selected' : '' ?>><?= $f ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="note">Note (/20) :</label>
            <input type="number" step="0.5" min="0" max="20" id="note" name="note" value="<?= h($note) ?>" required>
        </div>
        <div>
            <label for="photo">Photo de profil :</label>
            <input type="file" id="photo" name="photo">
            <?php if ($photo_actuelle): ?>
                <p>Photo actuelle : <img src="uploads/<?= h($photo_actuelle) ?>" class="avatar" alt="photo"></p>
            <?php endif; ?>
        </div>
        <button type="submit"><?= $edition ? "Mettre à jour" : "Ajouter" ?></button>
    </form>
</div>

<?php include 'footer.php'; ?>